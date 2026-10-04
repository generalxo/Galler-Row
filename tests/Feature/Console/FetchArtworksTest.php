<?php

namespace Tests\Feature\Console;

use App\Console\Commands\FetchArtworks;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FetchArtworksTest extends TestCase
{
    protected string $path;

    protected function setUp(): void
    {
        parent::setUp();

        $this->path = storage_path('framework/testing/artworks-'.uniqid());
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->path);

        parent::tearDown();
    }

    public function test_it_writes_images_and_a_manifest_for_public_domain_works_only(): void
    {
        $notPublicDomain = FetchArtworks::CATALOGUE['northlight'][0]['ids'][0];

        Http::fake([
            'api.artic.edu/*' => function (Request $request) use ($notPublicDomain) {
                parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

                $data = array_map(fn (string $id): array => [
                    'id' => (int) $id,
                    'title' => "Work {$id}",
                    'artist_title' => 'Claude Monet',
                    'date_end' => 1889,
                    'medium_display' => 'Oil on canvas',
                    'dimensions_detail' => [['width' => 93, 'height' => 65, 'depth' => null]],
                    'image_id' => "image-{$id}",
                    'is_public_domain' => (int) $id !== $notPublicDomain,
                ], explode(',', $query['ids']));

                return Http::response(['data' => $data]);
            },
            'www.artic.edu/iiif/*' => Http::response('jpeg-bytes'),
        ]);

        $this->artisan('artworks:fetch', ['--path' => $this->path, '--delay' => 0])
            ->expectsOutputToContain("Skipped AIC {$notPublicDomain}")
            ->assertSuccessful();

        $manifest = json_decode(File::get("{$this->path}/manifest.json"), true);
        $expected = collect(FetchArtworks::CATALOGUE)->flatten(1)->sum(fn (array $group): int => count($group['ids'])) - 1;

        $this->assertCount($expected, $manifest);
        $this->assertNotContains($notPublicDomain, array_column($manifest, 'source_id'));

        $first = $manifest[0];
        $this->assertSame('northlight', $first['store']);
        $this->assertSame('Mountains and rivers', $first['collection']);
        $this->assertSame('painting', $first['type']);
        $this->assertSame(93, $first['width_cm']);
        $this->assertFileExists("{$this->path}/{$first['image']}");

        Http::assertSent(fn (Request $request): bool => $request->hasHeader('AIC-User-Agent'));
    }
}
