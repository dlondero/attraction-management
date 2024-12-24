<?php

namespace Tests\Feature;

use App\Models\Attraction;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttractionTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // Create and assign roles to users
        $this->admin = User::factory()->create();
        $this->admin->roles()->attach($adminRole);

        $this->user = User::factory()->create();
        $this->user->roles()->attach($userRole);
    }

    public function test_can_list_attractions(): void
    {
        Attraction::factory()->count(5)->create();

        $response = $this->actingAs($this->user)->get(route('attractions.index'));

        $response->assertStatus(200);
        $response->assertViewHas('attractions');
    }

    public function test_admin_can_create_attraction(): void
    {
        $attractionData = Attraction::factory()->make()->toArray();

        $response = $this->actingAs($this->admin)->post(route('attractions.store'), $attractionData);

        $response->assertRedirect(route('attractions.show', Attraction::first()));
        $this->assertDatabaseHas('attractions', $attractionData);
    }

    public function test_user_cannot_create_attraction(): void
    {
        $attractionData = Attraction::factory()->make()->toArray();

        $response = $this->actingAs($this->user)->post(route('attractions.store'), $attractionData);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('attractions', $attractionData);
    }

    public function test_admin_can_update_attraction(): void
    {
        $attraction = Attraction::factory()->create();
        $updatedData = [
            'name' => 'Updated Attraction',
            'description' => 'Updated description',
            'location' => 'Updated location',
            'price' => 100,
        ];

        $response = $this->actingAs($this->admin)->put(route('attractions.update', $attraction), $updatedData);

        $response->assertRedirect(route('attractions.show', $attraction));
        $this->assertDatabaseHas('attractions', $updatedData);
    }

    public function test_user_cannot_update_attraction(): void
    {
        $attraction = Attraction::factory()->create();
        $updatedData = [
            'name' => 'Updated Attraction',
            'description' => 'Updated description',
            'location' => 'Updated location',
            'price' => 100,
        ];

        $response = $this->actingAs($this->user)->put(route('attractions.update', $attraction), $updatedData);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('attractions', $updatedData);
    }

    public function test_admin_can_delete_attraction(): void
    {
        $attraction = Attraction::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('attractions.destroy', $attraction));

        $response->assertRedirect(route('attractions.index'));
        $this->assertDatabaseMissing('attractions', ['id' => $attraction->id]);
    }

    public function test_user_cannot_delete_attraction(): void
    {
        $attraction = Attraction::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('attractions.destroy', $attraction));

        $response->assertStatus(403);
        $this->assertDatabaseHas('attractions', ['id' => $attraction->id]);
    }
}
