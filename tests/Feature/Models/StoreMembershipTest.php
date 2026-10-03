<?php

namespace Tests\Feature\Models;

use App\Enums\StoreRole;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreMembershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_have_a_role_per_store(): void
    {
        $user = User::factory()->create();
        $owned = Store::factory()->ownedBy($user)->create();
        $staffed = Store::factory()->create();
        $staffed->members()->attach($user, ['role' => StoreRole::Staff]);
        $unrelated = Store::factory()->create();

        $this->assertSame(StoreRole::Owner, $user->roleIn($owned));
        $this->assertSame(StoreRole::Staff, $user->roleIn($staffed));
        $this->assertNull($user->roleIn($unrelated));

        $this->assertTrue($user->ownsStore($owned));
        $this->assertFalse($user->ownsStore($staffed));
        $this->assertTrue($user->isMemberOf($staffed));
        $this->assertFalse($user->isMemberOf($unrelated));
    }

    public function test_owners_relation_excludes_staff(): void
    {
        $owner = User::factory()->create();
        $store = Store::factory()->ownedBy($owner)->create();
        $store->members()->attach(User::factory()->create(), ['role' => StoreRole::Staff]);

        $this->assertSame([$owner->id], $store->owners()->pluck('users.id')->all());
        $this->assertSame(StoreRole::Owner, $store->owners()->firstOrFail()->membership->role);
    }

    public function test_user_can_only_join_a_store_once(): void
    {
        $user = User::factory()->create();
        $store = Store::factory()->ownedBy($user)->create();

        $this->expectException(UniqueConstraintViolationException::class);

        $store->members()->attach($user, ['role' => StoreRole::Staff]);
    }

    public function test_platform_admin_flag(): void
    {
        $this->assertTrue(User::factory()->admin()->create()->isAdmin());
        $this->assertFalse(User::factory()->create()->isAdmin());
    }
}
