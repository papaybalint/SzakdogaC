<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\BorrowingMedia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LibraryTest extends TestCase
{
    use RefreshDatabase;

    protected function createUser($role = 'user')
    {
        return User::factory()->create([
            'role' => $role,
            'password' => bcrypt('password123')
        ]);
    }




    public function test_login_with_valid_and_invalid_credentials()
    {
        $user = $this->createUser();


        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123'
        ]);
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);


        $this->post('/logout');

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password'
        ]);
        $response->assertSessionHasErrors();
        $this->assertGuest();
    }




    public function test_authenticated_user_can_add_item()
    {
        $admin = $this->createUser('admin');
        $this->actingAs($admin);

        $response = $this->post('/items', [
            'title' => 'Teszt könyv',
            'author' => 'Teszt Szerző',
            'inventory_number' => 'INV0001',
            'barcode' => 'BAR123',
            'isbn' => '9783161484100',
            'year_of_purchasing' => 2022,
            'published_year' => 2020,
            'supplier' => 'Teszt Szállító',
            'categories_id' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('items', [
            'title' => 'Teszt könyv'
        ]);
    }



    public function test_authenticated_user_can_borrow_item()
    {
        $user = $this->createUser();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/borrowings', [
            'users_id' => $user->id,
            'items_id' => $item->id,
            'borrowed_at' => now(),
            'due_date' => now()->addDays(7),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('borrowing_media', [
            'users_id' => $user->id,
            'items_id' => $item->id,
        ]);
    }


     
    public function test_authenticated_admin_can_list_borrowings()
    {
        $admin = $this->createUser('admin');
        $this->actingAs($admin);

        $response = $this->get('/borrowed_media');
        $response->assertStatus(200);
        $response->assertSee('BorrowedMedia');
    }




    public function test_user_cannot_access_admin_user_list()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $response = $this->get('/users');

        $response->assertStatus(403);
    }
}
