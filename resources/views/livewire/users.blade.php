<div class="space-y-6">

    <!-- Header Actions & Search -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white p-4 rounded-3xl border border-[#d6f0ea] shadow-sm">
        <div class="flex-1 flex flex-wrap items-center gap-3 w-full">
            <div class="relative flex-1 min-w-[220px]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#00c9a7]">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.250ms="search" 
                    placeholder="Chercher utilisateur, email, téléphone..." 
                    class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] placeholder-slate-400 focus:outline-none focus:border-[#00c9a7]"
                >
            </div>

            <select wire:model.live="roleFilter" class="px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] focus:outline-none focus:border-[#00c9a7]">
                <option value="">Tous les rôles</option>
                <option value="admin">Administrateur</option>
                <option value="caissier">Caissier</option>
            </select>
        </div>

        <button 
            wire:click="openCreateModal" 
            class="px-4 py-2.5 bg-[#00c9a7] hover:bg-[#00b899] text-white font-extrabold text-xs rounded-xl shadow-md flex items-center justify-center gap-2 whitespace-nowrap transition-all"
        >
            <i class="fa-solid fa-user-plus"></i>
            Nouvel Utilisateur
        </button>
    </div>

    @if($successMessage)
        <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-xs font-extrabold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-check-circle"></i>
                <span>{{ $successMessage }}</span>
            </div>
            <button wire:click="$set('successMessage', '')" class="text-emerald-800"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    @if($errorMessage)
        <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-2xl text-rose-800 text-xs font-extrabold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>{{ $errorMessage }}</span>
            </div>
            <button wire:click="$set('errorMessage', '')" class="text-rose-800"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    <!-- Users Table -->
    <div class="bg-white rounded-3xl border border-[#d6f0ea] overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800">
                <thead class="bg-slate-100 uppercase font-extrabold text-slate-600 text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="p-3.5">Nom & Utilisateur</th>
                        <th class="p-3.5">Adresse Email</th>
                        <th class="p-3.5">Téléphone</th>
                        <th class="p-3.5">Rôle</th>
                        <th class="p-3.5">Statut Compte</th>
                        <th class="p-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-3.5">
                                <div class="font-extrabold text-[#0f172a] text-sm flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-[#00c9a7]/15 flex items-center justify-center text-xs font-black text-[#05a88b] border border-[#00c9a7]/30">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span>{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="p-3.5 font-mono font-bold text-slate-700">
                                {{ $user->email }}
                            </td>
                            <td class="p-3.5 text-slate-600 font-mono font-bold">
                                {{ $user->phone ?: '-' }}
                            </td>
                            <td class="p-3.5">
                                @if($user->isAdmin())
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 text-[10px] font-extrabold">
                                        🛡️ Administrateur
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-cyan-100 text-cyan-800 border border-cyan-300 text-[10px] font-extrabold">
                                        👤 Caissier
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                <button 
                                    wire:click="toggleUserStatus({{ $user->id }})"
                                    class="px-2 py-0.5 rounded-md text-[10px] font-extrabold transition-all {{ $user->is_active ? 'bg-emerald-100 text-emerald-800 border border-emerald-300 hover:bg-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-300 hover:bg-rose-200' }}"
                                >
                                    {{ $user->is_active ? 'Actif' : 'Désactivé' }}
                                </button>
                            </td>
                            <td class="p-3.5 text-right">
                                <button 
                                    wire:click="editUser({{ $user->id }})" 
                                    class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-[#00c9a7] text-slate-700 hover:text-white border border-slate-300 inline-flex items-center justify-center transition-colors"
                                    title="Modifier le compte"
                                >
                                    <i class="fa-solid fa-user-pen"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500 font-bold">Aucun utilisateur trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Create / Edit User Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop animate-fadeIn">
            <div class="bg-white border border-slate-200 rounded-3xl max-w-md w-full p-6 shadow-2xl relative">
                <button wire:click="$set('showModal', false)" class="absolute top-4 right-4 text-slate-400 hover:text-slate-800">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <h3 class="font-heading font-extrabold text-lg text-[#0f172a] mb-4">
                    {{ $editingUserId ? 'Modifier l\'Utilisateur' : 'Créer un Compte Utilisateur' }}
                </h3>

                <form wire:submit.prevent="saveUser" class="space-y-3.5 text-xs">
                    
                    <div>
                        <label class="block font-extrabold text-slate-700 mb-1">Nom & Prénom *</label>
                        <input type="text" wire:model="name" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] focus:border-[#00c9a7] focus:outline-none" required>
                        @error('name') <span class="text-rose-600 text-[10px] font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-extrabold text-slate-700 mb-1">Adresse Email *</label>
                        <input type="email" wire:model="email" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] focus:border-[#00c9a7] focus:outline-none" required>
                        @error('email') <span class="text-rose-600 text-[10px] font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-extrabold text-slate-700 mb-1">Téléphone</label>
                        <input type="text" wire:model="phone" placeholder="ex: +243 80 000 00 00" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] focus:border-[#00c9a7] focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-extrabold text-slate-700 mb-1">Rôle Utilisateur *</label>
                        <select wire:model="role" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] focus:border-[#00c9a7] focus:outline-none">
                            <option value="caissier">Caissier / Vendeur (Accès Caisse seul)</option>
                            <option value="admin">Administrateur (Accès Total)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-extrabold text-slate-700 mb-1">
                            Mot de Passe {{ $editingUserId ? '(Laisser vide pour ne pas modifier)' : '*' }}
                        </label>
                        <input type="password" wire:model="password" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] focus:border-[#00c9a7] focus:outline-none" {{ $editingUserId ? '' : 'required' }}>
                        @error('password') <span class="text-rose-600 text-[10px] font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" id="is_active" wire:model="is_active" class="w-4 h-4 rounded text-[#00c9a7] border-slate-300">
                        <label for="is_active" class="font-extrabold text-slate-700 cursor-pointer">Compte Actif (Autorisé à se connecter)</label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold border border-slate-300">
                            Annuler
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#00c9a7] hover:bg-[#00b899] text-white font-extrabold shadow-md">
                            Enregistrer
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif

</div>
