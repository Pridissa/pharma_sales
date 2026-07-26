<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductBatch;
use App\Models\CashSession;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class PharmaFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_fefo_stock_deduction(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $cat = Category::create(['name' => 'Test Cat', 'slug' => 'test-cat']);
        
        $product = Product::create([
            'name' => 'Paracétamol Test',
            'code_barre' => '123456',
            'category_id' => $cat->id,
            'price' => 500,
            'purchase_price' => 300,
            'stock_quantity' => 30,
            'min_stock_alert' => 5,
        ]);

        // Create 2 batches: Lot A (expires in 10 days, qty 10), Lot B (expires in 60 days, qty 20)
        $batchA = ProductBatch::create([
            'product_id' => $product->id,
            'batch_number' => 'LOT-A',
            'expiration_date' => Carbon::now()->addDays(10),
            'quantity' => 10,
            'purchase_price' => 300,
            'is_active' => true,
        ]);

        $batchB = ProductBatch::create([
            'product_id' => $product->id,
            'batch_number' => 'LOT-B',
            'expiration_date' => Carbon::now()->addDays(60),
            'quantity' => 20,
            'purchase_price' => 300,
            'is_active' => true,
        ]);

        // Deduct 15 units -> should deplete Lot A (10) and take 5 from Lot B (20 -> 15)
        $product->deductFefoStock(15, $user->id, 'FAC-TEST-001');

        $batchA->refresh();
        $batchB->refresh();
        $product->refresh();

        $this->assertEquals(0, $batchA->quantity);
        $this->assertEquals(15, $batchB->quantity);
        $this->assertEquals(15, $product->stock_quantity);

        // Verify stock movements logged
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'product_batch_id' => $batchA->id,
            'quantity' => -10,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'product_batch_id' => $batchB->id,
            'quantity' => -5,
        ]);
    }

    public function test_cash_session_opening_and_closing(): void
    {
        $user = User::factory()->create(['role' => 'caissier']);

        // Open Cash session
        $session = CashSession::create([
            'user_id' => $user->id,
            'opened_at' => Carbon::now(),
            'opening_balance' => 20000,
            'status' => 'open',
        ]);

        $this->assertTrue($session->isOpen());
        $this->assertEquals($session->id, CashSession::activeForUser($user->id)->id);

        // Create a cash sale linked to session
        Sale::create([
            'invoice_number' => 'FAC-CASH-001',
            'user_id' => $user->id,
            'cash_session_id' => $session->id,
            'subtotal' => 5000,
            'total_amount' => 5000,
            'amount_paid' => 5000,
            'change_amount' => 0,
            'payment_method' => 'Espèces',
        ]);

        // Close session with 25000 actual cash (20000 opening + 5000 sale) -> zero difference
        $expected = 25000;
        $session->update([
            'closed_at' => Carbon::now(),
            'closing_balance_expected' => $expected,
            'closing_balance_actual' => 25000,
            'difference' => 0,
            'status' => 'closed',
        ]);

        $this->assertFalse($session->fresh()->isOpen());
        $this->assertEquals(0, $session->fresh()->difference);
    }
}
