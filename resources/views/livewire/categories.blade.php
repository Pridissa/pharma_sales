<div class="space-y-6">

    <!-- Header Actions & Filters -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 glass-panel p-4 rounded-3xl border border-slate-800">
        
        <!-- Search input -->
        <div class="flex-1 flex items-center gap-3 min-w-[240px]">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.250ms="search" 
                    placeholder="Rechercher une catégorie..." 
                    class="w-full pl-9 pr-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500"
                >
            </div>
        </div>

        <!-- Add Category Button -->
        <button 
            wire:click="openCreateModal" 
            class="px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-bold text-xs rounded-xl shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-2 whitespace-nowrap transition-all"
        >
            <i class="fa-solid fa-folder-plus text-sm"></i>
            Nouvelle Catégorie
        </button>
    </div>

    <!-- Categories Data Table -->
    <div class="glass-panel rounded-3xl border border-slate-800 overflow-hidden">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-tags text-emerald-400 text-lg"></i>
                <h3 class="font-heading font-bold text-base text-white">Table des Catégories de Produits</h3>
            </div>
            <span class="text-xs text-slate-400 font-mono font-semibold">
                Total: {{ $categories->total() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 uppercase font-semibold text-slate-400 text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="p-3.5">Nom de la Catégorie</th>
                        <th class="p-3.5">Description</th>
                        <th class="p-3.5">Produits Associés</th>
                        <th class="p-3.5">Date de Création</th>
                        <th class="p-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="p-3.5 font-bold text-white text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-xs shrink-0">
                                        <i class="fa-solid fa-tag"></i>
                                    </div>
                                    <span>{{ $cat->name }}</span>
                                </div>
                            </td>
                            <td class="p-3.5 text-slate-400 italic">
                                {{ $cat->description ?: 'Aucune description' }}
                            </td>
                            <td class="p-3.5 font-mono">
                                <span class="px-2.5 py-1 rounded-full {{ $cat->products_count > 0 ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-slate-800 text-slate-400' }} text-xs font-bold">
                                    {{ $cat->products_count }} produit(s)
                                </span>
                            </td>
                            <td class="p-3.5 text-slate-400 font-mono">
                                {{ $cat->created_at ? $cat->created_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="p-3.5 text-right space-x-1">
                                <button 
                                    wire:click="editCategory({{ $cat->id }})" 
                                    class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-emerald-400 inline-flex items-center justify-center transition-colors"
                                    title="Modifier la catégorie"
                                >
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button 
                                    wire:click="confirmDelete({{ $cat->id }})" 
                                    class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-rose-500/20 text-rose-400 inline-flex items-center justify-center transition-colors"
                                    title="Supprimer la catégorie"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400">
                                <i class="fa-solid fa-folder-open text-3xl text-slate-600 mb-2 block"></i>
                                <span>Aucune catégorie trouvée.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800">
            {{ $categories->links() }}
        </div>
    </div>

    <!-- Create / Edit Category Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-fadeIn">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl relative">
                <button wire:click="$set('showModal', false)" class="absolute top-4 right-4 text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-800">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-tag"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-lg text-white">
                            {{ $editingCategoryId ? 'Modifier la Catégorie' : 'Nouvelle Catégorie' }}
                        </h3>
                        <p class="text-xs text-slate-400">Organisez vos médicaments par famille et usage</p>
                    </div>
                </div>

                <form wire:submit.prevent="saveCategory" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Nom de la Catégorie *</label>
                        <input 
                            type="text" 
                            wire:model="name" 
                            placeholder="ex: Anti-inflammatoires, Antibiotiques, Vitamines..." 
                            class="w-full px-3.5 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:outline-none font-medium" 
                            required
                            autofocus
                        >
                        @error('name') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Description (Optionnel)</label>
                        <textarea 
                            wire:model="description" 
                            rows="3" 
                            placeholder="ex: Médicaments prescrits contre la douleur et les inflammations..." 
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold">
                            Annuler
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold shadow-lg shadow-emerald-500/20">
                            {{ $editingCategoryId ? 'Mettre à jour' : 'Créer la Catégorie' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-fadeIn">
            <div class="bg-slate-900 border border-rose-500/30 rounded-3xl max-w-md w-full p-6 shadow-2xl relative text-center">
                
                <div class="w-16 h-16 rounded-full bg-rose-500/20 text-rose-400 border border-rose-500/40 flex items-center justify-center text-3xl mx-auto mb-4 animate-pulse">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <h3 class="font-heading font-extrabold text-xl text-white mb-2">Supprimer la Catégorie</h3>
                
                <p class="text-xs text-slate-300 mb-6 leading-relaxed">
                    Êtes-vous sûr de vouloir supprimer la catégorie <br>
                    <span class="font-bold text-rose-400 text-sm font-mono mt-1 block">"{{ $deletingCategoryName }}"</span> ?
                </p>

                <div class="flex items-center justify-center gap-3">
                    <button 
                        type="button" 
                        wire:click="cancelDelete" 
                        class="px-5 py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all border border-slate-700 shadow-md"
                    >
                        Annuler
                    </button>
                    
                    <button 
                        type="button" 
                        wire:click="deleteCategory" 
                        class="px-6 py-3 rounded-2xl bg-gradient-to-r from-rose-600 to-red-500 hover:from-rose-500 hover:to-red-400 text-white font-bold text-xs shadow-lg shadow-rose-500/30 transition-all flex items-center gap-2"
                    >
                        <i class="fa-solid fa-trash-can"></i> Supprimer
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
