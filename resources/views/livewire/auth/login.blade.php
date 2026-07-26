<div class="bg-white border border-[#d6f0ea] rounded-3xl p-8 shadow-xl space-y-6">
    
    <!-- Brand Logo & Title Header -->
    <div class="text-center space-y-3 pb-2 border-b border-slate-100">
        <div class="w-16 h-16 rounded-2xl bg-[#eaf7f4] border border-[#d6f0ea] flex items-center justify-center mx-auto shadow-sm">
            <img src="{{ asset('logo.svg') }}" alt="BITA PHARMA Logo" class="w-12 h-12 object-contain">
        </div>
        <div>
            <h1 class="font-heading font-black text-2xl text-[#0f172a] tracking-wide">
                BITA <span class="text-[#00c9a7]">PHARMA</span>
            </h1>
            <p class="text-xs text-[#05a88b] font-extrabold italic mt-0.5">"La confiance au cœur de vos soins"</p>
        </div>
        <p class="text-[11px] text-slate-500 font-bold pt-1">Authentification & Accès au Système de Gestion</p>
    </div>

    <!-- Error Alert -->
    @if($errorMessage)
        <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 text-xs font-extrabold flex items-center gap-2.5">
            <i class="fa-solid fa-circle-exclamation text-sm text-rose-600"></i>
            <span>{{ $errorMessage }}</span>
        </div>
    @endif

    <!-- Login Form -->
    <form wire:submit.prevent="login" class="space-y-4 text-xs">
        
        <div>
            <label class="block font-extrabold text-slate-700 mb-1.5 uppercase tracking-wider text-[10px]">Adresse Email</label>
            <div class="relative">
                <input 
                    type="email" 
                    wire:model="email" 
                    placeholder="ex: admin@pharmacy.com" 
                    class="w-full pl-10 pr-4 py-3 bg-white border border-slate-300 rounded-2xl text-[#0f172a] placeholder-slate-400 focus:border-[#00c9a7] focus:outline-none focus:ring-2 focus:ring-[#00c9a7]/20 font-bold" 
                    required
                    autofocus
                >
                <i class="fa-solid fa-envelope absolute left-3.5 top-3.5 text-[#00c9a7] text-sm"></i>
            </div>
            @error('email') <span class="text-rose-600 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block font-extrabold text-slate-700 mb-1.5 uppercase tracking-wider text-[10px]">Mot de Passe</label>
            <div class="relative">
                <input 
                    type="password" 
                    wire:model="password" 
                    placeholder="••••••••" 
                    class="w-full pl-10 pr-4 py-3 bg-white border border-slate-300 rounded-2xl text-[#0f172a] placeholder-slate-400 focus:border-[#00c9a7] focus:outline-none focus:ring-2 focus:ring-[#00c9a7]/20 font-bold" 
                    required
                >
                <i class="fa-solid fa-lock absolute left-3.5 top-3.5 text-[#00c9a7] text-sm"></i>
            </div>
            @error('password') <span class="text-rose-600 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center justify-between pt-1">
            <label class="flex items-center gap-2 cursor-pointer text-slate-700 font-extrabold">
                <input type="checkbox" wire:model="remember" class="w-4 h-4 rounded text-[#00c9a7] border-slate-300">
                <span>Se souvenir de moi</span>
            </label>
        </div>

        <button 
            type="submit" 
            class="w-full py-3.5 rounded-2xl bg-[#00c9a7] hover:bg-[#00b899] text-white font-extrabold text-sm shadow-md transition-all flex items-center justify-center gap-2"
        >
            <i class="fa-solid fa-right-to-bracket"></i>
            Se Connecter
        </button>
    </form>

</div>
