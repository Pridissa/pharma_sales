<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_all_routes_without_errors(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Category::create(['name' => 'Général', 'slug' => 'general']);

        $routes = [
            '/',
            '/sales-history',
            '/requisitions',
            '/cash-register',
            '/dashboard',
            '/products',
            '/categories',
            '/users',
            '/reports',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($admin)->get($route);
            $response->assertStatus(200);
        }
    }
}
