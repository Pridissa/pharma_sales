<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'PharmaSales - Connexion' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,400..800;1,400..800&family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS Play CDN & FontAwesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        emerald: {
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            900: '#064e3b',
                            950: '#022c22',
                        },
                        pharma: {
                            dark: '#0b1329',
                            card: '#15203e',
                            accent: '#10b981',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #141e2e; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .glass-panel {
            background: rgba(30, 44, 66, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
    @livewireStyles
</head>
<body class="h-full bg-gradient-to-br from-[#141e2e] via-[#1a273b] to-[#111927] flex flex-col overflow-hidden relative">

    <!-- Background glowing ambient lights -->
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

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
                    : (toast.type === 'warning' ? 'bg-amber-950/95 border-amber-500/60 text-amber-100 shadow-amber-900/40' : 'bg-slate-900/95 border-emerald-500/60 text-emerald-200 shadow-emerald-950/50')"
                class="pointer-events-auto px-4 py-3 rounded-2xl border shadow-2xl backdrop-blur-xl flex items-center justify-between gap-3 text-xs font-semibold w-full"
            >
                <div class="flex items-center gap-3">
                    <div :class="toast.type === 'error' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : (toast.type === 'warning' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30')" class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0">
                        <i :class="toast.type === 'error' ? 'fa-solid fa-circle-exclamation text-sm' : (toast.type === 'warning' ? 'fa-solid fa-triangle-exclamation text-sm' : 'fa-solid fa-circle-check text-sm')"></i>
                    </div>
                    <span x-text="toast.message" class="leading-snug"></span>
                </div>
                <button @click="removeToast(toast.id)" class="text-slate-400 hover:text-white shrink-0 p-1">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </template>
    </div>

    <!-- Application Header Bar (Matching App Layout) -->
    <header class="h-16 border-b border-slate-800/80 px-6 flex items-center justify-between glass-panel select-none z-20">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 via-emerald-500 to-teal-400 flex items-center justify-center shadow-lg shadow-emerald-500/30 text-white font-bold text-lg shrink-0 border border-emerald-400/40">
                <i class="fa-solid fa-cross text-emerald-100"></i>
            </div>
            <div>
                <h1 class="font-heading font-extrabold text-base text-white tracking-wide leading-none">
                    BITA <span class="text-emerald-400">PHARMA</span>
                </h1>
                <span class="text-[9px] text-slate-400 italic block mt-0.5">La confiance au cœur de vos soins</span>
            </div>
        </div>

        <div class="flex items-center gap-4 sm:gap-6">
            <!-- Clock -->
            <div class="text-right hidden sm:block">
                <div id="guest-live-clock" class="text-sm font-semibold text-emerald-400 font-mono">--:--:--</div>
                <div class="text-[11px] text-slate-400">{{ now()->translatedFormat('l d F Y') }}</div>
            </div>
            <div class="h-6 w-px bg-slate-800 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-medium flex items-center justify-center shrink-0" title="Base de données connectée">
                    <i class="fa-solid fa-database text-xs"></i>
                </span>
            </div>
        </div>
    </header>

    <!-- Main Content Centered -->
    <main class="flex-1 flex items-center justify-center p-4 z-10 overflow-y-auto">
        <div class="w-full max-w-md">
            {{ $slot }}
        </div>
    </main>

    @livewireScripts
    <script>
        function updateGuestClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString('fr-FR');
            const clockEl = document.getElementById('guest-live-clock');
            if (clockEl) clockEl.innerText = timeStr;
        }
        setInterval(updateGuestClock, 1000);
        updateGuestClock();
    </script>
</body>
</html>
