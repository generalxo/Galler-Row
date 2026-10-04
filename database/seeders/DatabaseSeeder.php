<?php

namespace Database\Seeders;

use App\Enums\ArtworkType;
use App\Enums\StoreRole;
use App\Models\Artist;
use App\Models\Artwork;
use App\Models\ArtworkVariant;
use App\Models\Collection;
use App\Models\Image;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Store;
use App\Models\StoreApplication;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $customer = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->admin()->create([
            'name' => 'Platform Admin',
            'email' => 'admin@example.com',
        ]);

        $stores = [
            [
                'name' => 'Northlight Gallery',
                'slug' => 'northlight',
                'description' => 'Landscapes and seascapes from painters working along the northern coast, in oil, watercolour and print.',
                'owner' => 'owner@northlight.test',
            ],
            [
                'name' => 'Studio Vermeer',
                'slug' => 'studio-vermeer',
                'description' => 'Quiet interiors and still lifes in the Dutch tradition, plus limited-edition giclée prints.',
                'owner' => 'owner@vermeer.test',
            ],
            [
                // Also reachable on its own custom domain.
                'name' => 'Clay & Kiln',
                'slug' => 'clay-and-kiln',
                'domain' => 'clayandkiln.test',
                'description' => 'Hand-thrown stoneware and small sculpture, fired in a wood kiln and finished one piece at a time.',
                'owner' => 'owner@claykiln.test',
            ],
        ];

        foreach ($stores as $data) {
            $store = Store::factory()->create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'domain' => $data['domain'] ?? null,
                'description' => $data['description'],
            ]);

            $store->members()->attach(
                User::factory()->create(['email' => $data['owner']]),
                ['role' => StoreRole::Owner],
            );
            $store->members()->attach(User::factory()->create(), ['role' => StoreRole::Staff]);

            $this->seedCatalogue($store, $customer);
        }

        // Waiting for a platform admin to review.
        StoreApplication::factory()->create([
            'name' => 'Ines Albrecht',
            'email' => 'ines@example.com',
            'store_name' => 'Paper Moon Press',
            'subdomain' => 'paper-moon-press',
            'message' => 'Risograph and letterpress prints in small editions, mostly botanical studies.',
        ]);
        StoreApplication::factory()->create([
            'name' => 'Tomás Reyes',
            'email' => 'tomas@example.com',
            'store_name' => 'Reyes Glassworks',
            'subdomain' => 'reyes-glassworks',
            'website' => 'https://example.com/reyes',
            'message' => 'Blown glass vessels and a few large installation pieces. All originals.',
        ]);
    }

    protected function seedCatalogue(Store $store, User $customer): void
    {
        $artists = Artist::factory()->count(3)->for($store)->create();
        $collections = Collection::factory()->count(2)->for($store)->create();
        $collections->first()?->update(['is_featured' => true]);

        $types = ArtworkType::cases();

        foreach (range(1, 7) as $i) {
            $type = $types[$i % count($types)];

            $factory = Artwork::factory()
                ->ofType($type)
                ->byArtist($artists->random())
                ->published();

            // Prints and photographs come as limited editions; one is down to its last print.
            if (in_array($type, [ArtworkType::Print, ArtworkType::Photograph], true)) {
                $factory = $factory->edition(25, $i === 1 ? 1 : null);
            }

            $artwork = $factory->create();

            Image::factory()->count(2)->sequence(['position' => 0], ['position' => 1])->create([
                'imageable_type' => 'artwork',
                'imageable_id' => $artwork->id,
            ]);

            $artwork->collections()->attach($collections->random(), ['position' => $i]);

            if ($type === ArtworkType::Print) {
                ArtworkVariant::factory()->count(2)
                    ->sequence(['name' => 'A3, unframed', 'position' => 0], ['name' => 'A3, framed', 'position' => 1])
                    ->create(['artwork_id' => $artwork->id]);
            }
        }

        Artwork::factory()->for($store)->byArtist($artists->first())->create();

        $sold = Artwork::factory()->painting()->byArtist($artists->first())->sold()->create();

        $order = Order::factory()->for($store)->for($customer)->create([
            'subtotal' => $sold->price,
            'total' => $sold->price + 1500,
        ]);

        OrderItem::factory()->for($order)->forArtwork($sold)->create();
    }
}
