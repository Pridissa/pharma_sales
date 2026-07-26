<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use App\Models\Requisition;
use App\Models\Product;
use App\Models\Category;

class Requisitions extends Component
{
    use WithPagination;

    public string $search = '';
    public string $typeFilter = ''; // '', 'demande_client', 'seuil_alerte', 'approche_alerte'

    // Custom client requisition state
    public bool $showCustomReqModal = false;
    public string $customReqProductName = '';
    public int $customReqQuantity = 1;
    public string $customReqNotes = '';

    // Fulfill Stock Lot Modal state (Admin)
    public bool $showFulfillModal = false;
    public ?int $fulfillingRequisitionId = null;
    public ?int $fulfillingProductId = null; // null if product does not exist in stock yet
    public string $fulfillProductName = '';
    public ?int $fulfillCategoryId = null;
    public string $fulfillCodeBarre = '';
    public string $fulfillDci = '';
    public string $fulfillDosageUnit = '';
    public int $fulfillCurrentStock = 0;
    public int $fulfillAddQuantity = 10;
    public float $fulfillPurchasePrice = 0.0;
    public float $fulfillPrice = 0.0;
    public int $fulfillMinStockAlert = 10;
    public string $fulfillExpirationDate = '';
    public string $fulfillBatchNumber = '';
    public bool $fulfillRequiresPrescription = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    #[On('open-global-req-modal')]
    public function openCustomReqModal(): void
    {
        $this->customReqProductName = '';
        $this->customReqQuantity = 1;
        $this->customReqNotes = 'Produit demandé par un client (absent du stock)';
        $this->showCustomReqModal = true;
    }

    public function saveCustomRequisition(): void
    {
        if (empty(trim($this->customReqProductName))) {
            $this->dispatch('toast', message: 'Veuillez saisir le nom du produit demandé.', type: 'error');
            return;
        }

        Requisition::create([
            'product_id' => null,
            'product_name' => trim($this->customReqProductName),
            'requested_quantity' => max(1, $this->customReqQuantity),
            'user_id' => auth()->id(),
            'status' => 'en_attente',
            'type' => 'demande_client',
            'notes' => $this->customReqNotes ?: 'Demande client enregistrée',
        ]);

        $this->dispatch('toast', message: "Réquisition enregistrée avec succès pour \"{$this->customReqProductName}\".", type: 'success');
        $this->showCustomReqModal = false;
        $this->customReqProductName = '';
        $this->customReqQuantity = 1;
        $this->customReqNotes = '';
    }

    public function startFulfillRequisition(int $requisitionId)
    {
        if (!auth()->user()->isAdmin()) {
            $this->dispatch('toast', message: 'Seul un administrateur peut marquer une réquisition comme traitée.', type: 'error');
            return null;
        }

        $req = Requisition::find($requisitionId);
        if (!$req) return null;

        $this->fulfillingRequisitionId = $req->id;

        $product = null;
        if ($req->product_id) {
            $product = Product::find($req->product_id);
        }
        if (!$product) {
            $product = Product::where('name', 'like', '%' . trim($req->product_name) . '%')->first();
        }

        if ($product) {
            // Produit existant en stock
            $this->fulfillingProductId = $product->id;
            $this->fulfillProductName = $product->name;
            $this->fulfillCategoryId = $product->category_id;
            $this->fulfillCodeBarre = $product->code_barre ?? '';
            $this->fulfillDci = $product->dci ?? '';
            $this->fulfillDosageUnit = $product->dosage_unit ?? '';
            $this->fulfillCurrentStock = $product->stock_quantity;
            $this->fulfillAddQuantity = $req->requested_quantity > 0 ? $req->requested_quantity : 10;
            $this->fulfillPurchasePrice = (float)($product->purchase_price ?: 0);
            $this->fulfillPrice = (float)$product->price;
            $this->fulfillMinStockAlert = $product->min_stock_alert ?: 10;
            $this->fulfillExpirationDate = $product->expiration_date ? $product->expiration_date->format('Y-m-d') : '';
            $this->fulfillBatchNumber = 'LOT-' . date('Ym') . '-' . rand(100, 999);
            $this->fulfillRequiresPrescription = (bool)$product->requires_prescription;
        } else {
            // Produit absent du stock : ouvrir la modal pour le créer directement
            $this->fulfillingProductId = null;
            $this->fulfillProductName = $req->product_name;
            $this->fulfillCategoryId = Category::first()?->id;
            $this->fulfillCodeBarre = '';
            $this->fulfillDci = '';
            $this->fulfillDosageUnit = 'Boîte / Unité';
            $this->fulfillCurrentStock = 0;
            $this->fulfillAddQuantity = $req->requested_quantity > 0 ? $req->requested_quantity : 10;
            $this->fulfillPurchasePrice = 0.0;
            $this->fulfillPrice = 0.0;
            $this->fulfillMinStockAlert = 10;
            $this->fulfillExpirationDate = '';
            $this->fulfillBatchNumber = 'LOT-' . date('Ym') . '-' . rand(100, 999);
            $this->fulfillRequiresPrescription = false;
        }

        $this->showFulfillModal = true;
    }

