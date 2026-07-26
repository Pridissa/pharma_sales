<!DOCTYPE html>
<html lang="fr" class="h-full bg-[#0b141a] text-[#e9edef]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'PharmaSales - Gestion Pharmaceutique' }}</title>

    <!-- App Favicon & Icons -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="shortcut icon" href="/favicon.svg">

    <!-- Google Fonts: Plus Jakarta Sans (UI), Outfit (Headings), JetBrains Mono (Numbers/Money) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,400..800;1,400..800&family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS Play CDN & FontAwesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Flatpickr Datepicker CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'system-ui', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        wa: {
                            dark: '#0b141a',
                            panel: '#111b21',
                            card: '#202c33',
                            border: '#2a3942',
                            primary: '#00a884',
                            accent: '#25d366',
                            teal: '#128c7e',
                            cyan: '#34b7f1',
                            text: '#e9edef',
                            subtext: '#8696a0',
                        },
                        emerald: {
                            400: '#25d366',
                            500: '#00a884',
                            600: '#075e54',
                            700: '#128c7e',
                            900: '#0b141a',
                            950: '#050a0e',
                        },
                        pharma: {
                            dark: '#0b141a',
                            sidebar: '#111b21',
                            card: '#202c33',
                            accent: '#00a884',
                            cyan: '#34b7f1',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body { 
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            background-color: #0b141a;
            color: #e9edef;
        }
        h1, h2, h3, h4, h5, h6 { 
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.015em;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
            font-variant-numeric: tabular-nums;
        }
        .glass-panel {
            background: rgba(32, 44, 51, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(42, 57, 66, 0.8);
        }
        .glass-card {
            background: rgba(27, 38, 45, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(42, 57, 66, 0.6);
        }
        .scrollbar-thin::-webkit-scrollbar { width: 6px; height: 6px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: rgba(11, 20, 26, 0.6); }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: rgba(0, 168, 132, 0.4); border-radius: 3px; }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover { background: rgba(0, 168, 132, 0.8); }

        @media print {
            body { background: #fff !important; color: #000 !important; }
            body > * { display: none !important; }
            #printable-receipt-modal { 
                display: block !important; 
                position: absolute !important; 
                left: 0 !important; 
                top: 0 !important; 
                width: 100% !important; 
                height: auto !important; 
                background: #ffffff !important; 
                color: #000000 !important; 
                padding: 0 !important; 
                margin: 0 !important;
                border: none !important;
                box-shadow: none !important;
            }
            #printable-receipt-modal * {
                visibility: visible !important;
                color: #000000 !important;
                background: transparent !important;
            }
            .no-print-element { display: none !important; }
        }
    </style>
    @livewireStyles
</head>
<body class="h-full bg-[#0b141a] text-[#e9edef] flex overflow-hidden" x-data="{ collapsed: localStorage.getItem('sidebar_collapsed') === 'true' }">

    <!-- Sidebar Navbar -->
    <aside 
        :class="collapsed ? 'w-20' : 'w-64'" 
        class="bg-[#111b21] border-r border-[#2a3942] flex flex-col justify-between p-3.5 z-20 select-none transition-all duration-300 ease-in-out shrink-0"
    >
        <div>
            <!-- Brand Logo & Collapse Toggle Button -->
            <div class="flex items-center justify-between px-1 py-3 mb-5 border-b border-[#2a3942] gap-2">
                <div class="flex items-center gap-3 overflow-hidden">
                    <img src="/logo.svg" class="w-10 h-10 rounded-xl shadow-lg shadow-[#00a884]/30 border border-[#00a884]/40 shrink-0 object-cover" alt="PharmaSales Logo">
                    <div x-show="!collapsed" x-transition.opacity class="overflow-hidden whitespace-nowrap">
                        <h1 class="font-heading font-extrabold text-lg text-white tracking-wide leading-none">PHARMA <span class="text-[#00a884]">SALES</span></h1>
                        <span class="text-[9px] text-[#00a884]/90 tracking-tight font-medium block mt-0.5 italic">La confiance au cœur de vos soins</span>
                    </div>
                </div>

                <!-- Fold / Unfold Toggle Button -->
                <button 
                    @click="collapsed = !collapsed; localStorage.setItem('sidebar_collapsed', collapsed)" 
                    class="w-8 h-8 rounded-xl bg-[#202c33] hover:bg-[#2a3942] border border-[#2a3942] text-[#8696a0] hover:text-[#00a884] flex items-center justify-center transition-colors shrink-0"
                    :title="collapsed ? 'Déplier le menu' : 'Plier le menu'"
                >
                    <i class="fa-solid" :class="collapsed ? 'fa-indent text-[#00a884] text-base' : 'fa-outdent'"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-1.5">
                <!-- Links accessible to all authenticated users (Caissier & Admin) -->
                <a 
                    href="{{ route('pos') }}" 
                    :title="collapsed ? 'Espace Caisse / POS' : ''"
                    class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 text-sm font-medium {{ request()->routeIs('pos') ? 'bg-[#00a884]/20 text-[#00a884] border border-[#00a884]/40 shadow-md shadow-[#00a884]/10 font-bold' : 'text-[#8696a0] hover:text-[#e9edef] hover:bg-[#202c33]' }}"
                >
                    <i class="fa-solid fa-cash-register text-lg w-6 text-center shrink-0"></i>
                    <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Espace Caisse / POS</span>
                </a>

                <!-- Transactions & Historique Ventes -->
                <a 
                    href="{{ route('sales-history') }}" 
                    :title="collapsed ? 'Transactions & Historique Ventes' : ''"
                    class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 text-sm font-medium {{ request()->routeIs('sales-history') ? 'bg-[#00a884]/20 text-[#00a884] border border-[#00a884]/40 shadow-md shadow-[#00a884]/10 font-bold' : 'text-[#8696a0] hover:text-[#e9edef] hover:bg-[#202c33]' }}"
                >
                    <i class="fa-solid fa-arrow-right-arrow-left text-lg w-6 text-center shrink-0 text-[#34b7f1]"></i>
                    <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Transactions & Ventes</span>
                </a>

                <a 
                    href="{{ route('requisitions') }}" 
                    :title="collapsed ? 'Réquisitions & Demandes' : ''"
                    class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 text-sm font-medium {{ request()->routeIs('requisitions') ? 'bg-[#00a884]/20 text-[#00a884] border border-[#00a884]/40 shadow-md shadow-[#00a884]/10 font-bold' : 'text-[#8696a0] hover:text-[#e9edef] hover:bg-[#202c33]' }}"
                >
                    <i class="fa-solid fa-cart-flatbed text-lg w-6 text-center shrink-0"></i>
                    <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Réquisitions & Demandes</span>
                </a>

                <a 
                    href="{{ route('cash-register') }}" 
                    :title="collapsed ? 'Sessions Caisse & Audit' : ''"
                    class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 text-sm font-medium {{ request()->routeIs('cash-register') ? 'bg-[#00a884]/20 text-[#00a884] border border-[#00a884]/40 shadow-md shadow-[#00a884]/10 font-bold' : 'text-[#8696a0] hover:text-[#e9edef] hover:bg-[#202c33]' }}"
                >
                    <i class="fa-solid fa-vault text-lg w-6 text-center shrink-0 text-amber-400"></i>
                    <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Sessions Caisse & Audit</span>
                </a>

                <!-- Links restricted to Admin only -->
                @if(auth()->check() && auth()->user()->isAdmin())
                    <div x-show="!collapsed" x-transition.opacity class="pt-3 pb-1 px-3">
                        <span class="text-[9px] font-extrabold text-[#8696a0] uppercase tracking-widest">Administration</span>
                    </div>

                    <a 
                        href="{{ route('dashboard') }}" 
                        :title="collapsed ? 'Tableau de Bord' : ''"
                        class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-[#00a884]/20 text-[#00a884] border border-[#00a884]/40 shadow-md shadow-[#00a884]/10 font-bold' : 'text-[#8696a0] hover:text-[#e9edef] hover:bg-[#202c33]' }}"
                    >
                        <i class="fa-solid fa-chart-pie text-lg w-6 text-center shrink-0"></i>
                        <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Tableau de Bord</span>
                    </a>

                    <a 
                        href="{{ route('reports') }}" 
                        :title="collapsed ? 'Rapports & Analytics' : ''"
                        class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 text-sm font-medium {{ request()->routeIs('reports') ? 'bg-[#00a884]/20 text-[#00a884] border border-[#00a884]/40 shadow-md shadow-[#00a884]/10 font-bold' : 'text-[#8696a0] hover:text-[#e9edef] hover:bg-[#202c33]' }}"
                    >
                        <i class="fa-solid fa-chart-line text-lg w-6 text-center shrink-0 text-[#00a884]"></i>
                        <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Rapports & Analytics</span>
                    </a>

                    <a 
                        href="{{ route('products') }}" 
                        :title="collapsed ? 'Stock & Produits' : ''"
                        class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 text-sm font-medium {{ request()->routeIs('products') ? 'bg-[#00a884]/20 text-[#00a884] border border-[#00a884]/40 shadow-md shadow-[#00a884]/10 font-bold' : 'text-[#8696a0] hover:text-[#e9edef] hover:bg-[#202c33]' }}"
                    >
                        <i class="fa-solid fa-pills text-lg w-6 text-center shrink-0"></i>
                        <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Stock & Produits</span>
                    </a>

                    <a 
                        href="{{ route('categories') }}" 
                        :title="collapsed ? 'Catégories Produits' : ''"
                        class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 text-sm font-medium {{ request()->routeIs('categories') ? 'bg-[#00a884]/20 text-[#00a884] border border-[#00a884]/40 shadow-md shadow-[#00a884]/10 font-bold' : 'text-[#8696a0] hover:text-[#e9edef] hover:bg-[#202c33]' }}"
                    >
                        <i class="fa-solid fa-tags text-lg w-6 text-center shrink-0"></i>
                        <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Catégories Produits</span>
                    </a>

                    <a 
                        href="{{ route('users') }}" 
                        :title="collapsed ? 'Gestion Utilisateurs' : ''"
                        class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all duration-200 text-sm font-medium {{ request()->routeIs('users') ? 'bg-[#00a884]/20 text-[#00a884] border border-[#00a884]/40 shadow-md shadow-[#00a884]/10 font-bold' : 'text-[#8696a0] hover:text-[#e9edef] hover:bg-[#202c33]' }}"
                    >
                        <i class="fa-solid fa-users-gear text-lg w-6 text-center shrink-0"></i>
                        <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Gestion Utilisateurs</span>
                    </a>
                @endif
            </nav>
        </div>

        <!-- System info / Logged User profile & Logout -->
        <div class="glass-card p-2.5 rounded-2xl border border-[#2a3942] space-y-2">
            @if(auth()->check())
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <div class="w-8 h-8 rounded-xl bg-[#00a884]/20 text-[#00a884] flex items-center justify-center font-bold text-xs border border-[#00a884]/30 shrink-0" :title="collapsed ? '{{ auth()->user()->name }}' : ''">
                            <i class="fa-solid {{ auth()->user()->isAdmin() ? 'fa-user-shield' : 'fa-user' }}"></i>
                        </div>
                        <div x-show="!collapsed" x-transition.opacity class="overflow-hidden whitespace-nowrap">
                            <p class="text-xs font-bold text-[#e9edef] truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[9px] uppercase tracking-wider font-bold {{ auth()->user()->isAdmin() ? 'text-[#00a884]' : 'text-[#34b7f1]' }}">
                                {{ auth()->user()->isAdmin() ? '🛡️ Admin' : '👤 Caissier' }}
                            </p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="pt-1 border-t border-[#2a3942]">
                    @csrf
                    <button 
                        type="submit" 
                        :title="collapsed ? 'Déconnexion' : ''"
                        class="w-full py-1.5 px-3 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 hover:text-rose-300 font-bold text-[11px] transition-colors flex items-center justify-center gap-1.5"
                    >
                        <i class="fa-solid fa-right-from-bracket shrink-0"></i>
                        <span x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">Déconnexion</span>
                    </button>
                </form>
            @endif
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-[#0b141a]">
        <!-- Top Floating Toast Popup Notifications Container -->
        <div 
            x-data="{ 
                toasts: [],
                init() {
                    @if(session()->has('success'))
                        this.addToast(@js(session('success')), 'success');
                    @endif
                    @if(session()->has('error'))
                        this.addToast(@js(session('error')), 'error');
                    @endif
                    @if(session()->has('toast'))
                        this.addToast(@js(session('toast')), 'success');
                    @endif
                },
                addToast(message, type = 'success') {
                    if (!message) return;
                    const id = Date.now() + Math.random();
                    this.toasts.push({ id, message, type });
                    setTimeout(() => { this.removeToast(id); }, 4000);
                },
                removeToast(id) {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }
            }"
            @toast.window="addToast($event.detail.message, $event.detail.type || 'success')"
            class="fixed top-5 left-1/2 -translate-x-1/2 z-50 flex flex-col items-center gap-2.5 pointer-events-none w-full max-w-md px-4"
        >
            <template x-for="toast in toasts" :key="toast.id">
                <div 
                    x-show="true"
                    x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="-translate-y-8 opacity-0 scale-95"
                    x-transition:enter-end="translate-y-0 opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-200 transform"
                    x-transition:leave-start="translate-y-0 opacity-100 scale-100"
                    x-transition:leave-end="-translate-y-8 opacity-0 scale-95"
                    :class="toast.type === 'error' 
                        ? 'bg-rose-950/95 border-rose-500/60 text-rose-100 shadow-rose-900/40' 
                        : (toast.type === 'warning' ? 'bg-amber-950/95 border-amber-500/60 text-amber-100 shadow-amber-900/40' : 'bg-[#111b21] border-[#00a884]/60 text-[#25d366] shadow-[#00a884]/20')"
                    class="pointer-events-auto px-4 py-3 rounded-2xl border shadow-2xl backdrop-blur-xl flex items-center justify-between gap-3 text-xs font-semibold w-full"
                >
                    <div class="flex items-center gap-3">
                        <div :class="toast.type === 'error' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : (toast.type === 'warning' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-[#00a884]/20 text-[#00a884] border border-[#00a884]/30')" class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0">
                            <i :class="toast.type === 'error' ? 'fa-solid fa-circle-exclamation text-sm' : (toast.type === 'warning' ? 'fa-solid fa-triangle-exclamation text-sm' : 'fa-solid fa-circle-check text-sm')"></i>
                        </div>
                        <span x-text="toast.message" class="leading-snug"></span>
                    </div>
                    <button @click="removeToast(toast.id)" class="text-[#8696a0] hover:text-white shrink-0 p-1">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            </template>
        </div>

        <!-- Top Header Bar -->
        <header class="h-16 border-b border-[#2a3942] px-6 flex items-center justify-between glass-panel select-none">
            <div class="flex items-center gap-4">
                <img src="/logo.svg" class="w-8 h-8 rounded-lg shadow-md shrink-0 md:hidden" alt="Logo">
                <h2 class="text-lg font-bold text-white tracking-wide flex items-center gap-2">
                    {{ $header ?? 'Gestion des Ventes Pharmaceutiques' }}
                </h2>
            </div>
            <div class="flex items-center gap-4 sm:gap-6">
                <!-- Header Quick Client Requisition Action Button -->
                <button 
                    @click="$dispatch('open-global-req-modal')"
                    class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-[#00a884] to-[#128c7e] hover:from-[#25d366] hover:to-[#00a884] text-slate-950 font-bold text-xs shadow-md shadow-[#00a884]/20 flex items-center gap-1.5 transition-all"
                    title="Enregistrer une réquisition pour un produit demandé par un client"
                >
                    <i class="fa-solid fa-cart-flatbed"></i>
                    <span class="hidden md:inline">+ Demande Client (Produit Absent)</span>
                </button>

                <!-- Clock -->
                <div class="text-right hidden sm:block">
                    <div id="live-clock" class="text-sm font-semibold text-[#00a884] font-mono">--:--:--</div>
                    <div class="text-[11px] text-[#8696a0]">{{ now()->translatedFormat('l d F Y') }}</div>
                </div>
                <div class="h-6 w-px bg-[#2a3942] hidden sm:block"></div>
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-[#00a884]/10 text-[#00a884] border border-[#00a884]/20 font-medium flex items-center justify-center shrink-0" title="Base de données connectée">
                        <i class="fa-solid fa-database text-xs"></i>
                    </span>
                </div>
            </div>
        </header>

        <!-- Main Page Dynamic Slot -->
        <main class="flex-1 overflow-y-auto scrollbar-thin p-6">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
    <script>
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('fr-FR');
            const clockEl = document.getElementById('live-clock');
            if (clockEl) clockEl.innerText = timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>
