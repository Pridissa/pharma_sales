<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductBatch;
use App\Models\CashSession;
use App\Models\Requisition;
use App\Models\Sale;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class LivewireComponentsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Category $category;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->category = Category::create(['name' => 'Antalgiques', 'slug' => 'antalgiques']);
        
        $this->product = Product::create([
            'name' => 'Paracétamol 500mg',
            'code_barre' => '3400938472910',
            'category_id' => $this->category->id,
            'price' => 2000,
            'purchase_price' => 1200,
            'stock_quantity' => 50,
            'min_stock_alert' => 10,
            'expiration_date' => Carbon::now()->addMonths(6)->format('Y-m-d'),
        ]);

        ProductBatch::create([
            'product_id' => $this->product->id,
            'batch_number' => 'LOT-2026-001',
            'expiration_date' => Carbon::now()->addMonths(6)->format('Y-m-d'),
            'quantity' => 50,
            'purchase_price' => 1200,
            'is_active' => true,
        ]);
    }

    public function test_dashboard_renders_properly(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(\App\Livewire\Dashboard::class)
            ->assertStatus(200)
            ->assertSee('BITA PHARMA')
            ->assertSee('Paracétamol 500mg');
    }

    public function test_pos_component_allows_adding_to_cart_and_checkout(): void
    {
        $this->actingAs($this->admin);

        // Open cash session
        $session = CashSession::create([
            'user_id' => $this->admin->id,
            'opened_at' => Carbon::now(),
            'opening_balance' => 10000,
            'status' => 'open',
        ]);

        Livewire::test(\App\Livewire\Pos::class)
            ->call('addToCart', $this->product->id)
            ->assertSet('cart.1.qty', 1)
            ->set('amountPaid', 5000)
            ->call('completeSale')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('sales', [
            'user_id' => $this->admin->id,
            'total_amount' => 2000,
        ]);
    }

    public function test_product_management_crud(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(\App\Livewire\Products::class)
            ->set('name', 'Ibuprofène 400mg')
            ->set('category_id', $this->category->id)
            ->set('price', 3500)
            ->set('purchase_price', 2000)
            ->set('stock_quantity', 100)
            ->set('min_stock_alert', 15)
            ->call('saveProduct')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'name' => 'Ibuprofène 400mg',
            'price' => 3500,
        ]);
    }

    public function test_category_management_crud(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(\App\Livewire\Categories::class)
            ->set('name', 'Antibiotiques')
            ->set('description', 'Famille des pénicillines et céphalosporines')
            ->call('saveCategory')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'name' => 'Antibiotiques',
        ]);
    }

    public function test_requisition_creation_and_fulfillment(): void
    {
        $this->actingAs($this->admin);

        // Custom customer requisition for non-existent product
        Livewire::test(\App\Livewire\Requisitions::class)
            ->set('customReqProductName', 'Insuline Mixtard')
            ->set('customReqQuantity', 5)
            ->set('customReqNotes', 'Urgent')
            ->call('saveCustomRequisition')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('requisitions', [
            'product_name' => 'Insuline Mixtard',
            'requested_quantity' => 5,
        ]);
    }

    public function test_user_management_crud(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(\App\Livewire\Users::class)
            ->set('name', 'Jean Caissier')
            ->set('email', 'jean@bita.com')
            ->set('role', 'caissier')
            ->set('password', 'password123')
            ->call('saveUser')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'email' => 'jean@bita.com',
            'role' => 'caissier',
        ]);
    }
}
