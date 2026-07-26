<div class="space-y-6">

    <!-- Active Session Quick Status Banner -->
    <div class="bg-white p-5 rounded-3xl border border-[#d6f0ea] shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-[#00c9a7]/15 text-[#00c9a7] border border-[#00c9a7]/30 flex items-center justify-center text-2xl font-extrabold shadow-sm">
                <i class="fa-solid fa-cash-register"></i>
            </div>
            <div>
                <h3 class="font-heading font-extrabold text-lg text-[#0f172a]">État de la Caisse Personnel</h3>
                @if($currentSession)
                    <p class="text-xs text-[#05a88b] font-extrabold flex items-center gap-1.5 mt-0.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#00c9a7] animate-pulse"></span>
                        Session #{{ $currentSession->id }} Ouverte depuis {{ $currentSession->opened_at->format('H:i') }} (Fond initial: {{ number_format($currentSession->opening_balance, 0, ',', ' ') }} FC)
                    </p>
                @else
                    <p class="text-xs text-rose-600 font-extrabold flex items-center gap-1.5 mt-0.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        Aucune session ouverte. Cliquez sur Espace Caisse / POS pour ouvrir la caisse.
                    </p>
                @endif
            </div>
        </div>

        @if($currentSession)
            <div class="flex items-center gap-6 font-mono text-xs bg-slate-50 p-3 rounded-2xl border border-slate-200 shadow-sm">
                <div>
                    <span class="text-slate-500 font-bold block text-[10px]">Ventes Espèces</span>
                    <span class="font-black text-[#05a88b] text-sm">{{ number_format($currentCashTotal, 0, ',', ' ') }} FC</span>
                </div>
                <div class="w-px h-8 bg-slate-200"></div>
                <div>
                    <span class="text-slate-500 font-bold block text-[10px]">Mobile Money</span>
                    <span class="font-black text-cyan-700 text-sm">{{ number_format($currentMobileTotal, 0, ',', ' ') }} FC</span>
                </div>
                <div class="w-px h-8 bg-slate-200"></div>
                <div>
                    <span class="text-slate-500 font-bold block text-[10px]">Nombre Ventes</span>
                    <span class="font-black text-[#0f172a] text-sm">{{ $currentSalesCount }}</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
        <button 
            wire:click="$set('activeTab', 'sessions')" 
            class="px-4 py-2.5 rounded-xl font-extrabold text-xs transition-all flex items-center gap-2 {{ $activeTab === 'sessions' ? 'bg-[#00c9a7] text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}"
        >
            <i class="fa-solid fa-list-check"></i> Sessions de Caisse (Z-de-Caisse)
        </button>

        @if(auth()->check() && auth()->user()->isAdmin())
            <button 
                wire:click="$set('activeTab', 'audit')" 
                class="px-4 py-2.5 rounded-xl font-extrabold text-xs transition-all flex items-center gap-2 {{ $activeTab === 'audit' ? 'bg-[#00c9a7] text-white shadow-md' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}"
            >
                <i class="fa-solid fa-shield-halved"></i> Journal d'Audit & Sécurité
            </button>
        @endif
    </div>

    @if($activeTab === 'sessions')
        <!-- Sessions Table Section -->
        <div class="bg-white p-4 rounded-3xl border border-[#d6f0ea] space-y-4 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h4 class="font-heading font-extrabold text-sm text-[#0f172a]">Historique des Sessions de Caisse</h4>

                <div class="flex items-center gap-3">
                    @if(auth()->user()->isAdmin() && count($users) > 0)
                        <select wire:model.live="selectedUserId" class="px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] focus:border-[#00c9a7]">
                            <option value="">Tous les caissiers</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    @endif

                    <select wire:model.live="statusFilter" class="px-3 py-1.5 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] focus:border-[#00c9a7]">
                        <option value="">Tous les statuts</option>
                        <option value="open">Ouvertes</option>
                        <option value="closed">Clôturées</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs text-slate-800">
                    <thead class="bg-slate-100 uppercase font-extrabold text-slate-600 text-[10px] border-b border-slate-200">
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
                    <tbody class="divide-y divide-slate-100 font-mono">
                        @forelse($sessions as $sess)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-3.5 font-black text-[#0f172a]">#{{ $sess->id }}</td>
                                <td class="p-3.5 font-sans font-extrabold text-[#0f172a]">{{ $sess->user->name }}</td>
                                <td class="p-3.5 text-slate-600 font-bold">{{ $sess->opened_at->format('d/m/Y H:i') }}</td>
                                <td class="p-3.5 text-slate-600 font-bold">{{ $sess->closed_at ? $sess->closed_at->format('d/m/Y H:i') : '-' }}</td>
                                <td class="p-3.5 text-[#05a88b] font-black">{{ number_format($sess->opening_balance, 0, ',', ' ') }} FC</td>
                                <td class="p-3.5 text-slate-700 font-bold">{{ $sess->closing_balance_expected ? number_format($sess->closing_balance_expected, 0, ',', ' ') . ' FC' : '-' }}</td>
                                <td class="p-3.5 text-slate-700 font-bold">{{ $sess->closing_balance_actual ? number_format($sess->closing_balance_actual, 0, ',', ' ') . ' FC' : '-' }}</td>
                                <td class="p-3.5 font-black">
                                    @if(is_null($sess->difference))
                                        <span class="text-slate-400">-</span>
                                    @elseif($sess->difference == 0)
                                        <span class="text-[#05a88b]">0 FC (Équilibré)</span>
                                    @elseif($sess->difference > 0)
                                        <span class="text-cyan-700">+{{ number_format($sess->difference, 0, ',', ' ') }} FC (Excédent)</span>
                                    @else
                                        <span class="text-rose-600">{{ number_format($sess->difference, 0, ',', ' ') }} FC (Déficit)</span>
                                    @endif
                                </td>
                                <td class="p-3.5 font-sans">
                                    @if($sess->isOpen())
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 text-[10px] font-extrabold">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-bold border border-slate-300">
                                            Clôturée Z
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-8 text-center text-slate-500 font-sans italic font-bold">Aucune session enregistrée.</td>
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
        <div class="bg-white p-4 rounded-3xl border border-[#d6f0ea] space-y-4 shadow-sm">
            <h4 class="font-heading font-extrabold text-sm text-[#0f172a]">Journal des Actions & Événements de Sécurité</h4>

            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs text-slate-800">
                    <thead class="bg-slate-100 uppercase font-extrabold text-slate-600 text-[10px] border-b border-slate-200">
                        <tr>
                            <th class="p-3.5">Date / Heure</th>
                            <th class="p-3.5">Utilisateur</th>
                            <th class="p-3.5">Action</th>
                            <th class="p-3.5">Cible</th>
                            <th class="p-3.5">Adresse IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono">
                        @forelse($auditLogs as $log)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="p-3.5 text-slate-600 text-[11px] font-bold">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                <td class="p-3.5 font-sans font-extrabold text-[#0f172a]">{{ $log->user ? $log->user->name : 'Système' }}</td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[11px] font-extrabold border border-emerald-300">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-slate-700 text-[11px] font-bold">
                                    {{ $log->model_type ? $log->model_type . ' #' . $log->model_id : '-' }}
                                </td>
                                <td class="p-3.5 text-slate-500 text-[11px] font-bold">{{ $log->ip_address }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-slate-500 font-sans italic font-bold">Aucun log d'audit.</td>
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
