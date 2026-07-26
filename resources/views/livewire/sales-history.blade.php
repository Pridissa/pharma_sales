<div class="space-y-6">

    <!-- Filters Bar -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white p-4 rounded-3xl border border-[#d6f0ea] shadow-sm">
        <div class="flex-1 flex flex-wrap items-center gap-3 w-full">
            <div class="relative flex-1 min-w-[240px]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#00c9a7]">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.250ms="search" 
                    placeholder="Recherche N° Facture, Patient ou Médecin..." 
                    class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] placeholder-slate-400 focus:outline-none focus:border-[#00c9a7]"
                >
            </div>

            <div class="flex items-center gap-2">
                <label class="text-xs text-slate-700 font-extrabold">Date:</label>
                <div class="w-48">
                    <x-date-picker wire:model.live="dateFilter" placeholder="Filtrer par date..." />
                </div>
                @if($dateFilter)
                    <button wire:click="$set('dateFilter', '')" class="text-xs text-slate-700 hover:text-slate-900 px-2 py-1 bg-slate-100 rounded-lg font-bold border border-slate-300">
                        Effacer
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-3xl border border-[#d6f0ea] overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800">
                <thead class="bg-slate-100 uppercase font-extrabold text-slate-600 text-[10px] border-b border-slate-200">
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
                <tbody class="divide-y divide-slate-100">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-3.5 font-mono font-black text-[#05a88b]">
                                {{ $sale->invoice_number }}
                            </td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-[11px] font-extrabold flex items-center gap-1.5 w-fit">
                                    <i class="fa-solid fa-user-check text-[10px]"></i>
                                    {{ $sale->user ? $sale->user->name : 'N/A' }}
                                </span>
                            </td>
                            <td class="p-3.5 text-slate-600 font-bold">
                                {{ $sale->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="p-3.5">
                                @if($sale->patient_name || $sale->doctor_name)
                                    <div class="font-extrabold text-[#0f172a]">{{ $sale->patient_name ?: 'Client anonyme' }}</div>
                                    @if($sale->doctor_name)
                                        <div class="text-[10px] text-amber-800 font-bold">Dr: {{ $sale->doctor_name }}</div>
                                    @endif
                                @else
                                    <span class="text-slate-500 font-medium">Client Comptant</span>
                                @endif
                            </td>
                            <td class="p-3.5 font-mono font-bold">
                                {{ $sale->items->count() }} article(s)
                            </td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-800 text-[10px] font-extrabold border border-slate-300">
                                    {{ $sale->payment_method }}
                                </span>
                            </td>
                            <td class="p-3.5 text-right font-mono font-black text-[#0f172a] text-sm">
                                {{ number_format($sale->total_amount, 0, ',', ' ') }} FC
                            </td>
                            <td class="p-3.5 text-right">
                                <button 
                                    wire:click="showSaleDetails({{ $sale->id }})" 
                                    class="px-3 py-1.5 rounded-xl bg-[#00c9a7] hover:bg-[#00b899] text-white text-xs font-extrabold transition-all flex items-center gap-1.5 ml-auto shadow-sm"
                                >
                                    <i class="fa-solid fa-file-invoice"></i> Détails
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-500 font-bold">Aucune vente enregistrée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200">
            {{ $sales->links() }}
        </div>
    </div>

    <!-- Sale Detail Modal -->
    @if($showDetailsModal && $selectedSale)
        <div id="printable-receipt-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop animate-fadeIn">
            <div class="bg-white border border-slate-200 rounded-3xl max-w-lg w-full p-6 shadow-2xl relative">
                <button wire:click="closeDetailsModal" class="absolute top-4 right-4 text-slate-400 hover:text-slate-800 no-print-element">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-200 no-print-element">
                    <div class="w-10 h-10 rounded-2xl bg-[#00c9a7]/15 text-[#00c9a7] flex items-center justify-center text-lg">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-lg text-[#0f172a]">Détails de la Facture</h3>
                        <p class="text-xs text-[#05a88b] font-mono font-extrabold">{{ $selectedSale->invoice_number }}</p>
                    </div>
                </div>

                <div class="space-y-4 text-xs">
                    <!-- Ticket Area for Print -->
                    <div id="printable-receipt" class="bg-white text-slate-900 p-6 rounded-xl font-mono text-xs shadow-inner space-y-3 border border-slate-200">
                        <div class="text-center border-b border-dashed border-slate-400 pb-3">
                            <h2 class="font-black text-base tracking-wider uppercase text-slate-950">BITA PHARMA</h2>
                            <p class="text-[10px] italic font-serif text-slate-700 font-bold mb-1">"La confiance au cœur de vos soins"</p>
                            <p class="text-[10px] font-bold text-slate-800">Tél: +243 80 88 58 326 / +243 99 45 50 510</p>
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
                                    <span class="truncate max-w-[200px] font-bold">{{ $item->quantity }}x {{ $item->product_name }}</span>
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
                        <button onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-[#00c9a7] hover:bg-[#00b899] text-white font-extrabold shadow-md flex items-center gap-2">
                            <i class="fa-solid fa-print"></i> Imprimer Ticket
                        </button>
                        <button wire:click="closeDetailsModal" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold border border-slate-300">
                            Fermer
                        </button>
                    </div>
                </div>

            </div>
        </div>
    @endif

</div>
