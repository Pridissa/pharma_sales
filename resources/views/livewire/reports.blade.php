<div class="space-y-6">

    <!-- Header Actions & Period Filter -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 rounded-3xl border border-[#d6f0ea] shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#00c9a7]/15 text-[#00c9a7] border border-[#00c9a7]/30 flex items-center justify-center font-extrabold text-lg">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div>
                <h3 class="font-heading font-extrabold text-base text-[#0f172a]">Analyse Financière & Valorisation Stock</h3>
                <p class="text-[11px] text-slate-500 font-semibold">Rapports d'activité, marges brutes et suivi des périssables</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <select wire:model.live="period" class="px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs font-extrabold text-[#0f172a] focus:outline-none focus:border-[#00c9a7]">
                <option value="today">Aujourd'hui</option>
                <option value="this_week">Cette Semaine</option>
                <option value="this_month">Ce Mois-ci</option>
                <option value="all">Tout l'historique</option>
            </select>

            <button 
                wire:click="downloadStockCsv" 
                class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-[#0f172a] text-xs font-extrabold transition-all border border-slate-300 flex items-center gap-1.5"
                title="Exporter la valorisation du stock au format CSV"
            >
                <i class="fa-solid fa-file-csv text-sm text-[#00c9a7]"></i> Export Stock
            </button>

            <button 
                wire:click="downloadSalesCsv" 
                class="px-3.5 py-2 rounded-xl bg-[#00c9a7] hover:bg-[#00b899] text-white text-xs font-extrabold transition-all shadow-md flex items-center gap-1.5"
                title="Exporter les ventes au format CSV"
            >
                <i class="fa-solid fa-download text-sm"></i> Export Ventes
            </button>

            <button 
                onclick="window.print()" 
                class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all border border-slate-300"
                title="Imprimer le rapport de synthèse"
            >
                <i class="fa-solid fa-print"></i>
            </button>
        </div>
    </div>

    <!-- Stock Valuation Key Metrics Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Stock Purchase Value (HT / Coût) -->
        <div class="bg-white p-4 rounded-3xl border border-[#d6f0ea] space-y-2 relative overflow-hidden shadow-sm">
            <div class="flex items-center justify-between text-slate-500 text-xs font-extrabold">
                <span>Valorisation Stock (Achat)</span>
                <i class="fa-solid fa-box-archive text-[#00c9a7]"></i>
            </div>
            <div class="text-xl font-black text-[#0f172a] font-mono">
                {{ number_format($stockPurchaseValuation, 0, ',', ' ') }} FC
            </div>
            <div class="text-[10px] text-slate-500 font-bold flex justify-between pt-1 border-t border-slate-100">
                <span>Total Unités: <strong class="text-slate-800 font-mono">{{ $totalStockUnits }}</strong></span>
                <span>Médicaments: <strong class="text-slate-800 font-mono">{{ $totalProductsCount }}</strong></span>
            </div>
        </div>

        <!-- Stock Selling Value (TTC / Vente Potentielle) -->
        <div class="bg-white p-4 rounded-3xl border border-[#d6f0ea] space-y-2 relative overflow-hidden shadow-sm">
            <div class="flex items-center justify-between text-slate-500 text-xs font-extrabold">
                <span>Valeur Vente Théorique</span>
                <i class="fa-solid fa-hand-holding-dollar text-cyan-600"></i>
            </div>
            <div class="text-xl font-black text-cyan-700 font-mono">
                {{ number_format($stockSellingValuation, 0, ',', ' ') }} FC
            </div>
            <div class="text-[10px] text-slate-500 font-bold pt-1 border-t border-slate-100">
                Marge théorique stock : <strong class="text-[#05a88b] font-mono">+{{ number_format($potentialMargin, 0, ',', ' ') }} FC</strong>
            </div>
        </div>

        <!-- Revenue for Period -->
        <div class="bg-white p-4 rounded-3xl border border-[#d6f0ea] space-y-2 relative overflow-hidden shadow-sm">
            <div class="flex items-center justify-between text-slate-500 text-xs font-extrabold">
                <span>Chiffre d'Affaires Encaissé</span>
                <i class="fa-solid fa-receipt text-amber-600"></i>
            </div>
            <div class="text-xl font-black text-amber-700 font-mono">
                {{ number_format($totalRevenue, 0, ',', ' ') }} FC
            </div>
            <div class="text-[10px] text-slate-500 font-bold flex justify-between pt-1 border-t border-slate-100 font-mono">
                <span>Espèces: {{ number_format($cashRevenue, 0, ',', ' ') }} FC</span>
                <span>Mobile: {{ number_format($mobileRevenue, 0, ',', ' ') }} FC</span>
            </div>
        </div>

        <!-- Gross Profit Realized -->
        <div class="bg-[#e6f9f5] p-4 rounded-3xl border border-[#00c9a7]/40 space-y-2 relative overflow-hidden shadow-sm">
            <div class="flex items-center justify-between text-[#05a88b] text-xs font-black">
                <span>Marge Brute Réalisée</span>
                <i class="fa-solid fa-chart-pie text-[#00c9a7]"></i>
            </div>
            <div class="text-xl font-black text-[#05a88b] font-mono">
                +{{ number_format($grossProfit, 0, ',', ' ') }} FC
            </div>
            <div class="text-[10px] text-[#05a88b] pt-1 border-t border-[#00c9a7]/20 font-extrabold">
                Taux de Marge Brute : <span class="text-[#0f172a] font-mono font-black">{{ number_format($profitMarginRate, 1) }} %</span>
            </div>
        </div>
    </div>

    <!-- Main Content Section: Top Profit Products & Expiring Stock Risk -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Top Profit Products Table -->
        <div class="bg-white p-5 rounded-3xl border border-[#d6f0ea] space-y-4 shadow-sm">
            <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                <h4 class="font-heading font-extrabold text-sm text-[#0f172a] flex items-center gap-2">
                    <i class="fa-solid fa-trophy text-amber-600"></i> Produits les plus Rentables (Top Marge)
                </h4>
                <span class="text-[10px] text-slate-500 uppercase font-extrabold tracking-wider">Bénéfice Cumulé</span>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs text-slate-800">
                    <thead class="bg-slate-100 text-[10px] uppercase font-extrabold text-slate-600">
                        <tr>
                            <th class="p-3">Médicament</th>
                            <th class="p-3">Prix Achat / Vente</th>
                            <th class="p-3 text-right">Marge Totale Générée</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($topMarginProducts as $prod)
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 font-extrabold text-[#0f172a]">
                                    {{ $prod->name }}
                                    <span class="block text-[10px] font-semibold text-slate-500 font-mono">{{ $prod->dci ?: 'Sans DCI' }}</span>
                                </td>
                                <td class="p-3 font-mono text-[11px] font-bold">
                                    <span class="text-slate-500">{{ number_format($prod->purchase_price, 0, ',', ' ') }}</span> ➔ <span class="text-[#05a88b] font-black">{{ number_format($prod->price, 0, ',', ' ') }} FC</span>
                                </td>
                                <td class="p-3 font-mono font-black text-[#05a88b] text-right">
                                    +{{ number_format($prod->total_profit, 0, ',', ' ') }} FC
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-6 text-center text-slate-500 italic font-bold">Aucune donnée de vente pour calculer la marge.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Perishables & Expiration Risk Breakdown -->
        <div class="bg-rose-50 p-5 rounded-3xl border border-rose-200 space-y-4 shadow-sm">
            <div class="flex items-center justify-between pb-2 border-b border-rose-200">
                <h4 class="font-heading font-extrabold text-sm text-[#0f172a] flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-rose-600"></i> Suivi des Lots Périssables (< 60 jours)
                </h4>
                <span class="px-2.5 py-0.5 rounded-full bg-rose-600 text-white font-mono font-extrabold text-xs">
                    Risque: {{ number_format($atRiskFinancialValue, 0, ',', ' ') }} FC
                </span>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-rose-200 bg-white">
                <table class="w-full text-left text-xs text-slate-800">
                    <thead class="bg-slate-100 text-[10px] uppercase font-extrabold text-slate-600">
                        <tr>
                            <th class="p-3">Produit / N° Lot</th>
                            <th class="p-3">Péremption</th>
                            <th class="p-3">Qté en Stock</th>
                            <th class="p-3 text-right">Valeur à Risque</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono">
                        @forelse($expiringBatches as $batch)
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 font-sans">
                                    <span class="font-extrabold text-[#0f172a] block">{{ $batch->product->name }}</span>
                                    <span class="text-[10px] text-[#05a88b] font-mono font-bold">{{ $batch->batch_number }}</span>
                                </td>
                                <td class="p-3 font-black {{ $batch->isExpired() ? 'text-rose-600' : 'text-amber-700' }}">
                                    {{ $batch->expiration_date->format('d/m/Y') }}
                                </td>
                                <td class="p-3 font-black text-[#0f172a]">{{ $batch->quantity }}</td>
                                <td class="p-3 text-right font-black text-rose-600">
                                    {{ number_format($batch->quantity * $batch->purchase_price, 0, ',', ' ') }} FC
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-6 text-center text-slate-500 italic font-sans font-bold">
                                    <i class="fa-solid fa-circle-check text-[#00c9a7] text-lg mb-1 block"></i>
                                    Aucun lot proche de la péremption dans les 60 prochains jours.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
