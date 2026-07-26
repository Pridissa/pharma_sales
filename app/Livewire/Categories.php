<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;

class Categories extends Component
{
    use WithPagination;

    public string $search = '';

    // Create / Edit state
    public bool $showModal = false;
    public ?int $editingCategoryId = null;
    public string $name = '';
    public string $description = '';

    // Delete state
    public bool $showDeleteModal = false;
    public ?int $deletingCategoryId = null;
    public ?string $deletingCategoryName = null;

    public function mount(): void
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Accès réservé aux administrateurs.');
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name,' . $this->editingCategoryId,
            'description' => 'nullable|string|max:1000',
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

    public function editCategory(int $id): void
    {
        $this->resetForm();
        $cat = Category::findOrFail($id);

        $this->editingCategoryId = $cat->id;
        $this->name = $cat->name;
        $this->description = $cat->description ?? '';

        $this->showModal = true;
    }

    public function saveCategory(): void
    {
        $validated = $this->validate();

        if ($this->editingCategoryId) {
            $cat = Category::findOrFail($this->editingCategoryId);
            $cat->update($validated);
            $msg = "Catégorie \"{$cat->name}\" mise à jour avec succès.";
        } else {
            $cat = Category::create($validated);
            $msg = "Catégorie \"{$cat->name}\" créée avec succès.";
        }

        $this->dispatch('toast', message: $msg, type: 'success');
        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $cat = Category::findOrFail($id);
        $this->deletingCategoryId = $cat->id;
        $this->deletingCategoryName = $cat->name;
        $this->showDeleteModal = true;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingCategoryId = null;
        $this->deletingCategoryName = null;
    }

    public function deleteCategory(): void
    {
        if (!$this->deletingCategoryId) return;

        $cat = Category::withCount('products')->find($this->deletingCategoryId);
        if ($cat) {
            if ($cat->products_count > 0) {
                $this->dispatch('toast', message: "Impossible de supprimer la catégorie \"{$cat->name}\" car elle contient {$cat->products_count} produit(s). Veuillez d'abord réassigner ou supprimer ces produits.", type: 'error');
                $this->cancelDelete();
                return;
            }

            $name = $cat->name;
            $cat->delete();
            $this->dispatch('toast', message: "La catégorie \"{$name}\" a été supprimée avec succès.", type: 'success');
        }

        $this->cancelDelete();
    }

    public function resetForm(): void
    {
        $this->editingCategoryId = null;
        $this->name = '';
        $this->description = '';
        $this->resetValidation();
    }

    public function render()
    {
        $categories = Category::withCount('products')
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy('name')
            ->paginate(12);

        return view('livewire.categories', [
            'categories' => $categories,
        ])->layout('components.layouts.app', ['header' => 'Gestion des Catégories de Produits']);
    }
}
