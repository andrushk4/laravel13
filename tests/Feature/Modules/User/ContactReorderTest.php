<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\User;

use App\Modules\User\Models\User;
use App\Modules\User\Models\UserContact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ContactReorderTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_reorders_user_contacts(): void
    {
        $user = User::factory()->create();

        $contact1 = UserContact::factory()->create(['user_id' => $user->id, 'order' => 1]);
        $contact2 = UserContact::factory()->create(['user_id' => $user->id, 'order' => 2]);
        $contact3 = UserContact::factory()->create(['user_id' => $user->id, 'order' => 3]);

        $response = $this->patchJson("/api/users/{$user->id}/contacts/reorder", [
            'order' => [
                $contact3->id,
                $contact1->id,
                $contact2->id,
            ]
        ]);

        $response->assertSuccessful();

        $this->assertEquals(1, $contact3->refresh()->order);
        $this->assertEquals(2, $contact1->refresh()->order);
        $this->assertEquals(3, $contact2->refresh()->order);
    }
}
