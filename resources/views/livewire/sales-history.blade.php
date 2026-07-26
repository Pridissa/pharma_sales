<div class="space-y-6">

    <!-- Filters Bar -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 glass-panel p-4 rounded-3xl border border-slate-800">
        <div class="flex-1 flex flex-wrap items-center gap-3 w-full">
            <div class="relative flex-1 min-w-[240px]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.250ms="search" 
                    placeholder="Recherche N° Facture, Patient ou Médecin..." 
                    class="w-full pl-9 pr-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500"
                >
            </div>

            <div 
                x-data 
                x-init="flatpickr($refs.historyDatePicker, { 
                    locale: 'fr', 
                    dateFormat: 'Y-m-d', 
                    altInput: true, 
                    altFormat: 'd/m/Y', 
                    theme: 'dark', 
                    onChange: function(selectedDates, dateStr) { $wire.set('dateFilter', dateStr); } 
                })"
                class="flex items-center gap-2"
            >
                <label class="text-xs text-slate-400 font-semibold">Date:</label>
                <div class="relative">
                    <input 
                        x-ref="historyDatePicker" 
                        type="text" 
                        wire:model.live="dateFilter" 
                        placeholder="Filtrer par date..."
                        class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500 font-mono cursor-pointer"
                    >
                    <i class="fa-solid fa-calendar-days absolute right-3 top-2.5 text-slate-400 pointer-events-none"></i>
                </div>
                @if($dateFilter)
                    <button wire:click="$set('dateFilter', '')" class="text-xs text-slate-400 hover:text-white px-2 py-1 bg-slate-800 rounded-lg">
                        Effacer
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="glass-panel rounded-3xl border border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 uppercase font-semibold text-slate-400 text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="p-3.5">N° Facture</th>
                        <th class="p-3.5">Vendeur / Caissier</th>
                        <th class="p-3.5">Date & Heure</th>
                        <th class="p-3.5">Patient / Prescripteur</th>
                        <th class="p-3.5">Nb Articles</th>
                        <th class="p-3.5">Mode Règlement</th>
                        <th class="p-3.5 text-right">Total Vente</th>
                        <th class="p-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="p-3.5 font-mono font-bold text-emerald-400">
                                {{ $sale->invoice_number }}
                            </td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 text-[11px] font-semibold flex items-center gap-1.5 w-fit">
                                    <i class="fa-solid fa-user-check text-[10px]"></i>
                                    {{ $sale->user ? $sale->user->name : 'N/A' }}
                                </span>
                            </td>
                            <td class="p-3.5 text-slate-400">
                                {{ $sale->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="p-3.5">
                                @if($sale->patient_name || $sale->doctor_name)
                                    <div class="font-semibold text-slate-200">{{ $sale->patient_name ?: 'Client anonyme' }}</div>
                                    @if($sale->doctor_name)
                                        <div class="text-[10px] text-amber-300">Dr: {{ $sale->doctor_name }}</div>
                                    @endif
                                @else
                                    <span class="text-slate-500">Client Comptant</span>
                                @endif
                            </td>
                            <td class="p-3.5 font-mono">
                                {{ $sale->items->count() }} article(s)
                            </td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded-full bg-slate-800 text-slate-300 text-[10px] font-semibold border border-slate-700">
                                    {{ $sale->payment_method }}
                                </span>
                            </td>
                            <td class="p-3.5 text-right font-mono font-bold text-white text-sm">
                                {{ number_format($sale->total_amount, 0, ',', ' ') }} FC
                            </td>
                            <td class="p-3.5 text-right">
                                <button 
                                    wire:click="showSaleDetails({{ $sale->id }})" 
                                    class="px-3 py-1.5 rounded-lg bg-emerald-500/20 hover:bg-emerald-500 hover:text-slate-950 text-emerald-400 text-xs font-bold transition-all flex items-center gap-1.5 ml-auto"
                                >
                                    <i class="fa-solid fa-file-invoice"></i> Détails
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">Aucune vente enregistrée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800">
            {{ $sales->links() }}
        </div>
    </div>

    <!-- Sale Detail Modal -->
    @if($showDetailsModal && $selectedSale)
        <div id="printable-receipt-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-fadeIn">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl relative">
                <button wire:click="closeDetailsModal" class="absolute top-4 right-4 text-slate-400 hover:text-white no-print-element">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-800 no-print-element">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-lg text-white">Détails de la Facture</h3>
                        <p class="text-xs text-emerald-400 font-mono font-bold">{{ $selectedSale->invoice_number }}</p>
                    </div>
                </div>

                <div class="space-y-4 text-xs">
                    <!-- Ticket Area for Print -->
                    <div id="printable-receipt" class="bg-white text-slate-900 p-6 rounded-xl font-mono text-xs shadow-inner space-y-3">
                        <div class="text-center border-b border-dashed border-slate-400 pb-3">
                            <h2 class="font-bold text-base tracking-wider uppercase text-slate-950">BITA PHARMA</h2>
                            <p class="text-[10px] italic font-serif text-slate-700 font-bold mb-1">"La confiance au cœur de vos soins"</p>
                            <p class="text-[10px] font-semibold text-slate-800">Tél: +243 80 88 58 326 / +243 99 45 50 510</p>
                            <div class="mt-2 text-[10px] text-slate-800 border-t border-slate-200 pt-1">
                                <p class="font-bold">Facture N°: {{ $selectedSale->invoice_number }}</p>
                                <p>Date: {{ $selectedSale->created_at->format('d/m/Y H:i:s') }}</p>
                                <p class="font-bold text-emerald-800">Vendeur / Caissier: {{ $selectedSale->user ? $selectedSale->user->name : 'Non spécifié' }}</p>
                                @if($selectedSale->patient_name)
                                    <p>Patient: {{ $selectedSale->patient_name }}</p>
                                @endif
                                @if($selectedSale->doctor_name)
                                    <p>Prescripteur: {{ $selectedSale->doctor_name }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Items list -->
                        <div class="space-y-1.5 py-1">
                            @foreach($selectedSale->items as $item)
                                <div class="flex justify-between items-start text-xs">
                                    <span class="truncate max-w-[200px] font-medium">{{ $item->quantity }}x {{ $item->product_name }}</span>
                                    <span class="font-bold font-mono">{{ number_format($item->subtotal, 0, ',', ' ') }} FC</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Totals -->
                        <div class="border-t border-dashed border-slate-400 pt-2 space-y-1 font-bold">
                            <div class="flex justify-between text-sm text-slate-950">
                                <span>TOTAL NET:</span>
                                <span>{{ number_format($selectedSale->total_amount, 0, ',', ' ') }} FC</span>
                            </div>
                            <div class="flex justify-between text-[11px] font-normal text-slate-800">
                                <span>Montant Versé ({{ $selectedSale->payment_method }}):</span>
                                <span>{{ number_format($selectedSale->amount_paid, 0, ',', ' ') }} FC</span>
                            </div>
                            <div class="flex justify-between text-[11px] font-normal text-slate-800">
                                <span>Monnaie Rendue:</span>
                                <span>{{ number_format($selectedSale->change_amount, 0, ',', ' ') }} FC</span>
                            </div>
                        </div>

                        <div class="text-center pt-3 border-t border-dashed border-slate-400 text-[9px] italic text-slate-700">
                            Merci de votre confiance. Bon rétablissement !
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 no-print-element">
                        <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold flex items-center gap-2">
                            <i class="fa-solid fa-print"></i> Imprimer Ticket
                        </button>
                        <button wire:click="closeDetailsModal" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold">
                            Fermer
                        </button>
                    </div>
                </div>

            </div>
        </div>
    @endif

</div>
