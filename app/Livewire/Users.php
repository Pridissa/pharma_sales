<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class Users extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = '';

    // Form Modal state
    public bool $showModal = false;
    public ?int $editingUserId = null;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $role = 'caissier';
    public string $password = '';
    public bool $is_active = true;

    public string $successMessage = '';
    public string $errorMessage = '';

    public function mount(): void
    {
        if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(403, 'Accès réservé aux administrateurs.');
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->editingUserId,
            'phone' => 'nullable|string|max:50',
            'role' => 'required|in:admin,caissier',
            'password' => $this->editingUserId ? 'nullable|string|min:6' : 'required|string|min:6',
            'is_active' => 'boolean',
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

    public function editUser(int $userId): void
    {
        $this->resetForm();
        $user = User::findOrFail($userId);

        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->role = $user->role;
        $this->is_active = $user->is_active;

        $this->showModal = true;
    }

    public function saveUser(): void
    {
        $validated = $this->validate();

        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'role' => $this->role,
                'is_active' => $this->is_active,
            ];

            if (!empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }

            $user->update($data);
            $this->successMessage = "Utilisateur {$user->name} mis à jour avec succès.";
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'role' => $this->role,
                'password' => Hash::make($this->password),
                'is_active' => $this->is_active,
            ]);
            $this->successMessage = "Utilisateur {$user->name} créé avec succès.";
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function toggleUserStatus(int $userId): void
    {
        $user = User::findOrFail($userId);
        if ($user->id === Auth::id()) {
            $this->errorMessage = "Vous ne pouvez pas désactiver votre propre compte actif.";
            return;
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusStr = $user->is_active ? 'activé' : 'désactivé';
        $this->successMessage = "Le compte de {$user->name} a été {$statusStr}.";
    }

    public function resetForm(): void
    {
        $this->editingUserId = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->role = 'caissier';
        $this->password = '';
        $this->is_active = true;
        $this->resetValidation();
    }

    public function render()
    {
        $query = User::latest();

        if ($this->roleFilter) {
            $query->where('role', $this->roleFilter);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        $users = $query->paginate(10);

        return view('livewire.users', [
            'users' => $users,
        ])->layout('components.layouts.app', ['header' => 'Gestion des Utilisateurs & Rôles']);
    }
}
