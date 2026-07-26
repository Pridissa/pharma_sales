<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Product;
use App\Models\Category;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\CashSession;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Pos extends Component
{
    public string $search = '';
    public ?int $selectedCategory = null;
    
    // Cart state: [ product_id => [id, name, price, qty, subtotal, requires_prescription, stock_quantity, code_barre] ]
    public array $cart = [];
    
    public mixed $amountPaid = 0.0;
    public string $paymentMethod = 'Espèces';
    public string $patientName = '';
    public string $doctorName = '';
    public string $notes = '';

    public bool $showReceiptModal = false;
    public ?Sale $lastSale = null;
    public string $errorMessage = '';
    public string $successMessage = '';

    // Cash Session Management State
    public bool $showOpenSessionModal = false;
    public bool $showCloseSessionModal = false;
    public float $openingBalance = 50000.0;
    public string $openingNotes = '';
    public float $closingActualCash = 0.0;
    public string $closingNotes = '';
    public ?CashSession $activeSession = null;

    public function mount(): void
    {
        $this->checkActiveSession();
    }

    public function checkActiveSession(): void
    {
        if (auth()->check()) {
            $this->activeSession = CashSession::activeForUser(auth()->id());
        }
    }

    public function openSessionModal(): void
    {
        $this->openingBalance = 50000.0;
        $this->openingNotes = 'Ouverture normale de la caisse';
        $this->showOpenSessionModal = true;
    }

    public function saveOpenSession(): void
    {
        if (!auth()->check()) return;

        $session = CashSession::create([
            'user_id' => auth()->id(),
            'opened_at' => Carbon::now(),
            'opening_balance' => max(0, (float)$this->openingBalance),
            'status' => 'open',
            'opening_notes' => $this->openingNotes ?: 'Ouverture de session caisse',
        ]);

        AuditLog::log('session_opened', 'CashSession', $session->id, [
            'opening_balance' => $session->opening_balance,
        ]);

        $this->activeSession = $session;
        $this->showOpenSessionModal = false;
        $msg = "Session de caisse ouverte avec un fond initial de " . number_format($session->opening_balance, 0, ',', ' ') . " FC.";
        $this->dispatch('toast', message: $msg, type: 'success');
    }

    public function openCloseSessionModal(): void
    {
        $this->checkActiveSession();
        if (!$this->activeSession) {
            $this->errorMessage = 'Aucune session de caisse n\'est actuellement ouverte.';
            return;
        }

        // Expected cash balance calculation = opening_balance + sum of cash sales during session
        $cashSalesSum = Sale::where('cash_session_id', $this->activeSession->id)
            ->where('payment_method', 'Espèces')
            ->sum('total_amount');

        $expectedTotal = $this->activeSession->opening_balance + $cashSalesSum;
        $this->closingActualCash = (float)$expectedTotal;
        $this->closingNotes = 'Clôture normale de caisse Z';
        $this->showCloseSessionModal = true;
    }

    public function saveCloseSession(): void
    {
        if (!$this->activeSession) return;

        $cashSalesSum = Sale::where('cash_session_id', $this->activeSession->id)
            ->where('payment_method', 'Espèces')
            ->sum('total_amount');

        $expectedTotal = $this->activeSession->opening_balance + $cashSalesSum;
        $actual = (float)$this->closingActualCash;
        $difference = $actual - $expectedTotal;

        $this->activeSession->update([
            'closed_at' => Carbon::now(),
            'closing_balance_expected' => $expectedTotal,
            'closing_balance_actual' => $actual,
            'difference' => $difference,
            'status' => 'closed',
            'closing_notes' => $this->closingNotes ?: 'Clôture de session Z',
        ]);

        AuditLog::log('session_closed', 'CashSession', $this->activeSession->id, [
            'expected' => $expectedTotal,
            'actual' => $actual,
            'difference' => $difference,
        ]);

        $sessionRef = $this->activeSession->id;
        $this->activeSession = null;
        $this->showCloseSessionModal = false;

        $statusMsg = $difference === 0.00 
            ? "Caisse clôturée sans écart !" 
            : ($difference > 0 ? "Caisse clôturée avec un excédent de +" . number_format($difference, 0, ',', ' ') . " FC." : "Caisse clôturée avec un déficit de " . number_format($difference, 0, ',', ' ') . " FC.");

        $this->dispatch('toast', message: "Clôture de Caisse Z #{$sessionRef} enregistrée. {$statusMsg}", type: 'success');
    }

    public function getNumericAmountPaid(): float
    {
        if (is_null($this->amountPaid) || $this->amountPaid === '') {
            return 0.0;
        }
        return (float) $this->amountPaid;
    }

    public function addToCart(int $productId): void
    {
        $this->errorMessage = '';
        $product = Product::find($productId);

        if (!$product) {
            $this->errorMessage = 'Produit introuvable.';
            return;
        }

        if ($product->stock_quantity <= 0) {
            $this->errorMessage = "Stock épuisé pour {$product->name}.";
            return;
        }

        if (isset($this->cart[$productId])) {
            $currentQty = $this->cart[$productId]['qty'];
            if ($currentQty + 1 > $product->stock_quantity) {
                $this->errorMessage = "Stock insuffisant (Max disponible: {$product->stock_quantity}).";
                return;
            }
            $this->cart[$productId]['qty'] += 1;
            $this->cart[$productId]['subtotal'] = $this->cart[$productId]['qty'] * $this->cart[$productId]['price'];
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'dci' => $product->dci,
                'code_barre' => $product->code_barre,
                'price' => (float)$product->price,
                'qty' => 1,
                'subtotal' => (float)$product->price,
                'requires_prescription' => $product->requires_prescription,
                'stock_quantity' => $product->stock_quantity,
                'dosage_unit' => $product->dosage_unit,
            ];
        }

        // Auto set amount paid to total if not modified yet
        if ($this->amountPaid == 0) {
            $this->amountPaid = $this->calculateTotal();
        }
    }

    public function updateQuantity(int $productId, int $qty): void
    {
        $this->errorMessage = '';
        if (!isset($this->cart[$productId])) return;

        if ($qty <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        $product = Product::find($productId);
        if ($product && $qty > $product->stock_quantity) {
            $this->errorMessage = "Quantité demandée ({$qty}) dépasse le stock disponible ({$product->stock_quantity}).";
            return;
        }

        $this->cart[$productId]['qty'] = $qty;
        $this->cart[$productId]['subtotal'] = $qty * $this->cart[$productId]['price'];

        if ($this->getNumericAmountPaid() < $this->calculateTotal()) {
            $this->amountPaid = $this->calculateTotal();
        }
    }

    public function removeFromCart(int $productId): void
    {
        unset($this->cart[$productId]);
        if (empty($this->cart)) {
            $this->amountPaid = 0;
        }
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->amountPaid = 0;
        $this->patientName = '';
        $this->doctorName = '';
        $this->errorMessage = '';
        $this->successMessage = '';
    }

    public function calculateTotal(): float
    {
        return array_reduce($this->cart, function ($carry, $item) {
            return $carry + $item['subtotal'];
        }, 0.0);
    }

    #[Computed]
    public function changeAmount(): float
    {
        $total = $this->calculateTotal();
        return max(0, $this->getNumericAmountPaid() - $total);
    }

    #[Computed]
    public function hasPrescriptionItem(): bool
    {
        foreach ($this->cart as $item) {
            if ($item['requires_prescription']) return true;
        }
        return false;
    }

    public function setAmountPaid($amount): void
    {
        $this->amountPaid = max(0, (float)$amount);
    }

    public function addAmountPaid($amount): void
    {
        $this->amountPaid = $this->getNumericAmountPaid() + (float)$amount;
    }

    public function updatedAmountPaid($value): void
    {
        if ($value === '' || is_null($value)) {
            return;
        }
        if ((float)$value < 0) {
            $this->amountPaid = 0;
        }
    }

    public function completeSale(): void
    {
        $this->errorMessage = '';

        if (empty($this->cart)) {
            $this->errorMessage = 'Le panier est vide.';
            return;
        }

        // Check if cash session is required and active
        $this->checkActiveSession();
        if (!$this->activeSession) {
            $this->openSessionModal();
            $this->errorMessage = 'Veuillez ouvrir une session de caisse avant de valider des ventes.';
            return;
        }

        // Validate mandatory doctor name if cart has prescription products
        if ($this->hasPrescriptionItem() && empty(trim($this->doctorName))) {
            $this->errorMessage = 'Le nom du médecin prescripteur est obligatoire car le panier contient un produit sur ordonnance.';
            return;
        }

        // Ensure valid payment method
        if (!in_array($this->paymentMethod, ['Espèces', 'Mobile Money'])) {
            $this->paymentMethod = 'Espèces';
        }

        $total = $this->calculateTotal();
        $numericAmountPaid = $this->getNumericAmountPaid();

        if ($numericAmountPaid < $total) {
            $this->errorMessage = 'Le montant versé est inférieur au total à payer.';
            return;
        }

        DB::beginTransaction();

        try {
            // Verify stock again before saving
            foreach ($this->cart as $item) {
                $product = Product::lockForUpdate()->find($item['id']);
                if (!$product || $product->stock_quantity < $item['qty']) {
                    throw new \Exception("Stock insuffisant pour le produit {$item['name']}.");
                }
            }

            // Create Invoice Number
            $todayCount = Sale::whereDate('created_at', Carbon::today())->count() + 1;
            $invoiceNumber = 'FAC-' . Carbon::now()->format('Ymd') . '-' . str_pad($todayCount, 4, '0', STR_PAD_LEFT);

            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'user_id' => auth()->id(),
                'cash_session_id' => $this->activeSession?->id,
                'subtotal' => $total,
                'tax_amount' => 0.00,
                'discount_amount' => 0.00,
                'total_amount' => $total,
                'amount_paid' => $numericAmountPaid,
                'change_amount' => $numericAmountPaid - $total,
                'payment_method' => $this->paymentMethod,
                'status' => 'completed',
                'patient_name' => $this->patientName ?: null,
                'doctor_name' => $this->doctorName ?: null,
                'notes' => $this->notes ?: null,
            ]);

            foreach ($this->cart as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'unit_price' => $item['price'],
                    'quantity' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                ]);

                // FEFO Deduction logic from active product batches
                $product = Product::find($item['id']);
                $product->deductFefoStock($item['qty'], auth()->id(), $invoiceNumber);
            }

            AuditLog::log('sale_created', 'Sale', $sale->id, [
                'invoice_number' => $invoiceNumber,
                'total_amount' => $total,
                'payment_method' => $this->paymentMethod,
            ]);

            DB::commit();

            $this->lastSale = $sale->load(['items', 'user']);
            $this->showReceiptModal = true;
            $this->clearCart();
            $this->successMessage = "Vente {$invoiceNumber} enregistrée avec succès !";
            $this->dispatch('toast', message: "Vente {$invoiceNumber} enregistrée avec succès !", type: 'success');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorMessage = 'Erreur lors de la validation de la vente : ' . $e->getMessage();
            $this->dispatch('toast', message: $this->errorMessage, type: 'error');
        }
    }

    // Custom Requisition state
    public bool $showCustomReqModal = false;
    public string $customReqProductName = '';
    public int $customReqQuantity = 1;
    public string $customReqNotes = '';

    #[\Livewire\Attributes\On('open-global-req-modal')]
    public function openCustomReqModal(string $defaultName = ''): void
    {
        $this->customReqProductName = $defaultName ?: $this->search;
        $this->customReqQuantity = 1;
        $this->customReqNotes = 'Demande d\'un client pour produit indisponible';
        $this->showCustomReqModal = true;
    }

    public function saveCustomRequisition(): void
    {
        if (empty(trim($this->customReqProductName))) {
            $this->errorMessage = 'Veuillez saisir le nom du produit demandé.';
            return;
        }

        \App\Models\Requisition::create([
            'product_id' => null,
            'product_name' => trim($this->customReqProductName),
            'requested_quantity' => max(1, $this->customReqQuantity),
            'user_id' => auth()->id(),
            'status' => 'en_attente',
            'type' => 'demande_client',
            'notes' => $this->customReqNotes ?: 'Demande client enregistrée depuis l\'Espace Caisse',
        ]);

        $msg = "Réquisition enregistrée avec succès pour \"{$this->customReqProductName}\".";
        $this->successMessage = $msg;
        $this->dispatch('toast', message: $msg, type: 'success');
        $this->showCustomReqModal = false;
        $this->customReqProductName = '';
        $this->customReqQuantity = 1;
        $this->customReqNotes = '';
    }

    public function requestRequisition(int $productId): void
    {
        $product = Product::find($productId);
        if (!$product) return;

        \App\Models\Requisition::create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'requested_quantity' => max(10, $product->min_stock_alert * 2),
            'user_id' => auth()->id(),
            'status' => 'en_attente',
            'type' => 'demande_client',
            'notes' => "Demande enregistrée depuis l'Espace Caisse pour {$product->name}",
        ]);

        $msg = "Demande d'approvisionnement enregistrée avec succès pour \"{$product->name}\".";
        $this->successMessage = $msg;
        $this->dispatch('toast', message: $msg, type: 'success');
    }

    public function closeReceiptModal(): void
    {
        $this->showReceiptModal = false;
        $this->lastSale = null;
    }

    public function render()
    {
        // Synchroniser automatiquement les réquisitions en alerte
        \App\Models\Requisition::syncAlertRequisitions();
        $this->checkActiveSession();

        $query = Product::with(['category', 'batches']);

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('dci', 'like', '%' . $this->search . '%')
                  ->orWhere('code_barre', 'like', '%' . $this->search . '%');
            });
        }

        $products = $query->orderBy('name')->take(24)->get();
        $categories = Category::withCount('products')->get();

        return view('livewire.pos', [
            'products' => $products,
            'categories' => $categories,
            'total' => $this->calculateTotal(),
            'activeSession' => $this->activeSession,
        ])->layout('components.layouts.app', ['header' => 'Espace Caisse & Ventes']);
    }
}