    public function completeFulfillRequisition(): void
    {
        if (!auth()->user()->isAdmin()) return;

        if ($this->fulfillingProductId) {
            // Mise à jour de produit existant
            $product = Product::find($this->fulfillingProductId);
            if (!$product) return;

            $addQty = max(1, (int)$this->fulfillAddQuantity);
            $prevStock = $product->stock_quantity;
            $newStock = $prevStock + $addQty;

            $batchNumber = $this->fulfillBatchNumber ?: ('LOT-' . date('Ym') . '-' . rand(100, 999));
            $expDate = $this->fulfillExpirationDate ?: \Carbon\Carbon::now()->addYear()->format('Y-m-d');

            // Créer un lot pour cette livraison
            $batch = \App\Models\ProductBatch::create([
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
            if ($this->fulfillExpirationDate) $product->expiration_date = $this->fulfillExpirationDate;

            $product->syncStockFromBatches();
            $product->save();

            \App\Models\StockMovement::create([
                'product_id' => $product->id,
                'product_batch_id' => $batch->id,
                'user_id' => auth()->id(),
                'type' => 'entree',
                'quantity' => $addQty,
                'previous_quantity' => $prevStock,
                'new_quantity' => $product->stock_quantity,
                'reason' => "Livraison Réquisition #{$this->fulfillingRequisitionId}",
            ]);

            if ($this->fulfillingRequisitionId) {
                $req = Requisition::find($this->fulfillingRequisitionId);
                if ($req) $req->delete();
            }

            Requisition::syncAlertRequisitions();

            $this->dispatch('toast', message: "Approvisionnement validé ! Stock de \"{$product->name}\" augmenté de +{$addQty} (Nouveau stock: {$product->stock_quantity}).", type: 'success');
        } else {
            // Création d'un nouveau produit
            if (empty(trim($this->fulfillProductName))) {
                $this->dispatch('toast', message: 'Veuillez saisir le nom du produit.', type: 'error');
                return;
            }

            if (!$this->fulfillCategoryId) {
                $this->dispatch('toast', message: 'Veuillez sélectionner une catégorie.', type: 'error');
                return;
            }

            if ($this->fulfillPrice <= 0) {
                $this->dispatch('toast', message: 'Veuillez saisir un prix de vente valide supérieur à 0 FC.', type: 'error');
                return;
            }

            $addQty = max(1, (int)$this->fulfillAddQuantity);

            $product = Product::create([
                'name' => trim($this->fulfillProductName),
                'category_id' => $this->fulfillCategoryId,
                'code_barre' => $this->fulfillCodeBarre ?: null,
                'dci' => $this->fulfillDci ?: null,
                'dosage_unit' => $this->fulfillDosageUnit ?: null,
                'price' => (float)$this->fulfillPrice,
                'purchase_price' => (float)$this->fulfillPurchasePrice,
                'stock_quantity' => 0,
                'min_stock_alert' => max(1, (int)$this->fulfillMinStockAlert),
                'expiration_date' => $this->fulfillExpirationDate ?: null,
                'requires_prescription' => (bool)$this->fulfillRequiresPrescription,
            ]);

            $batchNumber = $this->fulfillBatchNumber ?: ('LOT-' . date('Ym') . '-' . rand(100, 999));
            $expDate = $this->fulfillExpirationDate ?: \Carbon\Carbon::now()->addYear()->format('Y-m-d');

            $batch = \App\Models\ProductBatch::create([
                'product_id' => $product->id,
                'batch_number' => $batchNumber,
                'expiration_date' => $expDate,
                'quantity' => $addQty,
                'purchase_price' => (float)$this->fulfillPurchasePrice,
                'supplier_name' => 'Approvisionnement Réquisition Initial',
                'is_active' => true,
            ]);

            $product->syncStockFromBatches();
            $product->save();

            \App\Models\StockMovement::create([
                'product_id' => $product->id,
                'product_batch_id' => $batch->id,
                'user_id' => auth()->id(),
                'type' => 'entree',
                'quantity' => $addQty,
                'previous_quantity' => 0,
                'new_quantity' => $product->stock_quantity,
                'reason' => 'Création initiale via Réquisition',
            ]);

            if ($this->fulfillingRequisitionId) {
                $req = Requisition::find($this->fulfillingRequisitionId);
                if ($req) $req->delete();
            }

            Requisition::syncAlertRequisitions();

            $this->dispatch('toast', message: "Nouveau produit \"{$product->name}\" créé et ajouté au stock avec +{$addQty} unités ! Réquisition traitée avec succès.", type: 'success');
        }

        $this->showFulfillModal = false;
        $this->fulfillingRequisitionId = null;
        $this->fulfillingProductId = null;
    }

    public function render()
    {
        Requisition::syncAlertRequisitions();

        $query = Requisition::with(['user', 'product'])->where('status', 'en_attente');

        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('product_name', 'like', '%' . $this->search . '%')
                  ->orWhere('notes', 'like', '%' . $this->search . '%');
            });
        }

        $requisitions = $query->latest()->paginate(12);
        $categories = Category::all();

        return view('livewire.requisitions', [
            'requisitions' => $requisitions,
            'categories' => $categories,
        ])->layout('components.layouts.app', ['header' => 'Gestion des Réquisitions & Approvisionnements']);
    }
}
