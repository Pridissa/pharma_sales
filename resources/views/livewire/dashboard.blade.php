<div 
    x-data="{
        initCharts() {
            // Chart 1: Sales Trend (7 Days)
            const trendCtx = document.getElementById('salesTrendChart')?.getContext('2d');
            if (trendCtx) {
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: @js($salesTrendLabels),
                        datasets: [{
                            label: 'Chiffre d\'Affaires (FC)',
                            data: @js($salesTrendData),
                            borderColor: '#00c9a7',
                            backgroundColor: 'rgba(0, 201, 167, 0.12)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#00c9a7',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return ' ' + context.parsed.y.toLocaleString('fr-FR') + ' FC';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: { 
                                grid: { color: 'rgba(226, 232, 240, 0.6)' }, 
                                ticks: { font: { family: 'JetBrains Mono', size: 10, weight: 'bold' }, color: '#64748b' } 
                            },
                            x: { 
                                grid: { display: false }, 
                                ticks: { font: { family: 'Plus Jakarta Sans', size: 11, weight: 'bold' }, color: '#475569' } 
                            }
                        }
                    }
                });
            }

            // Chart 2: Category Sales Breakdown
            const categoryCtx = document.getElementById('categorySalesChart')?.getContext('2d');
            if (categoryCtx) {
                new Chart(categoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @js($categoryChartLabels),
                        datasets: [{
                            data: @js($categoryChartData),
                            backgroundColor: ['#00c9a7', '#05a88b', '#0ea5e9', '#f59e0b', '#ec4899', '#8b5cf6', '#64748b'],
                            borderWidth: 3,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { 
                                position: 'bottom', 
                                labels: { font: { family: 'Plus Jakarta Sans', size: 11, weight: 'bold' }, color: '#1e293b', padding: 12 } 
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return ' ' + context.label + ': ' + context.parsed.toLocaleString('fr-FR') + ' FC';
                                    }
                                }
                            }
                        },
                        cutout: '65%'
                    }
                });
            }
        }
    }"
    x-init="$nextTick(() => initCharts())"
    class="space-y-6"
