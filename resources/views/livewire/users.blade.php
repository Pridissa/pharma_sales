<div class="space-y-6">

    <!-- Header Actions & Search -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 glass-panel p-4 rounded-3xl border border-slate-800">
        <div class="flex-1 flex flex-wrap items-center gap-3 w-full">
            <div class="relative flex-1 min-w-[220px]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.250ms="search" 
                    placeholder="Chercher utilisateur, email, téléphone..." 
                    class="w-full pl-9 pr-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500"
                >
            </div>

            <select wire:model.live="roleFilter" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500">
                <option value="">Tous les rôles</option>
                <option value="admin">Administrateur</option>
                <option value="caissier">Caissier</option>
            </select>
        </div>

        <button 
            wire:click="openCreateModal" 
            class="px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-bold text-xs rounded-xl shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-2 whitespace-nowrap transition-all"
        >
            <i class="fa-solid fa-user-plus"></i>
            Nouvel Utilisateur
        </button>
    </div>

    @if($successMessage)
        <div class="p-3.5 bg-emerald-500/15 border border-emerald-500/30 rounded-2xl text-emerald-300 text-xs font-medium flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-check-circle"></i>
                <span>{{ $successMessage }}</span>
            </div>
            <button wire:click="$set('successMessage', '')" class="text-emerald-400"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    @if($errorMessage)
        <div class="p-3.5 bg-rose-500/15 border border-rose-500/30 rounded-2xl text-rose-300 text-xs font-medium flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>{{ $errorMessage }}</span>
            </div>
            <button wire:click="$set('errorMessage', '')" class="text-rose-400"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    <!-- Users Table -->
    <div class="glass-panel rounded-3xl border border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 uppercase font-semibold text-slate-400 text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="p-3.5">Nom & Utilisateur</th>
                        <th class="p-3.5">Adresse Email</th>
                        <th class="p-3.5">Téléphone</th>
                        <th class="p-3.5">Rôle</th>
                        <th class="p-3.5">Statut Compte</th>
                        <th class="p-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="p-3.5">
                                <div class="font-bold text-white text-sm flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-slate-800 flex items-center justify-center text-xs font-bold text-emerald-400 border border-slate-700">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span>{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="p-3.5 font-mono text-slate-300">
                                {{ $user->email }}
                            </td>
                            <td class="p-3.5 text-slate-400 font-mono">
                                {{ $user->phone ?: '-' }}
                            </td>
                            <td class="p-3.5">
                                @if($user->isAdmin())
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[10px] font-bold">
                                        🛡️ Administrateur
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 text-[10px] font-bold">
                                        👤 Caissier
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                <button 
                                    wire:click="toggleUserStatus({{ $user->id }})"
                                    class="px-2 py-0.5 rounded-md text-[10px] font-bold transition-all {{ $user->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20 hover:bg-rose-500/20' }}"
                                >
                                    {{ $user->is_active ? 'Actif' : 'Désactivé' }}
                                </button>
                            </td>
                            <td class="p-3.5 text-right">
                                <button 
                                    wire:click="editUser({{ $user->id }})" 
                                    class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-emerald-400 inline-flex items-center justify-center transition-colors"
                                    title="Modifier le compte"
                                >
                                    <i class="fa-solid fa-user-pen"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-400">Aucun utilisateur trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Create / Edit User Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-fadeIn">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl relative">
                <button wire:click="$set('showModal', false)" class="absolute top-4 right-4 text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <h3 class="font-heading font-bold text-lg text-white mb-4">
                    {{ $editingUserId ? 'Modifier l\'Utilisateur' : 'Créer un Compte Utilisateur' }}
                </h3>

                <form wire:submit.prevent="saveUser" class="space-y-3.5 text-xs">
                    
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Nom & Prénom *</label>
                        <input type="text" wire:model="name" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:outline-none" required>
                        @error('name') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Adresse Email *</label>
                        <input type="email" wire:model="email" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:outline-none" required>
                        @error('email') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Téléphone</label>
                        <input type="text" wire:model="phone" placeholder="ex: +221 77 000 00 00" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Rôle Utilisateur *</label>
                        <select wire:model="role" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:outline-none">
                            <option value="caissier">Caissier / Vendeur (Accès Caisse seul)</option>
                            <option value="admin">Administrateur (Accès Total)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">
                            Mot de Passe {{ $editingUserId ? '(Laisser vide pour ne pas modifier)' : '*' }}
                        </label>
                        <input type="password" wire:model="password" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:outline-none" {{ $editingUserId ? '' : 'required' }}>
                        @error('password') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" id="is_active" wire:model="is_active" class="w-4 h-4 rounded text-emerald-500 bg-slate-950 border-slate-700">
                        <label for="is_active" class="font-semibold text-slate-300 cursor-pointer">Compte Actif (Autorisé à se connecter)</label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold">
                            Annuler
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold shadow-lg shadow-emerald-500/20">
                            Enregistrer
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif

</div>
