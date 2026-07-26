<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    public string $errorMessage = '';

    protected array $rules = [
        'email' => 'required|email',
        'password' => 'required|string',
    ];

    public function login()
    {
        $this->validate();
        $this->errorMessage = '';

        $user = User::where('email', $this->email)->first();

        if (!$user || !$user->is_active) {
            $this->errorMessage = 'Ce compte utilisateur n\'existe pas ou est désactivé.';
            $this->dispatch('toast', message: $this->errorMessage, type: 'error');
            return;
        }

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            session()->flash('toast', 'Bienvenue ' . Auth::user()->name . ' !');

            // Redirect based on role or to POS
            if (Auth::user()->isAdmin()) {
                return redirect()->intended(route('dashboard'));
            }

            return redirect()->intended(route('pos'));
        }

        $this->errorMessage = 'Adresse email ou mot de passe incorrect.';
        $this->dispatch('toast', message: $this->errorMessage, type: 'error');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('components.layouts.guest', ['title' => 'Connexion - PharmaSales']);
    }
}
