<div class="space-y-6">

    <!-- Active Session Quick Status Banner -->
    <div class="glass-panel p-5 rounded-3xl border border-slate-800 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-slate-950 flex items-center justify-center text-2xl font-bold shadow-lg shadow-emerald-500/20">
                <i class="fa-solid fa-cash-register"></i>
            </div>
            <div>
                <h3 class="font-heading font-bold text-lg text-white">État de la Caisse Personnel</h3>
                @if($currentSession)
                    <p class="text-xs text-emerald-400 font-semibold flex items-center gap-1.5 mt-0.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Session #{{ $currentSession->id }} Ouverte depuis {{ $currentSession->opened_at->format('H:i') }} (Fond initial: {{ number_format($currentSession->opening_balance, 0, ',', ' ') }} FC)
                    </p>
                @else
                    <p class="text-xs text-rose-400 font-semibold flex items-center gap-1.5 mt-0.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        Aucune session ouverte. Cliquez sur Espace Caisse / POS pour ouvrir la caisse.
                    </p>
                @endif
            </div>
        </div>

        @if($currentSession)
            <div class="flex items-center gap-6 font-mono text-xs glass-card p-3 rounded-2xl border border-slate-800">
                <div>
                    <span class="text-slate-400 block text-[10px]">Ventes Espèces</span>
                    <span class="font-bold text-emerald-400 text-sm">{{ number_format($currentCashTotal, 0, ',', ' ') }} FC</span>
                </div>
                <div class="w-px h-8 bg-slate-800"></div>
                <div>
                    <span class="text-slate-400 block text-[10px]">Mobile Money</span>
                    <span class="font-bold text-cyan-400 text-sm">{{ number_format($currentMobileTotal, 0, ',', ' ') }} FC</span>
                </div>
                <div class="w-px h-8 bg-slate-800"></div>
                <div>
                    <span class="text-slate-400 block text-[10px]">Nombre Ventes</span>
                    <span class="font-bold text-white text-sm">{{ $currentSalesCount }}</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-800 pb-2">
        <button 
            wire:click="$set('activeTab', 'sessions')" 
            class="px-4 py-2.5 rounded-xl font-bold text-xs transition-all flex items-center gap-2 {{ $activeTab === 'sessions' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}"
        >
            <i class="fa-solid fa-list-check"></i> Sessions de Caisse (Z-de-Caisse)
        </button>

        @if(auth()->check() && auth()->user()->isAdmin())
            <button 
                wire:click="$set('activeTab', 'audit')" 
                class="px-4 py-2.5 rounded-xl font-bold text-xs transition-all flex items-center gap-2 {{ $activeTab === 'audit' ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/20' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}"
            >
                <i class="fa-solid fa-shield-halved"></i> Journal d'Audit & Sécurité
            </button>
        @endif
    </div>

    @if($activeTab === 'sessions')
        <!-- Sessions Table Section -->
        <div class="glass-panel p-4 rounded-3xl border border-slate-800 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h4 class="font-heading font-bold text-sm text-white">Historique des Sessions de Caisse</h4>

                <div class="flex items-center gap-3">
                    @if(auth()->user()->isAdmin() && count($users) > 0)
                        <select wire:model.live="selectedUserId" class="px-3 py-1.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white">
                            <option value="">Tous les caissiers</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    @endif

                    <select wire:model.live="statusFilter" class="px-3 py-1.5 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white">
                        <option value="">Tous les statuts</option>
                        <option value="open">Ouvertes</option>
                        <option value="closed">Clôturées</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-800">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/80 uppercase font-semibold text-slate-400 text-[10px] border-b border-slate-800">
                        <tr>
                            <th class="p-3.5">Session ID</th>
                            <th class="p-3.5">Caissier</th>
                            <th class="p-3.5">Ouverture</th>
                            <th class="p-3.5">Clôture</th>
                            <th class="p-3.5">Fond Initial</th>
                            <th class="p-3.5">Total Attendu</th>
                            <th class="p-3.5">Montant Compté</th>
                            <th class="p-3.5">Écart de Caisse</th>
                            <th class="p-3.5">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-mono">
                        @forelse($sessions as $sess)
                            <tr class="hover:bg-slate-800/40">
                                <td class="p-3.5 font-bold text-white">#{{ $sess->id }}</td>
                                <td class="p-3.5 font-sans font-semibold text-slate-200">{{ $sess->user->name }}</td>
                                <td class="p-3.5 text-slate-400">{{ $sess->opened_at->format('d/m/Y H:i') }}</td>
                                <td class="p-3.5 text-slate-400">{{ $sess->closed_at ? $sess->closed_at->format('d/m/Y H:i') : '-' }}</td>
                                <td class="p-3.5 text-emerald-400 font-bold">{{ number_format($sess->opening_balance, 0, ',', ' ') }} FC</td>
                                <td class="p-3.5 text-slate-200">{{ $sess->closing_balance_expected ? number_format($sess->closing_balance_expected, 0, ',', ' ') . ' FC' : '-' }}</td>
                                <td class="p-3.5 text-slate-200">{{ $sess->closing_balance_actual ? number_format($sess->closing_balance_actual, 0, ',', ' ') . ' FC' : '-' }}</td>
                                <td class="p-3.5 font-bold">
                                    @if(is_null($sess->difference))
                                        <span class="text-slate-500">-</span>
                                    @elseif($sess->difference == 0)
                                        <span class="text-emerald-400">0 FC (Équilibré)</span>
                                    @elseif($sess->difference > 0)
                                        <span class="text-cyan-400">+{{ number_format($sess->difference, 0, ',', ' ') }} FC (Excédent)</span>
                                    @else
                                        <span class="text-rose-400">{{ number_format($sess->difference, 0, ',', ' ') }} FC (Déficit)</span>
                                    @endif
                                </td>
                                <td class="p-3.5 font-sans">
                                    @if($sess->isOpen())
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[10px] font-bold">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-slate-800 text-slate-400 text-[10px] font-semibold">
                                            Clôturée Z
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-8 text-center text-slate-500 font-sans italic">Aucune session enregistrée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-2">
                {{ $sessions->links() }}
            </div>
        </div>
    @elseif($activeTab === 'audit')
        <!-- Audit Logs Section -->
        <div class="glass-panel p-4 rounded-3xl border border-slate-800 space-y-4">
            <h4 class="font-heading font-bold text-sm text-white">Journal des Actions & Événements de Sécurité</h4>

            <div class="overflow-x-auto rounded-2xl border border-slate-800">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/80 uppercase font-semibold text-slate-400 text-[10px] border-b border-slate-800">
                        <tr>
                            <th class="p-3.5">Date / Heure</th>
                            <th class="p-3.5">Utilisateur</th>
                            <th class="p-3.5">Action</th>
                            <th class="p-3.5">Cible</th>
                            <th class="p-3.5">Adresse IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-mono">
                        @forelse($auditLogs as $log)
                            <tr class="hover:bg-slate-800/40">
                                <td class="p-3.5 text-slate-400 text-[11px]">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                <td class="p-3.5 font-sans font-semibold text-slate-200">{{ $log->user ? $log->user->name : 'Système' }}</td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-800 text-emerald-400 text-[11px] font-bold">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-slate-300 text-[11px]">
                                    {{ $log->model_type ? $log->model_type . ' #' . $log->model_id : '-' }}
                                </td>
                                <td class="p-3.5 text-slate-500 text-[11px]">{{ $log->ip_address }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-500 font-sans italic">Aucun log d'audit.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pt-2">
                {{ $auditLogs->links() }}
            </div>
        </div>
    @endif

</div>
