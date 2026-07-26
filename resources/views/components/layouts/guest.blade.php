<!DOCTYPE html>
<html lang="fr" class="h-full bg-[#eaf7f4] text-slate-800">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'BITA PHARMA - Connexion' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,400..800;1,400..800&family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
                            400: '#00c9a7',
                            500: '#00c9a7',
                            600: '#05a88b',
                            900: '#024b3e',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #eaf7f4; color: #0f172a; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Outfit', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .glass-panel {
            background: #ffffff;
            border: 1px solid #d6f0ea;
            box-shadow: 0 4px 20px -2px rgba(0, 201, 167, 0.08);
        }
    </style>
    @livewireStyles
</head>
<body class="h-full bg-[#eaf7f4] flex flex-col overflow-hidden relative">

    <!-- Background subtle gradient circles -->
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-[#00c9a7]/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-[#05a88b]/10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- Application Header Bar (Matching App Layout) -->
    <header class="h-16 border-b border-[#d6f0ea] px-6 flex items-center justify-between bg-white select-none z-20 shadow-sm">
        <div class="flex items-center gap-3">
            <img src="{{ asset('logo.svg') }}" alt="BITA PHARMA" class="w-9 h-9 object-contain drop-shadow-sm">
            <div>
                <h1 class="font-heading font-black text-base text-[#0f172a] tracking-wide leading-none">
                    BITA <span class="text-[#00c9a7]">PHARMA</span>
                </h1>
                <span class="text-[9px] text-[#05a88b] font-bold italic block mt-0.5">La confiance au cœur de vos soins</span>
            </div>
        </div>

        <div class="flex items-center gap-4 sm:gap-6">
            <!-- Clock -->
            <div class="text-right hidden sm:block">
                <div id="guest-live-clock" class="text-sm font-black text-[#05a88b] font-mono">--:--:--</div>
                <div class="text-[11px] text-slate-500 font-bold">{{ now()->translatedFormat('l d F Y') }}</div>
            </div>
            <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-emerald-50 text-[#00c9a7] border border-emerald-200 font-extrabold flex items-center justify-center shrink-0" title="Base de données connectée">
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
