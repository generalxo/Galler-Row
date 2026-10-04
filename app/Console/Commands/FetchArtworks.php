<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

/**
 * Downloads the demo catalogue: public-domain (CC0) artworks from the Art
 * Institute of Chicago, hand-picked per store. Images and a manifest are
 * written to database/seeders/artworks and committed, so seeding is offline.
 *
 * Only CC0 fields are used; AIC's `description` field is CC-BY and skipped.
 */
#[Signature('artworks:fetch
    {--path= : Output directory (default: database/seeders/artworks)}
    {--discover= : Search AIC and list public-domain candidates instead of fetching}
    {--type=Painting : AIC artwork type to filter --discover by}
    {--delay=250 : Milliseconds to wait between image downloads}')]
#[Description('Download the curated public-domain demo catalogue from the Art Institute of Chicago')]
class FetchArtworks extends Command
{
    protected const string API = 'https://api.artic.edu/api/v1/artworks';

    protected const string IIIF = 'https://www.artic.edu/iiif/2';

    protected const string FIELDS = 'id,title,artist_title,date_end,medium_display,dimensions_detail,image_id,is_public_domain';

    /**
     * Store slug => groups. Each group becomes a collection; `type` is our
     * ArtworkType. `digital` re-sells images already in the store as downloads.
     *
     * @var array<string, list<array{collection: string, type: string, ids: list<int>}>>
     */
    public const array CATALOGUE = [
        'northlight' => [
            ['collection' => 'Mountains and rivers', 'type' => 'painting', 'ids' => [81546, 76571, 39554, 16564, 73054, 146701, 874, 109693]],
            ['collection' => 'Coastlines', 'type' => 'painting', 'ids' => [153993, 59927, 14598, 8971, 152747, 100489, 81535, 27764]],
            ['collection' => 'Harbour etchings', 'type' => 'print', 'ids' => [117443, 158305, 24253, 41695, 133358, 100627]],
            ['collection' => 'Sea photographs', 'type' => 'photograph', 'ids' => [126485, 126479, 38287, 47866]],
        ],
        'studio-vermeer' => [
            ['collection' => 'Still lifes', 'type' => 'painting', 'ids' => [66042, 16549, 120154, 100829, 64957, 75507, 72180, 62450, 21682]],
            ['collection' => 'Quiet rooms', 'type' => 'painting', 'ids' => [14591, 81512, 62460, 16398, 81548, 28868, 64979]],
            ['collection' => 'Prints', 'type' => 'print', 'ids' => [13508, 26693, 22732, 22740, 36840, 9385]],
            ['collection' => 'Digital downloads', 'type' => 'digital', 'ids' => [64957, 100829]],
        ],
        'clay-and-kiln' => [
            ['collection' => 'Vessels', 'type' => 'sculpture', 'ids' => [21502, 18722, 246812, 145206, 145685, 6488, 67654, 202468, 195530, 88105, 143586, 71011, 159880, 180198, 158473]],
            ['collection' => 'Small sculpture', 'type' => 'sculpture', 'ids' => [15287, 145840, 48741, 70986, 70989, 190409, 89145, 89236, 18901]],
        ],
    ];

    public function handle(): int
    {
        if ($query = $this->option('discover')) {
            return $this->discover((string) $query, (string) $this->option('type'));
        }

        $path = rtrim((string) ($this->option('path') ?: database_path('seeders/artworks')), '/');
        $manifest = [];

        foreach (self::CATALOGUE as $store => $groups) {
            $ids = array_values(array_unique(array_merge(...array_column($groups, 'ids'))));
            $records = $this->fetchRecords($ids);

            foreach ($groups as $group) {
                foreach ($group['ids'] as $id) {
                    $record = $records[$id] ?? null;

                    if ($record === null || ! ($record['is_public_domain'] ?? false) || empty($record['image_id'])) {
                        $this->warn("Skipped AIC {$id}: missing, not public domain, or no image.");

                        continue;
                    }

                    $image = "{$store}/aic-{$id}.jpg";

                    if (! $this->downloadImage($record['image_id'], "{$path}/{$image}")) {
                        $this->warn("Skipped AIC {$id}: image download failed.");

                        continue;
                    }

                    $manifest[] = $this->entry($store, $group, $record, $image);
                }
            }

            $this->info("{$store}: done.");
        }

        File::ensureDirectoryExists($path);
        File::put("{$path}/manifest.json", json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n");

        $this->info(count($manifest)." artworks written to {$path}/manifest.json");

        return self::SUCCESS;
    }

    /**
     * @param  list<int>  $ids
     * @return array<int, array<string, mixed>>
     */
    protected function fetchRecords(array $ids): array
    {
        $records = [];

        foreach (array_chunk($ids, 50) as $chunk) {
            $response = $this->http()->get(self::API, [
                'ids' => implode(',', $chunk),
                'fields' => self::FIELDS,
                'limit' => count($chunk),
            ])->throw();

            foreach ($response->json('data', []) as $record) {
                $records[$record['id']] = $record;
            }
        }

        return $records;
    }

    protected function downloadImage(string $imageId, string $target): bool
    {
        if (File::exists($target)) {
            return true;
        }

        $response = $this->http()->get(self::IIIF."/{$imageId}/full/600,/0/default.jpg");

        if (! $response->successful()) {
            return false;
        }

        File::ensureDirectoryExists(dirname($target));
        File::put($target, $response->body());

        // Be polite to the IIIF server.
        usleep(max(0, (int) $this->option('delay')) * 1000);

        return true;
    }

    /**
     * @param  array{collection: string, type: string, ids: list<int>}  $group
     * @param  array<string, mixed>  $record
     * @return array<string, mixed>
     */
    protected function entry(string $store, array $group, array $record, string $image): array
    {
        $dimensions = $record['dimensions_detail'][0] ?? [];

        return [
            'store' => $store,
            'collection' => $group['collection'],
            'type' => $group['type'],
            'source_id' => $record['id'],
            'title' => $record['title'],
            'artist' => $record['artist_title'] ?? null,
            'year' => $record['date_end'] ?? null,
            'medium' => $record['medium_display'] ?? null,
            'width_cm' => $dimensions['width'] ?? null,
            'height_cm' => $dimensions['height'] ?? null,
            'depth_cm' => $dimensions['depth'] ?? null,
            'image' => $image,
        ];
    }

    protected function discover(string $query, string $type): int
    {
        $response = $this->http()->get(self::API.'/search', [
            'q' => $query,
            'query' => ['bool' => ['must' => [
                ['term' => ['is_public_domain' => true]],
                ['term' => ['artwork_type_title.keyword' => $type]],
                ['exists' => ['field' => 'image_id']],
            ]]],
            'fields' => 'id,title,artist_title,date_end,medium_display',
            'limit' => 25,
        ])->throw();

        $this->table(
            ['id', 'title', 'artist', 'year', 'medium'],
            $response->collect('data')->map(fn (array $r): array => [
                $r['id'],
                mb_strimwidth((string) $r['title'], 0, 50, '…'),
                $r['artist_title'] ?? '',
                $r['date_end'] ?? '',
                mb_strimwidth((string) ($r['medium_display'] ?? ''), 0, 30, '…'),
            ])->all(),
        );

        return self::SUCCESS;
    }

    protected function http(): PendingRequest
    {
        $agent = 'gallery-row-demo ('.config('app.url').')';

        return Http::withUserAgent($agent)
            ->withHeaders(['AIC-User-Agent' => $agent])
            ->timeout(30)
            ->retry(2, 1000);
    }
}
