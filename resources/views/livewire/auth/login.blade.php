<div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 shadow-2xl space-y-6">
    
    <!-- Brand Logo & Title Header -->
    <div class="text-center space-y-3 pb-2 border-b border-slate-800/80">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-500 to-cyan-400 flex items-center justify-center mx-auto text-slate-950 font-bold text-3xl shadow-xl shadow-emerald-500/20">
            <i class="fa-solid fa-cross"></i>
        </div>
        <div>
            <h1 class="font-heading font-extrabold text-2xl text-white tracking-wide">
                BITA <span class="text-emerald-400">PHARMA</span>
            </h1>
            <p class="text-xs text-emerald-400/90 font-medium italic mt-0.5">"La confiance au cœur de vos soins"</p>
        </div>
        <p class="text-[11px] text-slate-400 pt-1">Authentification & Accès au Système de Gestion</p>
    </div>

    <!-- Error Alert -->
    @if($errorMessage)
        <div class="p-3.5 bg-rose-500/15 border border-rose-500/30 rounded-2xl text-rose-300 text-xs flex items-center gap-2.5">
            <i class="fa-solid fa-circle-exclamation text-sm"></i>
            <span>{{ $errorMessage }}</span>
        </div>
    @endif

    <!-- Login Form -->
    <form wire:submit.prevent="login" class="space-y-4 text-xs">
        
        <div>
            <label class="block font-semibold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Adresse Email</label>
            <div class="relative">
                <input 
                    type="email" 
                    wire:model="email" 
                    placeholder="ex: admin@pharmacy.com" 
                    class="w-full pl-10 pr-4 py-3 bg-slate-950/80 border border-slate-800 rounded-2xl text-white placeholder-slate-500 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 font-medium" 
                    required
                    autofocus
                >
                <i class="fa-solid fa-envelope absolute left-3.5 top-3.5 text-slate-500 text-sm"></i>
            </div>
            @error('email') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block font-semibold text-slate-300 mb-1.5 uppercase tracking-wider text-[10px]">Mot de Passe</label>
            <div class="relative">
                <input 
                    type="password" 
                    wire:model="password" 
                    placeholder="••••••••" 
                    class="w-full pl-10 pr-4 py-3 bg-slate-950/80 border border-slate-800 rounded-2xl text-white placeholder-slate-500 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 font-medium" 
                    required
                >
                <i class="fa-solid fa-lock absolute left-3.5 top-3.5 text-slate-500 text-sm"></i>
            </div>
            @error('password') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center justify-between pt-1">
            <label class="flex items-center gap-2 cursor-pointer text-slate-400">
                <input type="checkbox" wire:model="remember" class="w-4 h-4 rounded text-emerald-500 bg-slate-950 border-slate-800">
                <span>Se souvenir de moi</span>
            </label>
        </div>

        <button 
            type="submit" 
            class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-bold text-sm shadow-xl shadow-emerald-500/20 transition-all flex items-center justify-center gap-2"
        >
            <i class="fa-solid fa-right-to-bracket"></i>
            Se Connecter
        </button>
    </form>

</div>
