<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sale;

class SalesHistory extends Component
{
    use WithPagination;

    public string $search = '';
    public string $dateFilter = '';
    public ?int $selectedSaleId = null;
    public bool $showDetailsModal = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedDateFilter(): void
    {
        $this->resetPage();
    }

    public function showSaleDetails(int $saleId): void
    {
        $sale = Sale::find($saleId);
        if ($sale && !auth()->user()->isAdmin() && $sale->user_id !== auth()->id()) {
            return;
        }

        $this->selectedSaleId = $saleId;
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal(): void
    {
        $this->showDetailsModal = false;
        $this->selectedSaleId = null;
    }

    public function render()
    {
        $query = Sale::with(['items', 'user'])->latest();

        // Le vendeur (non-admin) ne voit que les ventes qu'il a effectuées
        if (auth()->check() && !auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                  ->orWhere('patient_name', 'like', '%' . $this->search . '%')
                  ->orWhere('doctor_name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function ($uq) {
                      $uq->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->dateFilter) {
            $query->whereDate('created_at', $this->dateFilter);
        }

        $sales = $query->paginate(12);
        $selectedSale = $this->selectedSaleId ? Sale::with(['items', 'user'])->find($this->selectedSaleId) : null;

        return view('livewire.sales-history', [
            'sales' => $sales,
            'selectedSale' => $selectedSale,
        ])->layout('components.layouts.app', ['header' => 'Historique & Journal des Ventes']);
    }
}
