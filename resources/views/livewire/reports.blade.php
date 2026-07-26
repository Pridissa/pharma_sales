<div class="space-y-6">

    <!-- Header Actions & Period Filter -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 glass-panel p-4 rounded-3xl border border-slate-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center font-bold text-lg">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div>
                <h3 class="font-heading font-bold text-base text-white">Analyse Financière & Valorisation Stock</h3>
                <p class="text-[11px] text-slate-400">Rapports d'activité, marges brutes et suivi des périssables</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <select wire:model.live="period" class="px-3.5 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500 font-medium">
                <option value="today">Aujourd'hui</option>
                <option value="this_week">Cette Semaine</option>
                <option value="this_month">Ce Mois-ci</option>
                <option value="all">Tout l'historique</option>
            </select>

            <button 
                wire:click="downloadStockCsv" 
                class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-emerald-400 text-xs font-bold transition-all border border-slate-700 flex items-center gap-1.5"
                title="Exporter la valorisation du stock au format CSV"
            >
                <i class="fa-solid fa-file-csv text-sm"></i> Export Stock
            </button>

            <button 
                wire:click="downloadSalesCsv" 
                class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 text-xs font-bold transition-all shadow-md shadow-emerald-500/20 flex items-center gap-1.5"
                title="Exporter les ventes au format CSV"
            >
                <i class="fa-solid fa-download text-sm"></i> Export Ventes
            </button>

            <button 
                onclick="window.print()" 
                class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-all border border-slate-700"
                title="Imprimer le rapport de synthèse"
            >
                <i class="fa-solid fa-print"></i>
            </button>
        </div>
    </div>

    <!-- Stock Valuation Key Metrics Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Stock Purchase Value (HT / Coût) -->
        <div class="glass-panel p-4 rounded-3xl border border-slate-800 space-y-2 relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
                <span>Valorisation Stock (Achat)</span>
                <i class="fa-solid fa-box-archive text-emerald-400"></i>
            </div>
            <div class="text-xl font-extrabold text-white font-mono">
                {{ number_format($stockPurchaseValuation, 0, ',', ' ') }} FC
            </div>
            <div class="text-[10px] text-slate-400 flex justify-between pt-1 border-t border-slate-800/60">
                <span>Total Unités: <strong class="text-slate-200 font-mono">{{ $totalStockUnits }}</strong></span>
                <span>Médicaments: <strong class="text-slate-200 font-mono">{{ $totalProductsCount }}</strong></span>
            </div>
        </div>

        <!-- Stock Selling Value (TTC / Vente Potentielle) -->
        <div class="glass-panel p-4 rounded-3xl border border-slate-800 space-y-2 relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
                <span>Valeur Vente Théorique</span>
                <i class="fa-solid fa-hand-holding-dollar text-cyan-400"></i>
            </div>
            <div class="text-xl font-extrabold text-cyan-400 font-mono">
                {{ number_format($stockSellingValuation, 0, ',', ' ') }} FC
            </div>
            <div class="text-[10px] text-slate-400 pt-1 border-t border-slate-800/60">
                Marge théorique stock : <strong class="text-emerald-400 font-mono">+{{ number_format($potentialMargin, 0, ',', ' ') }} FC</strong>
            </div>
        </div>

        <!-- Revenue for Period -->
        <div class="glass-panel p-4 rounded-3xl border border-slate-800 space-y-2 relative overflow-hidden">
            <div class="flex items-center justify-between text-slate-400 text-xs font-semibold">
                <span>Chiffre d'Affaires Encaissé</span>
                <i class="fa-solid fa-receipt text-amber-400"></i>
            </div>
            <div class="text-xl font-extrabold text-amber-300 font-mono">
                {{ number_format($totalRevenue, 0, ',', ' ') }} FC
            </div>
            <div class="text-[10px] text-slate-400 flex justify-between pt-1 border-t border-slate-800/60 font-mono">
                <span>Espèces: {{ number_format($cashRevenue, 0, ',', ' ') }} FC</span>
                <span>Mobile: {{ number_format($mobileRevenue, 0, ',', ' ') }} FC</span>
            </div>
        </div>

        <!-- Gross Profit Realized -->
        <div class="glass-panel p-4 rounded-3xl border border-emerald-500/30 bg-emerald-500/5 space-y-2 relative overflow-hidden">
            <div class="flex items-center justify-between text-emerald-400 text-xs font-bold">
                <span>Marge Brute Réalisée</span>
                <i class="fa-solid fa-chart-pie text-emerald-400"></i>
            </div>
            <div class="text-xl font-extrabold text-emerald-300 font-mono">
                +{{ number_format($grossProfit, 0, ',', ' ') }} FC
            </div>
            <div class="text-[10px] text-emerald-400/80 pt-1 border-t border-emerald-500/20 font-bold">
                Taux de Marge Brute : <span class="text-white font-mono font-bold">{{ number_format($profitMarginRate, 1) }} %</span>
            </div>
        </div>
    </div>

    <!-- Main Content Section: Top Profit Products & Expiring Stock Risk -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Top Profit Products Table -->
        <div class="glass-panel p-5 rounded-3xl border border-slate-800 space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                <h4 class="font-heading font-bold text-sm text-white flex items-center gap-2">
                    <i class="fa-solid fa-trophy text-amber-400"></i> Produits les plus Rentables (Top Marge)
                </h4>
                <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Bénéfice Cumulé</span>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-800">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/80 text-[10px] uppercase font-bold text-slate-400">
                        <tr>
                            <th class="p-3">Médicament</th>
                            <th class="p-3">Prix Achat / Vente</th>
                            <th class="p-3 font-right">Marge Totale Générée</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($topMarginProducts as $prod)
                            <tr class="hover:bg-slate-800/40">
                                <td class="p-3 font-bold text-white">
                                    {{ $prod->name }}
                                    <span class="block text-[10px] font-normal text-slate-400 font-mono">{{ $prod->dci ?: 'Sans DCI' }}</span>
                                </td>
                                <td class="p-3 font-mono text-[11px]">
                                    <span class="text-slate-400">{{ number_format($prod->purchase_price, 0, ',', ' ') }}</span> ➔ <span class="text-emerald-400 font-bold">{{ number_format($prod->price, 0, ',', ' ') }} FC</span>
                                </td>
                                <td class="p-3 font-mono font-extrabold text-emerald-400 text-right">
                                    +{{ number_format($prod->total_profit, 0, ',', ' ') }} FC
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-6 text-center text-slate-500 italic">Aucune donnée de vente pour calculer la marge.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Perishables & Expiration Risk Breakdown -->
        <div class="glass-panel p-5 rounded-3xl border border-rose-500/30 bg-rose-500/5 space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-rose-500/20">
                <h4 class="font-heading font-bold text-sm text-white flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-rose-400"></i> Suivi des Lots Périssables (< 60 jours)
                </h4>
                <span class="px-2.5 py-0.5 rounded-full bg-rose-500/20 text-rose-300 font-mono font-bold text-xs">
                    Risque: {{ number_format($atRiskFinancialValue, 0, ',', ' ') }} FC
                </span>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-800">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/90 text-[10px] uppercase font-bold text-slate-400">
                        <tr>
                            <th class="p-3">Produit / N° Lot</th>
                            <th class="p-3">Péremption</th>
                            <th class="p-3">Qté en Stock</th>
                            <th class="p-3 text-right">Valeur à Risque</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80 font-mono">
                        @forelse($expiringBatches as $batch)
                            <tr class="hover:bg-slate-800/40">
                                <td class="p-3 font-sans">
                                    <span class="font-bold text-white block">{{ $batch->product->name }}</span>
                                    <span class="text-[10px] text-emerald-400 font-mono">{{ $batch->batch_number }}</span>
                                </td>
                                <td class="p-3 font-bold {{ $batch->isExpired() ? 'text-rose-400' : 'text-amber-400' }}">
                                    {{ $batch->expiration_date->format('d/m/Y') }}
                                </td>
                                <td class="p-3 font-bold text-white">{{ $batch->quantity }}</td>
                                <td class="p-3 text-right font-bold text-rose-300">
                                    {{ number_format($batch->quantity * $batch->purchase_price, 0, ',', ' ') }} FC
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-6 text-center text-slate-400 italic font-sans">
                                    <i class="fa-solid fa-circle-check text-emerald-400 text-lg mb-1 block"></i>
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