>

    <!-- Dashboard Welcome Header with BITA PHARMA Logo (APPLOCK Style) -->
    <div class="bg-gradient-to-r from-[#182232] via-[#1d2b3f] to-[#152030] p-6 rounded-3xl border border-[#28384f] flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xl text-white">
        <div class="flex items-center gap-4">
            <img src="/logo.svg" class="w-14 h-14 rounded-2xl shadow-xl shadow-[#00c9a7]/30 border border-[#00c9a7]/40 object-cover shrink-0" alt="BITA PHARMA Logo">
            <div>
                <h2 class="font-heading font-extrabold text-2xl text-white tracking-wide flex items-center gap-2">
                    BITA <span class="text-[#00c9a7]">PHARMA</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-[#00c9a7]/20 text-[#00c9a7] text-xs font-mono font-bold border border-[#00c9a7]/30">v2.0 Pro</span>
                </h2>
                <p class="text-xs text-slate-300 mt-1">La confiance au cœur de vos soins — Supervision des ventes & des stocks</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('pos') }}" class="px-5 py-3 bg-[#00c9a7] hover:bg-[#00b899] text-white font-extrabold text-xs rounded-2xl shadow-lg shadow-[#00c9a7]/30 flex items-center gap-2 transition-all">
                <i class="fa-solid fa-cash-register"></i> Espace Caisse (POS)
            </a>
        </div>
    </div>

    <!-- ⚡ BARRE D'ACCÈS RAPIDE & ACTIONS DIRECTES -->
    <div class="space-y-2">
        <h3 class="text-xs uppercase tracking-wider font-extrabold text-slate-600 flex items-center gap-2">
            <i class="fa-solid fa-bolt text-[#00c9a7]"></i> Raccourcis & Actions Rapides
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            
            <a href="{{ route('pos') }}" class="bg-white p-3.5 rounded-2xl border border-[#d6f0ea] hover:border-[#00c9a7] shadow-sm hover:shadow-md transition-all flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-[#00c9a7]/15 text-[#00c9a7] flex items-center justify-center text-lg font-bold group-hover:bg-[#00c9a7] group-hover:text-white transition-all shrink-0">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs text-[#0f172a] group-hover:text-[#00c9a7] transition-colors">Vente POS</h4>
                    <span class="text-[10px] text-slate-500 font-bold block">Point de Vente</span>
                </div>
            </a>

            <a href="{{ route('cash-register') }}" class="bg-white p-3.5 rounded-2xl border border-[#d6f0ea] hover:border-[#00c9a7] shadow-sm hover:shadow-md transition-all flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-cyan-100 text-cyan-700 flex items-center justify-center text-lg font-bold group-hover:bg-cyan-600 group-hover:text-white transition-all shrink-0">
                    <i class="fa-solid fa-vault"></i>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs text-[#0f172a] group-hover:text-cyan-700 transition-colors">Session Caisse</h4>
                    <span class="text-[10px] text-slate-500 font-bold block">Z-de-Caisse</span>
                </div>
            </a>

            <a href="{{ route('products') }}" class="bg-white p-3.5 rounded-2xl border border-[#d6f0ea] hover:border-[#00c9a7] shadow-sm hover:shadow-md transition-all flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-lg font-bold group-hover:bg-emerald-600 group-hover:text-white transition-all shrink-0">
                    <i class="fa-solid fa-pills"></i>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs text-[#0f172a] group-hover:text-emerald-700 transition-colors">Médicaments</h4>
                    <span class="text-[10px] text-slate-500 font-bold block">Catalogue & Lots</span>
                </div>
            </a>

            <a href="{{ route('requisitions') }}" class="bg-white p-3.5 rounded-2xl border border-[#d6f0ea] hover:border-[#00c9a7] shadow-sm hover:shadow-md transition-all flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-lg font-bold group-hover:bg-amber-600 group-hover:text-white transition-all shrink-0">
                    <i class="fa-solid fa-boxes-packing"></i>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs text-[#0f172a] group-hover:text-amber-700 transition-colors">Réquisitions</h4>
                    <span class="text-[10px] text-slate-500 font-bold block">Commandes Stock</span>
                </div>
            </a>

            <a href="{{ route('reports') }}" class="bg-white p-3.5 rounded-2xl border border-[#d6f0ea] hover:border-[#00c9a7] shadow-sm hover:shadow-md transition-all flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-800 flex items-center justify-center text-lg font-bold group-hover:bg-indigo-600 group-hover:text-white transition-all shrink-0">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs text-[#0f172a] group-hover:text-indigo-700 transition-colors">Analytics</h4>
                    <span class="text-[10px] text-slate-500 font-bold block">Rapports Financiers</span>
                </div>
            </a>

        </div>
    </div>

    <!-- 💳 WIDGET STATUT CAISSE EN DIRECT -->
    <div class="bg-white p-5 rounded-3xl border border-[#d6f0ea] shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl {{ $currentSession ? 'bg-[#00c9a7]/15 text-[#00c9a7] border border-[#00c9a7]/30' : 'bg-rose-100 text-rose-600 border border-rose-200' }} flex items-center justify-center text-2xl font-extrabold shadow-sm shrink-0">
                <i class="fa-solid fa-cash-register"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-heading font-extrabold text-lg text-[#0f172a]">Statut Caisse en Direct</h3>
                    @if($currentSession)
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 text-[10px] font-extrabold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-[#00c9a7] animate-pulse"></span>
                            Session #{{ $currentSession->id }} Active
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 border border-rose-300 text-[10px] font-extrabold flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            Caisse Fermée
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 font-bold mt-0.5">
                    @if($currentSession)
                        Ouverte à {{ $currentSession->opened_at->format('H:i') }} par <strong>{{ auth()->user()->name }}</strong> (Fond initial: {{ number_format($currentSession->opening_balance, 0, ',', ' ') }} FC)
                    @else
                        Aucune session ouverte pour votre compte. Ouvrez une caisse pour démarrer les encaissements POS.
                    @endif
                </p>
            </div>
        </div>

        @if($currentSession)
            <div class="flex items-center gap-6 font-mono text-xs bg-slate-50 p-3.5 rounded-2xl border border-slate-200 shadow-sm">
                <div>
                    <span class="text-slate-500 font-bold block text-[10px] uppercase">Espèces</span>
                    <span class="font-black text-[#05a88b] text-base">{{ number_format($sessionCashTotal, 0, ',', ' ') }} FC</span>
                </div>
                <div class="w-px h-8 bg-slate-200"></div>
                <div>
                    <span class="text-slate-500 font-bold block text-[10px] uppercase">Mobile Money</span>
                    <span class="font-black text-cyan-700 text-base">{{ number_format($sessionMobileTotal, 0, ',', ' ') }} FC</span>
                </div>
                <div class="w-px h-8 bg-slate-200"></div>
                <div>
                    <span class="text-slate-500 font-bold block text-[10px] uppercase">Ventes</span>
                    <span class="font-black text-[#0f172a] text-base">{{ $sessionSalesCount }}</span>
                </div>
            </div>
        @else
            <a href="{{ route('pos') }}" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl shadow-sm transition-all flex items-center gap-2 whitespace-nowrap">
                <i class="fa-solid fa-key"></i> Ouvrir la Caisse POS
            </a>
        @endif
    </div>

    <!-- KPI Summary Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Card 1: Today Revenue -->
        <div class="bg-white p-5 rounded-3xl border border-[#d6f0ea] shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Recettes du Jour</p>
                    <h3 class="font-heading font-extrabold text-2xl text-slate-900 font-mono mt-1">
                        {{ number_format($todayRevenue, 0, ',', ' ') }} <span class="text-xs text-[#05a88b] font-bold">FC</span>
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-[#00c9a7]/15 text-[#00c9a7] border border-[#00c9a7]/30 flex items-center justify-center text-xl shadow-sm">
                    <i class="fa-solid fa-coins"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1.5 text-xs text-[#05a88b] font-bold">
                <i class="fa-solid fa-arrow-trend-up"></i>
                <span>Mise à jour en temps réel</span>
            </div>
        </div>

        <!-- Card 2: Today Sales Count -->
        <div class="bg-white p-5 rounded-3xl border border-[#d6f0ea] shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Ventes Effectuées</p>
                    <h3 class="font-heading font-extrabold text-2xl text-slate-900 font-mono mt-1">
                        {{ $todaySalesCount }} <span class="text-xs text-cyan-700 font-bold">Transactions</span>
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-cyan-100 text-cyan-700 border border-cyan-200 flex items-center justify-center text-xl shadow-sm">
                    <i class="fa-solid fa-cart-check"></i>
                </div>
            </div>
            <div class="mt-3 text-xs text-slate-500 font-bold">
                Aujourd'hui
            </div>
        </div>

        <!-- Card 3: Low Stock Alerts -->
        <div class="bg-white p-5 rounded-3xl border border-[#d6f0ea] shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Alertes Stock Faible</p>
                    <h3 class="font-heading font-extrabold text-2xl text-amber-700 font-mono mt-1">
                        {{ $lowStockCount }} <span class="text-xs text-amber-700 font-bold">Produits</span>
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 border border-amber-200 flex items-center justify-center text-xl shadow-sm">
                    <i class="fa-solid fa-boxes-packing"></i>
                </div>
            </div>
            <div class="mt-3 text-xs text-amber-700 font-bold">
                À réapprovisionner
            </div>
        </div>

        <!-- Card 4: Expiring Alert -->
        <div class="bg-white p-5 rounded-3xl border border-[#d6f0ea] shadow-sm relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Alerte Péremption (&lt;60j)</p>
                    <h3 class="font-heading font-extrabold text-2xl text-rose-600 font-mono mt-1">
                        {{ $expiringSoonCount }} <span class="text-xs text-rose-600 font-bold">Articles</span>
                    </h3>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-700 border border-rose-200 flex items-center justify-center text-xl shadow-sm">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>
            <div class="mt-3 text-xs text-rose-700 font-bold">
                Contrôle qualité requis
            </div>
        </div>

    </div>

    <!-- 📊 GRAPHIQUES INTERACTIFS D'ACTIVITÉ (CHARTS.JS) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Graphique 1: Évolution des Ventes (7 Derniers Jours) -->
        <div class="lg:col-span-2 bg-white p-5 rounded-3xl border border-[#d6f0ea] shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-200">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-[#00c9a7]/15 text-[#00c9a7] border border-[#00c9a7]/30 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-[#00c9a7] fa-chart-area"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-slate-900 text-base">Évolution du Chiffre d'Affaires (7 Derniers Jours)</h3>
                        <p class="text-[10px] text-slate-500 font-bold">Tendance des encaissements quotidiens</p>
                    </div>
                </div>
            </div>
            <div class="relative h-64 w-full flex-1">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

        <!-- Graphique 2: Répartition des Ventes par Catégorie -->
        <div class="bg-white p-5 rounded-3xl border border-[#d6f0ea] shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-200">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-cyan-100 text-cyan-700 border border-cyan-200 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-slate-900 text-base">Ventes par Catégorie</h3>
                        <p class="text-[10px] text-slate-500 font-bold">Part du CA par famille de médicaments</p>
                    </div>
                </div>
            </div>
            <div class="relative h-64 w-full flex-1 flex items-center justify-center">
                <canvas id="categorySalesChart"></canvas>
            </div>
        </div>

    </div>

    <!-- Sales Performance Analytics (Top & Flop Products) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Top Selling Products Widget -->
        <div class="bg-white p-5 rounded-3xl border border-[#d6f0ea] shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-200">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-fire"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-slate-900 text-base">Produits Les Plus Vendus</h3>
                        <p class="text-[10px] text-slate-500 font-bold">Classement par volume de ventes cumulé</p>
                    </div>
                </div>
            </div>

            <div class="space-y-2 flex-1">
                @forelse($topSellingProducts as $index => $prod)
                    <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-800 font-mono font-black text-xs flex items-center justify-center border border-emerald-300">
                                #{{ $index + 1 }}
                            </span>
                            <div>
                                <h4 class="text-xs font-extrabold text-[#0f172a]">{{ $prod->name }}</h4>
                                <p class="text-[11px] text-slate-500 font-semibold">DCI: {{ $prod->dci ?: 'N/A' }} • Prix: {{ number_format($prod->price, 0, ',', ' ') }} FC</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-2.5 py-1 rounded-xl text-xs font-black font-mono bg-emerald-100 text-emerald-800 border border-emerald-300">
                                {{ $prod->total_sold }} vendu(s)
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-slate-500 py-8 text-xs font-bold">
                        <i class="fa-solid fa-chart-line text-2xl text-slate-400 mb-2"></i>
                        <p>Aucune donnée de vente enregistrée.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Least Selling / Unsold Products Widget -->
        <div class="bg-white p-5 rounded-3xl border border-[#d6f0ea] shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-200">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-cyan-100 text-cyan-800 border border-cyan-300 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-snowflake"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-slate-900 text-base">Produits Les Moins Vendus</h3>
                        <p class="text-[10px] text-slate-500 font-bold">Identification des stocks à faible rotation / dormants</p>
                    </div>
                </div>
            </div>

            <div class="space-y-2 flex-1">
                @forelse($leastSellingProducts as $prod)
                    <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-extrabold text-[#0f172a]">{{ $prod->name }}</h4>
                            <p class="text-[11px] text-slate-500 font-semibold">Stock actuel: {{ $prod->stock_quantity }} • Prix: {{ number_format($prod->price, 0, ',', ' ') }} FC</p>
                        </div>
                        <div class="text-right">
                            <span class="px-2.5 py-1 rounded-xl text-xs font-black font-mono {{ $prod->total_sold == 0 ? 'bg-rose-100 text-rose-800 border border-rose-300' : 'bg-slate-100 text-slate-700 border border-slate-300' }}">
                                {{ $prod->total_sold }} vendu(s)
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-slate-500 py-8 text-xs font-bold">
                        <p>Aucun produit enregistré dans le catalogue.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Alert Tables Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Stock Alert List -->
        <div class="bg-white p-5 rounded-3xl border border-[#d6f0ea] shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-200">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 border border-amber-300 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <h3 class="font-heading font-extrabold text-slate-900 text-base">Ruptures & Stocks Critiques</h3>
                </div>
                <a href="{{ route('products') }}" class="text-xs text-[#00c9a7] hover:underline font-extrabold">Voir tout</a>
            </div>

            <div class="space-y-2 flex-1">
                @forelse($lowStockProducts as $prod)
                    <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-extrabold text-[#0f172a]">{{ $prod->name }}</h4>
                            <p class="text-[11px] text-slate-500 font-semibold">{{ $prod->category->name }} • {{ $prod->dosage_unit }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-black font-mono {{ $prod->stock_quantity <= 0 ? 'bg-rose-100 text-rose-800 border border-rose-300' : 'bg-amber-100 text-amber-800 border border-amber-300' }}">
                                Stock: {{ $prod->stock_quantity }} / {{ $prod->min_stock_alert }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-slate-500 py-8 text-xs font-bold">
                        <i class="fa-solid fa-check-double text-2xl text-[#00c9a7] mb-2"></i>
                        <p>Aucune alerte de stock critique actuellement.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Expiring Soon Products List -->
        <div class="bg-white p-5 rounded-3xl border border-[#d6f0ea] shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-200">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-800 border border-rose-300 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-calendar-xmark"></i>
                    </div>
                    <h3 class="font-heading font-extrabold text-slate-900 text-base">Péremptions Imminentes</h3>
                </div>
                <a href="{{ route('products') }}" class="text-xs text-[#00c9a7] hover:underline font-extrabold">Voir tout</a>
            </div>

            <div class="space-y-2 flex-1">
                @forelse($expiringProducts as $prod)
                    <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-extrabold text-[#0f172a]">{{ $prod->name }}</h4>
                            <p class="text-[11px] text-slate-500 font-semibold">DCI: {{ $prod->dci ?: 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-2.5 py-1 rounded-lg text-xs font-black font-mono {{ $prod->isExpired() ? 'bg-rose-600 text-white' : 'bg-rose-100 text-rose-800 border border-rose-300' }}">
                                Péremption: {{ $prod->expiration_date->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-slate-500 py-8 text-xs font-bold">
                        <i class="fa-solid fa-shield-halved text-2xl text-[#00c9a7] mb-2"></i>
                        <p>Aucun produit périmé ou proche de péremption.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Recent Transactions Table with Cashier Traceability -->
    <div class="bg-white p-5 rounded-3xl border border-[#d6f0ea] shadow-sm">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-200">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <h3 class="font-heading font-extrabold text-slate-900 text-base">Dernières Ventes Enregistrées</h3>
            </div>
            <a href="{{ route('sales-history') }}" class="text-xs text-[#00c9a7] hover:underline font-extrabold">Historique complet</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800">
                <thead class="bg-slate-100 uppercase font-extrabold text-slate-600 text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="p-3">N° Facture</th>
                        <th class="p-3">Vendeur / Caissier</th>
                        <th class="p-3">Date & Heure</th>
                        <th class="p-3">Articles</th>
                        <th class="p-3">Règlement</th>
                        <th class="p-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentSales as $sale)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-3 font-mono font-black text-[#05a88b]">{{ $sale->invoice_number }}</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded-lg bg-emerald-100 text-emerald-800 border border-emerald-300 text-[11px] font-extrabold flex items-center gap-1.5 w-fit">
                                    <i class="fa-solid fa-user-check text-[10px]"></i>
                                    {{ $sale->user ? $sale->user->name : 'N/A' }}
                                </span>
                            </td>
                            <td class="p-3 text-slate-600 font-bold">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            <td class="p-3 text-slate-700 font-semibold">{{ $sale->items->count() }} produit(s)</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-bold border border-slate-200">
                                    {{ $sale->payment_method }}
                                </span>
                            </td>
                            <td class="p-3 text-right font-mono font-black text-[#0f172a]">
                                {{ number_format($sale->total_amount, 0, ',', ' ') }} FC
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-500 font-bold">Aucune vente enregistrée pour le moment.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
