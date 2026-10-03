<?php

namespace Tests\Feature\Models;

use App\Models\Artwork;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_item_keeps_its_snapshot_when_artwork_is_deleted(): void
    {
        $artwork = Artwork::factory()->create(['title' => 'Blue Hour', 'price' => 120000]);
        $item = OrderItem::factory()->forArtwork($artwork)->create();

        $artwork->forceDelete();
        $item->refresh();

        $this->assertNull($item->artwork_id);
        $this->assertSame('Blue Hour', $item->title);
        $this->assertSame(120000, $item->unit_price);
        $this->assertSame(120000, $item->line_total);
    }

    public function test_order_survives_customer_deletion(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->for($user)->create();

        $user->delete();

        $this->assertNull($order->fresh()?->user_id);
    }
}
