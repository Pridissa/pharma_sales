<div class="space-y-6">

    <!-- Dashboard Welcome Header with Application Logo -->
    <div class="glass-panel p-5 rounded-3xl border border-[#2a3942] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <img src="/logo.svg" class="w-14 h-14 rounded-2xl shadow-xl shadow-[#00a884]/30 border border-[#00a884]/40 object-cover shrink-0" alt="PharmaSales Logo">
            <div>
                <h2 class="font-heading font-extrabold text-xl text-white tracking-wide flex items-center gap-2">
                    PHARMA <span class="text-[#00a884]">SALES</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-[#00a884]/20 text-[#00a884] text-xs font-mono font-bold border border-[#00a884]/30">v2.0 Pro</span>
                </h2>
                <p class="text-xs text-[#8696a0] mt-0.5">Tableau de bord de gestion pharmaceutique & supervision des ventes</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('pos') }}" class="px-4 py-2.5 bg-gradient-to-r from-[#00a884] to-[#128c7e] hover:from-[#25d366] hover:to-[#00a884] text-slate-950 font-extrabold text-xs rounded-2xl shadow-lg shadow-[#00a884]/20 flex items-center gap-2 transition-all">
                <i class="fa-solid fa-cash-register"></i> Aller à la Caisse (POS)
            </a>
        </div>
    </div>

    <!-- KPI Summary Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Card 1: Today Revenue -->
        <div class="glass-panel p-5 rounded-3xl border border-slate-800/80 relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all"></div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Recettes du Jour</p>
                    <h3 class="font-heading font-extrabold text-2xl text-white font-mono mt-1">
                        {{ number_format($todayRevenue, 0, ',', ' ') }} <span class="text-xs text-emerald-400">FC</span>
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-xl shadow-lg shadow-emerald-500/10">
                    <i class="fa-solid fa-coins"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1.5 text-xs text-emerald-400 font-medium">
                <i class="fa-solid fa-arrow-trend-up"></i>
                <span>Mise à jour en temps réel</span>
            </div>
        </div>

        <!-- Card 2: Today Sales Count -->
        <div class="glass-panel p-5 rounded-3xl border border-slate-800/80 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Ventes Effectuées</p>
                    <h3 class="font-heading font-extrabold text-2xl text-white font-mono mt-1">
                        {{ $todaySalesCount }} <span class="text-xs text-cyan-400">Transactions</span>
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 flex items-center justify-center text-xl shadow-lg shadow-cyan-500/10">
                    <i class="fa-solid fa-cart-check"></i>
                </div>
            </div>
            <div class="mt-3 text-xs text-slate-400">
                Aujourd'hui
            </div>
        </div>

        <!-- Card 3: Low Stock Alerts -->
        <div class="glass-panel p-5 rounded-3xl border border-slate-800/80 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Alertes Stock Faible</p>
                    <h3 class="font-heading font-extrabold text-2xl text-amber-400 font-mono mt-1">
                        {{ $lowStockCount }} <span class="text-xs text-amber-300">Produits</span>
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-400 border border-amber-500/30 flex items-center justify-center text-xl shadow-lg shadow-amber-500/10">
                    <i class="fa-solid fa-boxes-packing"></i>
                </div>
            </div>
            <div class="mt-3 text-xs text-amber-300/80 font-medium">
                À réapprovisionner
            </div>
        </div>

        <!-- Card 4: Expiring Alert -->
        <div class="glass-panel p-5 rounded-3xl border border-slate-800/80 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Alerte Péremption (&lt;60j)</p>
                    <h3 class="font-heading font-extrabold text-2xl text-rose-400 font-mono mt-1">
                        {{ $expiringSoonCount }} <span class="text-xs text-rose-300">Articles</span>
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-500/20 text-rose-400 border border-rose-500/30 flex items-center justify-center text-xl shadow-lg shadow-rose-500/10">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>
            <div class="mt-3 text-xs text-rose-300/80 font-medium">
                Contrôle qualité requis
            </div>
        </div>

    </div>

    <!-- Sales Performance Analytics (Top & Flop Products) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Top Selling Products Widget -->
        <div class="glass-panel p-5 rounded-3xl border border-slate-800/80 flex flex-col">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-fire"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-white text-base">Produits Les Plus Vendus</h3>
                        <p class="text-[10px] text-slate-400">Classement par volume de ventes cumulé</p>
                    </div>
                </div>
            </div>

            <div class="space-y-2 flex-1">
                @forelse($topSellingProducts as $index => $prod)
                    <div class="glass-card p-3 rounded-2xl border border-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-lg bg-emerald-500/20 text-emerald-400 font-mono font-bold text-xs flex items-center justify-center border border-emerald-500/30">
                                #{{ $index + 1 }}
                            </span>
                            <div>
                                <h4 class="text-xs font-bold text-slate-100">{{ $prod->name }}</h4>
                                <p class="text-[11px] text-slate-400">DCI: {{ $prod->dci ?: 'N/A' }} • Prix: {{ number_format($prod->price, 0, ',', ' ') }} FC</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-2.5 py-1 rounded-xl text-xs font-extrabold font-mono bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                {{ $prod->total_sold }} vendu(s)
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-slate-400 py-8 text-xs">
                        <i class="fa-solid fa-chart-line text-2xl text-slate-600 mb-2"></i>
                        <p>Aucune donnée de vente enregistrée.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Least Selling / Unsold Products Widget -->
        <div class="glass-panel p-5 rounded-3xl border border-slate-800/80 flex flex-col">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-snowflake"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-white text-base">Produits Les Moins Vendus</h3>
                        <p class="text-[10px] text-slate-400">Identification des stocks à faible rotation / dormants</p>
                    </div>
                </div>
            </div>

            <div class="space-y-2 flex-1">
                @forelse($leastSellingProducts as $prod)
                    <div class="glass-card p-3 rounded-2xl border border-slate-800 flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-bold text-slate-100">{{ $prod->name }}</h4>
                            <p class="text-[11px] text-slate-400">Stock actuel: {{ $prod->stock_quantity }} • Prix: {{ number_format($prod->price, 0, ',', ' ') }} FC</p>
                        </div>
                        <div class="text-right">
                            <span class="px-2.5 py-1 rounded-xl text-xs font-bold font-mono {{ $prod->total_sold == 0 ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : 'bg-slate-800 text-slate-300' }}">
                                {{ $prod->total_sold }} vendu(s)
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-slate-400 py-8 text-xs">
                        <p>Aucun produit enregistré dans le catalogue.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Alert Tables Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Stock Alert List -->
        <div class="glass-panel p-5 rounded-3xl border border-slate-800/80 flex flex-col">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <h3 class="font-heading font-bold text-white text-base">Ruptures & Stocks Critiques</h3>
                </div>
                <a href="{{ route('products') }}" class="text-xs text-emerald-400 hover:underline font-medium">Voir tout</a>
            </div>

            <div class="space-y-2 flex-1">
                @forelse($lowStockProducts as $prod)
                    <div class="glass-card p-3 rounded-2xl border border-slate-800 flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-bold text-slate-100">{{ $prod->name }}</h4>
                            <p class="text-[11px] text-slate-400">{{ $prod->category->name }} • {{ $prod->dosage_unit }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold font-mono {{ $prod->stock_quantity <= 0 ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' }}">
                                Stock: {{ $prod->stock_quantity }} / {{ $prod->min_stock_alert }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-slate-400 py-8 text-xs">
                        <i class="fa-solid fa-check-double text-2xl text-emerald-500 mb-2"></i>
                        <p>Aucune alerte de stock critique actuellement.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Expiring Soon Products List -->
        <div class="glass-panel p-5 rounded-3xl border border-slate-800/80 flex flex-col">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-calendar-xmark"></i>
                    </div>
                    <h3 class="font-heading font-bold text-white text-base">Péremptions Imminentes</h3>
                </div>
                <a href="{{ route('products') }}" class="text-xs text-emerald-400 hover:underline font-medium">Voir tout</a>
            </div>

            <div class="space-y-2 flex-1">
                @forelse($expiringProducts as $prod)
                    <div class="glass-card p-3 rounded-2xl border border-slate-800 flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-bold text-slate-100">{{ $prod->name }}</h4>
                            <p class="text-[11px] text-slate-400">DCI: {{ $prod->dci ?: 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold font-mono {{ $prod->isExpired() ? 'bg-rose-500 text-white' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30' }}">
                                Péremption: {{ $prod->expiration_date->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-slate-400 py-8 text-xs">
                        <i class="fa-solid fa-shield-halved text-2xl text-emerald-500 mb-2"></i>
                        <p>Aucun produit périmé ou proche de péremption.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Recent Transactions Table with Cashier Traceability -->
    <div class="glass-panel p-5 rounded-3xl border border-slate-800/80">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <h3 class="font-heading font-bold text-white text-base">Dernières Ventes Enregistrées</h3>
            </div>
            <a href="{{ route('sales-history') }}" class="text-xs text-emerald-400 hover:underline font-medium">Historique complet</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-900/60 uppercase font-semibold text-slate-400 text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="p-3">N° Facture</th>
                        <th class="p-3">Vendeur / Caissier</th>
                        <th class="p-3">Date & Heure</th>
                        <th class="p-3">Articles</th>
                        <th class="p-3">Règlement</th>
                        <th class="p-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($recentSales as $sale)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="p-3 font-mono font-bold text-emerald-400">{{ $sale->invoice_number }}</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 text-[11px] font-semibold flex items-center gap-1.5 w-fit">
                                    <i class="fa-solid fa-user-check text-[10px]"></i>
                                    {{ $sale->user ? $sale->user->name : 'N/A' }}
                                </span>
                            </td>
                            <td class="p-3 text-slate-400">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3">{{ $sale->items->count() }} produit(s)</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 text-[10px]">
                                    {{ $sale->payment_method }}
                                </span>
                            </td>
                            <td class="p-3 text-right font-mono font-bold text-white">
                                {{ number_format($sale->total_amount, 0, ',', ' ') }} FC
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-400">Aucune vente enregistrée pour le moment.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
