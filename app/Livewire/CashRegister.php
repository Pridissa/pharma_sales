<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CashSession;
use App\Models\AuditLog;
use App\Models\Sale;
use App\Models\User;

class CashRegister extends Component
{
    use WithPagination;

    public string $activeTab = 'sessions'; // 'sessions', 'audit'
    public ?int $selectedUserId = null;
    public string $statusFilter = ''; // '', 'open', 'closed'

    public function mount(): void
    {
        if (!auth()->check()) {
            abort(403, 'Veuillez vous connecter.');
        }
    }

    public function render()
    {
        $sessionsQuery = CashSession::with(['user', 'sales']);

        if (!auth()->user()->isAdmin()) {
            $sessionsQuery->where('user_id', auth()->id());
        } elseif ($this->selectedUserId) {
            $sessionsQuery->where('user_id', $this->selectedUserId);
        }

        if ($this->statusFilter) {
            $sessionsQuery->where('status', $this->statusFilter);
        }

        $sessions = $sessionsQuery->latest('opened_at')->paginate(10);
        $users = auth()->user()->isAdmin() ? User::orderBy('name')->get() : collect();

        $auditLogs = AuditLog::with('user')->latest()->paginate(15, ['*'], 'auditPage');

        // Active session overview
        $currentSession = CashSession::activeForUser(auth()->id());
        $currentSalesCount = 0;
        $currentCashTotal = 0;
        $currentMobileTotal = 0;

        if ($currentSession) {
            $currentSalesCount = Sale::where('cash_session_id', $currentSession->id)->count();
            $currentCashTotal = Sale::where('cash_session_id', $currentSession->id)->where('payment_method', 'Espèces')->sum('total_amount');
            $currentMobileTotal = Sale::where('cash_session_id', $currentSession->id)->where('payment_method', 'Mobile Money')->sum('total_amount');
        }

        return view('livewire.cash-register', [
            'sessions' => $sessions,
            'auditLogs' => $auditLogs,
            'users' => $users,
            'currentSession' => $currentSession,
            'currentSalesCount' => $currentSalesCount,
            'currentCashTotal' => $currentCashTotal,
            'currentMobileTotal' => $currentMobileTotal,
        ])->layout('components.layouts.app', ['header' => 'Gestion de la Caisse & Sécurité Financière']);
    }
}
