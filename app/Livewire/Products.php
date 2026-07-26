<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductBatch;
use App\Models\StockMovement;
use App\Models\AuditLog;
use Carbon\Carbon;

class Products extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $selectedCategory = null;
    public string $filterStock = ''; // '', 'low', 'out', 'expiring'

    // Form fields for Add/Edit Modal
    public bool $showModal = false;
    public ?int $editingProductId = null;

    public string $name = '';
    public string $dci = '';
    public string $code_barre = '';
    public ?int $category_id = null;
    public float $price = 0.0;
    public float $purchase_price = 0.0;
    public int $stock_quantity = 0;
    public int $min_stock_alert = 10;
    public string $expiration_date = '';
    public bool $requires_prescription = false;
    public string $dosage_unit = '';

    // Delete Modal state
    public bool $showDeleteModal = false;
    public ?int $deletingProductId = null;
    public ?string $deletingProductName = null;

    // Fulfill Requisition Modal state
    public bool $showFulfillModal = false;
    public ?int $fulfillingRequisitionId = null;
    public ?int $fulfillingProductId = null;
    public string $fulfillProductName = '';
    public int $fulfillCurrentStock = 0;
    public int $fulfillAddQuantity = 10;
    public float $fulfillPurchasePrice = 0.0;
    public float $fulfillPrice = 0.0;
    public string $fulfillExpirationDate = '';
    public string $fulfillBatchNumber = '';

    // Batch Management Modal state
    public bool $showBatchModal = false;
    public ?int $batchProductId = null;
    public ?Product $batchProduct = null;
    public string $newBatchNumber = '';
    public string $newBatchExpirationDate = '';
    public int $newBatchQuantity = 10;
    public float $newBatchPurchasePrice = 0.0;
    public string $newBatchSupplierName = '';

    // Stock Movement History Modal state
    public bool $showMovementsModal = false;
    public ?int $movementProductId = null;
    public ?Product $movementProduct = null;

    public string $successMessage = '';
    public string $errorMessage = '';

    public function mount(): void
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Accès réservé aux administrateurs.');
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'dci' => 'nullable|string|max:255',
            'code_barre' => 'nullable|string|max:255|unique:products,code_barre,' . $this->editingProductId,
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_alert' => 'required|integer|min:1',
            'expiration_date' => 'nullable|date',
            'requires_prescription' => 'boolean',
            'dosage_unit' => 'nullable|string|max:255',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function editProduct(int $productId): void
    {
        $this->resetForm();
        $product = Product::findOrFail($productId);
        
        $this->editingProductId = $product->id;
        $this->name = $product->name;
        $this->dci = $product->dci ?? '';
        $this->code_barre = $product->code_barre ?? '';
        $this->category_id = $product->category_id;
        $this->price = (float)$product->price;
        $this->purchase_price = (float)$product->purchase_price;
        $this->stock_quantity = $product->stock_quantity;
        $this->min_stock_alert = $product->min_stock_alert;
        $this->expiration_date = $product->expiration_date ? $product->expiration_date->format('Y-m-d') : '';
        $this->requires_prescription = $product->requires_prescription;
        $this->dosage_unit = $product->dosage_unit ?? '';

        $this->showModal = true;
    }

    public function saveProduct(): void
    {
        $validated = $this->validate();

        if ($this->editingProductId) {
            $product = Product::findOrFail($this->editingProductId);
            $oldStock = $product->stock_quantity;
            $product->update($validated);

            if ($oldStock !== $validated['stock_quantity']) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'ajustement',
                    'quantity' => $validated['stock_quantity'] - $oldStock,
                    'previous_quantity' => $oldStock,
                    'new_quantity' => $validated['stock_quantity'],
                    'reason' => 'Ajustement manuel depuis la fiche produit',
                ]);
            }

            AuditLog::log('product_updated', 'Product', $product->id, ['name' => $product->name]);
            $this->successMessage = "Produit {$product->name} mis à jour avec succès.";
        } else {
            $product = Product::create($validated);

            if ($validated['stock_quantity'] > 0) {
                // Créer le lot initial
                $batch = ProductBatch::create([
                    'product_id' => $product->id,
                    'batch_number' => 'LOT-' . date('Ym') . '-' . rand(100, 999),
                    'expiration_date' => $validated['expiration_date'] ?: Carbon::now()->addYear()->format('Y-m-d'),
                    'quantity' => $validated['stock_quantity'],
                    'purchase_price' => $validated['purchase_price'],
                    'supplier_name' => 'Stock Initial',
                    'is_active' => true,
                ]);

                StockMovement::create([
                    'product_id' => $product->id,
                    'product_batch_id' => $batch->id,
                    'user_id' => auth()->id(),
                    'type' => 'entree',
                    'quantity' => $validated['stock_quantity'],
                    'previous_quantity' => 0,
                    'new_quantity' => $validated['stock_quantity'],
                    'reason' => 'Création initiale du produit',
                ]);
            }

            AuditLog::log('product_created', 'Product', $product->id, ['name' => $product->name]);
            $this->successMessage = "Produit {$product->name} créé avec succès.";
        }

        // Auto-fulfill pending requisitions if stock quantity is replenished
        if ($product->stock_quantity > 0) {
            \App\Models\Requisition::where('product_id', $product->id)
                ->orWhere('product_name', 'like', '%' . $product->name . '%')
                ->delete();
        }

        if ($this->fulfillingRequisitionId) {
            $req = \App\Models\Requisition::find($this->fulfillingRequisitionId);
            if ($req) $req->delete();
            $this->fulfillingRequisitionId = null;
        }

        $this->dispatch('toast', message: $this->successMessage, type: 'success');
        $this->showModal = false;
        $this->resetForm();
    }

    // Fulfill / Stock Lot Modal methods
    public function startFulfillRequisition(int $requisitionId): void
    {
        $req = \App\Models\Requisition::find($requisitionId);
        if (!$req) return;

        $this->fulfillingRequisitionId = $req->id;

        // Chercher si le produit existe déjà en stock
        $product = null;
        if ($req->product_id) {
            $product = Product::find($req->product_id);
        }
        if (!$product) {
            $product = Product::where('name', 'like', '%' . trim($req->product_name) . '%')->first();
        }

        if ($product) {
            // Produit existant : ouvrir la modal d'ajout de stock / nouveau lot
            $this->fulfillingProductId = $product->id;
            $this->fulfillProductName = $product->name;
            $this->fulfillCurrentStock = $product->stock_quantity;
            $this->fulfillAddQuantity = $req->requested_quantity > 0 ? $req->requested_quantity : 10;
            $this->fulfillPurchasePrice = (float)($product->purchase_price ?: 0);
            $this->fulfillPrice = (float)$product->price;
            $this->fulfillExpirationDate = $product->expiration_date ? $product->expiration_date->format('Y-m-d') : '';
            $this->fulfillBatchNumber = 'LOT-' . date('Ym') . '-' . rand(100, 999);
            $this->showFulfillModal = true;
        } else {
            // Produit absent de la base : ouvrir le formulaire de création de produit pré-rempli
            $this->resetForm();
            $this->name = $req->product_name;
            $this->stock_quantity = max(1, $req->requested_quantity);
            $this->showModal = true;
            $this->dispatch('toast', message: "Produit \"{$req->product_name}\" non présent en stock. Renseignez les informations ci-dessous pour le créer.", type: 'success');
        }
    }

    public function completeFulfillRequisition(): void
    {
        if (!$this->fulfillingProductId) return;

        $product = Product::find($this->fulfillingProductId);
        if (!$product) return;

        $addQty = max(1, (int)$this->fulfillAddQuantity);
        $prevStock = $product->stock_quantity;
        $newStock = $prevStock + $addQty;

        $batchNumber = $this->fulfillBatchNumber ?: ('LOT-' . date('Ym') . '-' . rand(100, 999));
        $expDate = $this->fulfillExpirationDate ?: Carbon::now()->addYear()->format('Y-m-d');

        // Créer un lot pour cette livraison
        $batch = ProductBatch::create([
            'product_id' => $product->id,
            'batch_number' => $batchNumber,
            'expiration_date' => $expDate,
            'quantity' => $addQty,
            'purchase_price' => $this->fulfillPurchasePrice > 0 ? $this->fulfillPurchasePrice : $product->purchase_price,
            'supplier_name' => 'Approvisionnement Réquisition',
            'is_active' => true,
        ]);

        if ($this->fulfillPurchasePrice > 0) $product->purchase_price = $this->fulfillPurchasePrice;
        if ($this->fulfillPrice > 0) $product->price = $this->fulfillPrice;

        $product->syncStockFromBatches();
        $product->save();

        StockMovement::create([
            'product_id' => $product->id,
            'product_batch_id' => $batch->id,
            'user_id' => auth()->id(),
            'type' => 'entree',
            'quantity' => $addQty,
            'previous_quantity' => $prevStock,
            'new_quantity' => $product->stock_quantity,
            'reason' => "Livraison Réquisition #{$this->fulfillingRequisitionId}",
        ]);

        // Marquer la réquisition comme traitée
        if ($this->fulfillingRequisitionId) {
            $req = \App\Models\Requisition::find($this->fulfillingRequisitionId);
            if ($req) $req->delete();
        }

        // Re-synchroniser les réquisitions d'alerte
        \App\Models\Requisition::syncAlertRequisitions();

        $message = "Approvisionnement validé ! Stock de \"{$product->name}\" augmenté de +{$addQty} (Nouveau stock: {$product->stock_quantity}).";
        $this->successMessage = $message;
        $this->dispatch('toast', message: $message, type: 'success');

        $this->showFulfillModal = false;
        $this->fulfillingRequisitionId = null;
        $this->fulfillingProductId = null;
    }

    // Modal de gestion des Lots (Batch Management)
    public function openBatchModal(int $productId): void
    {
        $this->batchProductId = $productId;
        $this->batchProduct = Product::with('batches')->findOrFail($productId);
        $this->newBatchNumber = 'LOT-' . date('Ym') . '-' . rand(100, 999);
        $this->newBatchExpirationDate = Carbon::now()->addYear()->format('Y-m-d');
        $this->newBatchQuantity = 10;
        $this->newBatchPurchasePrice = (float)($this->batchProduct->purchase_price ?: 0);
        $this->newBatchSupplierName = '';
        $this->showBatchModal = true;
    }

    public function saveNewBatch(): void
    {
        if (!$this->batchProductId || !$this->batchProduct) return;

        if (empty(trim($this->newBatchNumber))) {
            $this->errorMessage = 'Le numéro de lot est obligatoire.';
            return;
        }

        if (empty($this->newBatchExpirationDate)) {
            $this->errorMessage = 'La date de péremption est obligatoire.';
            return;
        }

        $addQty = max(1, (int)$this->newBatchQuantity);
        $prevStock = $this->batchProduct->stock_quantity;

        $batch = ProductBatch::create([
            'product_id' => $this->batchProduct->id,
            'batch_number' => trim($this->newBatchNumber),
            'expiration_date' => $this->newBatchExpirationDate,
            'quantity' => $addQty,
            'purchase_price' => max(0, $this->newBatchPurchasePrice),
            'supplier_name' => $this->newBatchSupplierName ?: 'Fournisseur Général',
            'is_active' => true,
        ]);

        $this->batchProduct->syncStockFromBatches();
        $this->batchProduct->refresh();

        StockMovement::create([
            'product_id' => $this->batchProduct->id,
            'product_batch_id' => $batch->id,
            'user_id' => auth()->id(),
            'type' => 'entree',
            'quantity' => $addQty,
            'previous_quantity' => $prevStock,
            'new_quantity' => $this->batchProduct->stock_quantity,
            'reason' => 'Réception nouveau lot (' . $batch->batch_number . ')',
        ]);

        AuditLog::log('batch_added', 'ProductBatch', $batch->id, [
            'product' => $this->batchProduct->name,
            'batch_number' => $batch->batch_number,
            'quantity' => $addQty,
        ]);

        $msg = "Nouveau lot {$batch->batch_number} (+{$addQty}) ajouté pour {$this->batchProduct->name} !";
        $this->successMessage = $msg;
        $this->dispatch('toast', message: $msg, type: 'success');

        $this->batchProduct = Product::with('batches')->find($this->batchProductId);
        $this->newBatchNumber = 'LOT-' . date('Ym') . '-' . rand(100, 999);
        $this->newBatchQuantity = 10;
    }

    public function toggleBatchStatus(int $batchId): void
    {
        $batch = ProductBatch::find($batchId);
        if ($batch) {
            $batch->is_active = !$batch->is_active;
            $batch->save();

            if ($this->batchProduct) {
                $this->batchProduct->syncStockFromBatches();
                $this->batchProduct->refresh();
            }

            $this->dispatch('toast', message: "Statut du lot {$batch->batch_number} mis à jour.", type: 'success');
        }
    }

    // Modal Mouvements de stock
    public function openMovementsModal(int $productId): void
    {
        $this->movementProductId = $productId;
        $this->movementProduct = Product::with(['stockMovements.batch', 'stockMovements.user'])->findOrFail($productId);
        $this->showMovementsModal = true;
    }

    public function confirmDelete(int $productId): void
    {
        $product = Product::findOrFail($productId);
        $this->deletingProductId = $product->id;
        $this->deletingProductName = $product->name;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingProductId = null;
        $this->deletingProductName = null;
    }

    public function deleteProduct(): void
    {
        if (!$this->deletingProductId) return;

        $product = Product::find($this->deletingProductId);
        if ($product) {
            $name = $product->name;
            AuditLog::log('product_deleted', 'Product', $product->id, ['name' => $name]);
            $product->delete();
            $this->successMessage = "Le produit \"{$name}\" a été supprimé avec succès.";
        }

        $this->cancelDelete();
    }

    public function resetForm(): void
    {
        $this->editingProductId = null;
        $this->name = '';
        $this->dci = '';
        $this->code_barre = '';
        $this->category_id = Category::first()?->id;
        $this->price = 0.0;
        $this->purchase_price = 0.0;
        $this->stock_quantity = 0;
        $this->min_stock_alert = 10;
        $this->expiration_date = '';
        $this->requires_prescription = false;
        $this->dosage_unit = '';
        $this->fulfillingRequisitionId = null;
        $this->resetValidation();
    }

    public function render()
    {
        // Synchroniser automatiquement les produits au seuil d'alerte ou approchant de l'alerte
        \App\Models\Requisition::syncAlertRequisitions();

        $query = Product::with(['category', 'batches']);

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        if ($this->filterStock === 'low') {
            $query->whereColumn('stock_quantity', '<=', 'min_stock_alert')->where('stock_quantity', '>', 0);
        } elseif ($this->filterStock === 'out') {
            $query->where('stock_quantity', '<=', 0);
        } elseif ($this->filterStock === 'expiring') {
            $query->whereNotNull('expiration_date')->where('expiration_date', '<=', Carbon::now()->addDays(60));
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('dci', 'like', '%' . $this->search . '%')
                  ->orWhere('code_barre', 'like', '%' . $this->search . '%');
            });
        }

        $products = $query->orderBy('name')->paginate(12);
        $categories = Category::all();
        $requisitions = \App\Models\Requisition::with(['user', 'product'])->where('status', 'en_attente')->latest()->get();

        return view('livewire.products', [
            'products' => $products,
            'categories' => $categories,
            'requisitions' => $requisitions,
        ])->layout('components.layouts.app', ['header' => 'Gestion des Stocks, Lots & Médicaments']);
    }
}
