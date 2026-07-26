<div class="space-y-6">

    <!-- Header Actions & Filters -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-4 rounded-3xl border border-[#d6f0ea] shadow-sm">
        
        <!-- Search input -->
        <div class="flex-1 flex items-center gap-3 min-w-[240px]">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#00c9a7]">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.250ms="search" 
                    placeholder="Rechercher une catégorie..." 
                    class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] placeholder-slate-400 focus:outline-none focus:border-[#00c9a7]"
                >
            </div>
        </div>

        <!-- Add Category Button -->
        <button 
            wire:click="openCreateModal" 
            class="px-4 py-2.5 bg-[#00c9a7] hover:bg-[#00b899] text-white font-extrabold text-xs rounded-xl shadow-md shadow-[#00c9a7]/20 flex items-center justify-center gap-2 whitespace-nowrap transition-all"
        >
            <i class="fa-solid fa-folder-plus text-sm"></i>
            Nouvelle Catégorie
        </button>
    </div>

    <!-- Categories Data Table -->
    <div class="bg-white rounded-3xl border border-[#d6f0ea] overflow-hidden shadow-sm">
        <div class="p-4 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-tags text-[#00c9a7] text-lg"></i>
                <h3 class="font-heading font-extrabold text-base text-[#0f172a]">Table des Catégories de Produits</h3>
            </div>
            <span class="text-xs text-slate-500 font-mono font-extrabold">
                Total: {{ $categories->total() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800">
                <thead class="bg-slate-100 uppercase font-extrabold text-slate-600 text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="p-3.5">Nom de la Catégorie</th>
                        <th class="p-3.5">Description</th>
                        <th class="p-3.5">Produits Associés</th>
                        <th class="p-3.5">Date de Création</th>
                        <th class="p-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $cat)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-3.5 font-extrabold text-[#0f172a] text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-xl bg-[#00c9a7]/15 border border-[#00c9a7]/30 text-[#05a88b] flex items-center justify-center font-bold text-xs shrink-0">
                                        <i class="fa-solid fa-tag"></i>
                                    </div>
                                    <span>{{ $cat->name }}</span>
                                </div>
                            </td>
                            <td class="p-3.5 text-slate-600 italic font-medium">
                                {{ $cat->description ?: 'Aucune description' }}
                            </td>
                            <td class="p-3.5 font-mono">
                                <span class="px-2.5 py-1 rounded-full {{ $cat->products_count > 0 ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-slate-100 text-slate-600 border border-slate-200' }} text-xs font-extrabold">
                                    {{ $cat->products_count }} produit(s)
                                </span>
                            </td>
                            <td class="p-3.5 text-slate-500 font-mono font-bold">
                                {{ $cat->created_at ? $cat->created_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="p-3.5 text-right space-x-1">
                                <button 
                                    wire:click="editCategory({{ $cat->id }})" 
                                    class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-[#00c9a7] text-slate-700 hover:text-white border border-slate-300 inline-flex items-center justify-center transition-colors"
                                    title="Modifier la catégorie"
                                >
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button 
                                    wire:click="confirmDelete({{ $cat->id }})" 
                                    class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white border border-rose-200 inline-flex items-center justify-center transition-colors"
                                    title="Supprimer la catégorie"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-500 font-bold">
                                <i class="fa-solid fa-folder-open text-3xl text-slate-400 mb-2 block"></i>
                                <span>Aucune catégorie trouvée.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200">
            {{ $categories->links() }}
        </div>
    </div>

    <!-- Create / Edit Category Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop animate-fadeIn">
            <div class="bg-white border border-slate-200 rounded-3xl max-w-md w-full p-6 shadow-2xl relative">
                <button wire:click="$set('showModal', false)" class="absolute top-4 right-4 text-slate-400 hover:text-slate-800">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-200">
                    <div class="w-10 h-10 rounded-2xl bg-[#00c9a7]/15 text-[#00c9a7] flex items-center justify-center text-lg">
                        <i class="fa-solid fa-tag"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-lg text-[#0f172a]">
                            {{ $editingCategoryId ? 'Modifier la Catégorie' : 'Nouvelle Catégorie' }}
                        </h3>
                        <p class="text-xs text-slate-500">Organisez vos médicaments par famille et usage</p>
                    </div>
                </div>

                <form wire:submit.prevent="saveCategory" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-extrabold text-slate-700 mb-1">Nom de la Catégorie *</label>
                        <input 
                            type="text" 
                            wire:model="name" 
                            placeholder="ex: Anti-inflammatoires, Antibiotiques, Vitamines..." 
                            class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] focus:border-[#00c9a7] focus:ring-2 focus:ring-[#00c9a7]/20 focus:outline-none" 
                            required
                            autofocus
                        >
                        @error('name') <span class="text-rose-600 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-extrabold text-slate-700 mb-1">Description (Optionnel)</label>
                        <textarea 
                            wire:model="description" 
                            rows="3" 
                            placeholder="ex: Médicaments prescrits contre la douleur et les inflammations..." 
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] focus:border-[#00c9a7] focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                        <button type="button" wire:click="$set('showModal', false)" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold border border-slate-300">
                            Annuler
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#00c9a7] hover:bg-[#00b899] text-white font-extrabold shadow-md">
                            {{ $editingCategoryId ? 'Mettre à jour' : 'Créer la Catégorie' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop animate-fadeIn">
            <div class="bg-white border border-rose-300 rounded-3xl max-w-md w-full p-6 shadow-2xl relative text-center">
                
                <div class="w-16 h-16 rounded-full bg-rose-100 text-rose-600 border border-rose-300 flex items-center justify-center text-3xl mx-auto mb-4">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <h3 class="font-heading font-extrabold text-xl text-[#0f172a] mb-2">Supprimer la Catégorie</h3>
                
                <p class="text-xs text-slate-600 mb-6 leading-relaxed font-medium">
                    Êtes-vous sûr de vouloir supprimer la catégorie <br>
                    <span class="font-black text-rose-600 text-sm font-mono mt-1 block">"{{ $deletingCategoryName }}"</span> ?
                </p>

                <div class="flex items-center justify-center gap-3">
                    <button 
                        type="button" 
                        wire:click="cancelDelete" 
                        class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs border border-slate-300"
                    >
                        Annuler
                    </button>
                    
                    <button 
                        type="button" 
                        wire:click="deleteCategory" 
                        class="px-6 py-3 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-md flex items-center gap-2"
                    >
                        <i class="fa-solid fa-trash-can"></i> Supprimer
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
