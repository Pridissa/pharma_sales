<div class="space-y-6">

    <!-- Header Actions & Filters -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-4 rounded-3xl border border-[#d6f0ea] shadow-sm">
        
        <!-- Search & Filter inputs -->
        <div class="flex-1 flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[220px]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-[#00c9a7]">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.250ms="search" 
                    placeholder="Chercher médicament, DCI, code-barres..." 
                    class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] placeholder-slate-400 focus:outline-none focus:border-[#00c9a7]"
                >
            </div>

            <select wire:model.live="selectedCategory" class="px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] focus:outline-none focus:border-[#00c9a7]">
                <option value="">Toutes les catégories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterStock" class="px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] focus:outline-none focus:border-[#00c9a7]">
                <option value="">Tous les stocks</option>
                <option value="low">Stock faible</option>
                <option value="out">Rupture de stock</option>
                <option value="expiring">Péremption imminente</option>
            </select>
        </div>

        <!-- Add Product Button -->
        <button 
            wire:click="openCreateModal" 
            class="px-4 py-2.5 bg-[#00c9a7] hover:bg-[#00b899] text-white font-extrabold text-xs rounded-xl shadow-md shadow-[#00c9a7]/20 flex items-center justify-center gap-2 whitespace-nowrap transition-all"
        >
            <i class="fa-solid fa-plus-circle text-sm"></i>
            Nouveau Médicament
        </button>
    </div>

    @if($successMessage)
        <div class="p-3.5 bg-emerald-100 border border-emerald-300 rounded-2xl text-emerald-900 text-xs font-bold flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-check-circle text-emerald-600"></i>
                <span>{{ $successMessage }}</span>
            </div>
            <button wire:click="$set('successMessage', '')" class="text-emerald-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    <!-- Pending Requisitions Section -->
    @if(isset($requisitions) && count($requisitions) > 0)
        <div class="p-4 rounded-3xl border border-rose-200 bg-rose-50 space-y-3 animate-fadeIn shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-cart-flatbed text-rose-600 text-base"></i>
                    <h3 class="font-heading font-extrabold text-sm text-[#0f172a]">Table des Réquisitions pour Approvisionnement</h3>
                    <span class="px-2 py-0.5 rounded-full bg-rose-600 text-white text-xs font-extrabold font-mono">
                        {{ count($requisitions) }}
                    </span>
                </div>
                <span class="text-[11px] text-slate-500 font-semibold hidden sm:inline">Réquisitions clients & produits en/proche alerte stock</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($requisitions as $req)
                    <div class="bg-white p-3.5 rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between gap-2.5">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs font-extrabold text-[#0f172a] truncate">{{ $req->product_name }}</h4>
                                <p class="text-[10px] text-slate-500 mt-0.5 italic truncate font-medium">{{ $req->notes ?: 'Aucune note' }}</p>
                            </div>
                            @if($req->type === 'seuil_alerte')
                                <span class="px-2 py-0.5 rounded-md bg-amber-100 border border-amber-300 text-amber-900 text-[9px] font-extrabold shrink-0">
                                    ⚠️ Seuil alerte
                                </span>
                            @elseif($req->type === 'approche_alerte')
                                <span class="px-2 py-0.5 rounded-md bg-cyan-100 border border-cyan-300 text-cyan-900 text-[9px] font-extrabold shrink-0">
                                    ⚡ Proche alerte
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-md bg-rose-100 border border-rose-300 text-rose-900 text-[9px] font-extrabold shrink-0">
                                    🛒 Demande client
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between pt-1 border-t border-slate-100 text-[10px]">
                            <div class="text-slate-600 font-bold">
                                Qté demandée: <span class="font-extrabold text-[#0f172a] font-mono">{{ $req->requested_quantity }}</span>
                            </div>
                            <button 
                                wire:click="startFulfillRequisition({{ $req->id }})" 
                                title="Traiter l'approvisionnement et mettre à jour le stock/lot"
                                class="px-2.5 py-1.5 rounded-xl bg-[#00c9a7] hover:bg-[#00b899] text-white text-[10px] font-extrabold transition-all flex items-center gap-1 shrink-0 shadow-sm"
                            >
                                <i class="fa-solid fa-boxes-packing"></i> Marquer Traité
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Products Data Table -->
    <div class="bg-white rounded-3xl border border-[#d6f0ea] overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800">
                <thead class="bg-slate-100 uppercase font-extrabold text-slate-600 text-[10px] border-b border-slate-200">
                    <tr>
                        <th class="p-3.5">Code / Produit</th>
                        <th class="p-3.5">DCI (Principe actif)</th>
                        <th class="p-3.5">Catégorie</th>
                        <th class="p-3.5">Prix Vente</th>
                        <th class="p-3.5">Quantité Stock</th>
                        <th class="p-3.5">Péremption</th>
                        <th class="p-3.5">Statut</th>
                        <th class="p-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-3.5">
                                <div class="font-extrabold text-[#0f172a] text-sm">{{ $product->name }}</div>
                                <div class="text-[10px] font-mono text-slate-500 font-bold">{{ $product->code_barre ?: 'Sans code-barres' }} • {{ $product->dosage_unit }}</div>
                            </td>
                            <td class="p-3.5 italic text-[#05a88b] font-mono font-bold">
                                {{ $product->dci ?: '-' }}
                            </td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px] font-bold border border-slate-200">
                                    {{ $product->category->name }}
                                </span>
                            </td>
                            <td class="p-3.5 font-mono font-black text-[#05a88b]">
                                {{ number_format($product->price, 0, ',', ' ') }} FC
                            </td>
                            <td class="p-3.5 font-mono">
                                <span class="font-extrabold {{ $product->stock_quantity <= $product->min_stock_alert ? 'text-amber-700' : 'text-slate-800' }}">
                                    {{ $product->stock_quantity }}
                                </span>
                                <span class="text-[10px] text-slate-500 font-semibold">(Min: {{ $product->min_stock_alert }})</span>
                            </td>
                            <td class="p-3.5 font-mono">
                                @if($product->expiration_date)
                                    <span class="{{ $product->isExpired() ? 'text-rose-600 font-extrabold' : ($product->isExpiringSoon() ? 'text-amber-700 font-extrabold' : 'text-slate-700 font-bold') }}">
                                        {{ $product->expiration_date->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                @if($product->stock_quantity <= 0)
                                    <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 border border-rose-300 text-[10px] font-extrabold">
                                        Rupture
                                    </span>
                                @elseif($product->isLowStock())
                                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-300 text-[10px] font-extrabold">
                                        Stock faible
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 text-[10px] font-extrabold">
                                        Disponible
                                    </span>
                                @endif

                                @if($product->requires_prescription)
                                    <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-900 text-[9px] font-extrabold ml-1 border border-amber-300">
                                        Ordonnance
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-right space-x-1">
                                <button 
                                    wire:click="openBatchModal({{ $product->id }})" 
                                    class="px-2 py-1.5 rounded-lg bg-[#00c9a7]/15 hover:bg-[#00c9a7] text-[#05a88b] hover:text-white text-[10px] font-extrabold inline-flex items-center gap-1 transition-colors"
                                    title="Gérer les numéros de lot & péremptions FEFO"
                                >
                                    <i class="fa-solid fa-boxes-stacked"></i> Lots ({{ $product->batches->count() }})
                                </button>
                                <button 
                                    wire:click="openMovementsModal({{ $product->id }})" 
                                    class="px-2 py-1.5 rounded-lg bg-cyan-100 hover:bg-cyan-600 text-cyan-800 hover:text-white text-[10px] font-extrabold inline-flex items-center gap-1 transition-colors"
                                    title="Historique des mouvements de stock"
                                >
                                    <i class="fa-solid fa-timeline"></i> Flux
                                </button>
                                <button 
                                    wire:click="editProduct({{ $product->id }})" 
                                    class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-[#00c9a7] text-slate-700 hover:text-white border border-slate-300 inline-flex items-center justify-center transition-colors"
                                    title="Modifier"
                                >
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button 
                                    wire:click="confirmDelete({{ $product->id }})" 
                                    class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white border border-rose-200 inline-flex items-center justify-center transition-colors"
                                    title="Supprimer le médicament"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-500 font-bold">Aucun produit trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Create / Edit Product Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop animate-fadeIn">
            <div class="bg-white border border-slate-200 rounded-3xl max-w-xl w-full p-6 shadow-2xl relative">
                <button wire:click="$set('showModal', false)" class="absolute top-4 right-4 text-slate-400 hover:text-slate-800">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <h3 class="font-heading font-extrabold text-lg text-[#0f172a] mb-4">
                    {{ $editingProductId ? 'Modifier le Médicament' : 'Ajouter un nouveau Médicament' }}
                </h3>

                <form wire:submit.prevent="saveProduct" class="space-y-4 text-xs">
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2">
                            <label class="block font-extrabold text-slate-700 mb-1">Nom du produit / Marque *</label>
                            <input type="text" wire:model="name" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] focus:border-[#00c9a7] focus:ring-2 focus:ring-[#00c9a7]/20 focus:outline-none" required>
                            @error('name') <span class="text-rose-600 text-[10px] font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block font-extrabold text-slate-700 mb-1">DCI (Principe actif)</label>
                            <input type="text" wire:model="dci" placeholder="ex: Paracétamol" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] focus:border-[#00c9a7] focus:outline-none">
                        </div>

                        <div>
                            <label class="block font-extrabold text-slate-700 mb-1">Code-barres / EAN</label>
                            <input type="text" wire:model="code_barre" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] focus:border-[#00c9a7] focus:outline-none">
                        </div>

                        <div>
                            <label class="block font-extrabold text-slate-700 mb-1">Catégorie *</label>
                            <select wire:model="category_id" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] focus:border-[#00c9a7] focus:outline-none">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-extrabold text-slate-700 mb-1">Conditionnement</label>
                            <input type="text" wire:model="dosage_unit" placeholder="ex: Boîte 16 gélules" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] focus:border-[#00c9a7] focus:outline-none">
                        </div>

                        <div>
                            <label class="block font-extrabold text-slate-700 mb-1">Prix Vente (FC) *</label>
                            <input type="number" step="50" wire:model="price" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold text-[#0f172a] focus:border-[#00c9a7] focus:outline-none" required>
                        </div>

                        <div>
                            <label class="block font-extrabold text-slate-700 mb-1">Prix d'Achat (FC)</label>
                            <input type="number" step="50" wire:model="purchase_price" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold text-[#0f172a] focus:border-[#00c9a7] focus:outline-none">
                        </div>

                        <div>
                            <label class="block font-extrabold text-slate-700 mb-1">Quantité Stock *</label>
                            <input type="number" wire:model="stock_quantity" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold text-[#0f172a] focus:border-[#00c9a7] focus:outline-none" required>
                        </div>

                        <div>
                            <label class="block font-extrabold text-slate-700 mb-1">Seuil Alerte Stock *</label>
                            <input type="number" wire:model="min_stock_alert" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold text-[#0f172a] focus:border-[#00c9a7] focus:outline-none" required>
                        </div>

                        <!-- Flatpickr Datepicker Field -->
                        <div>
                            <label class="block font-extrabold text-slate-700 mb-1">Date Péremption</label>
                            <x-date-picker wire:model="expiration_date" placeholder="Sélectionner une date..." />
                        </div>

                        <div class="flex items-center gap-2 pt-5">
                            <input type="checkbox" id="requires_prescription" wire:model="requires_prescription" class="w-4 h-4 rounded text-[#00c9a7] border-slate-300">
                            <label for="requires_prescription" class="font-extrabold text-amber-900 cursor-pointer">Nécessite une ordonnance</label>
                        </div>
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

    <!-- Centered Deletion Confirmation Modal -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop animate-fadeIn">
            <div class="bg-white border border-rose-300 rounded-3xl max-w-md w-full p-6 shadow-2xl relative text-center">
                
                <div class="w-16 h-16 rounded-full bg-rose-100 text-rose-600 border border-rose-300 flex items-center justify-center text-3xl mx-auto mb-4">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <h3 class="font-heading font-extrabold text-xl text-[#0f172a] mb-2">Confirmation de Suppression</h3>
                
                <p class="text-xs text-slate-600 mb-6 leading-relaxed font-medium">
                    Êtes-vous absolument sûr de vouloir supprimer le produit <br>
                    <span class="font-black text-rose-600 text-sm font-mono mt-1 block">"{{ $deletingProductName }}"</span> ?
                    <br><span class="text-slate-500 text-[11px]">Cette action est irréversible et retirera définitivement ce produit du système.</span>
                </p>

                <div class="flex items-center justify-center gap-3">
                    <button 
                        type="button" 
                        wire:click="cancelDelete" 
                        class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs border border-slate-300"
                    >
                        <i class="fa-solid fa-xmark mr-1.5"></i> Annuler
                    </button>
                    
                    <button 
                        type="button" 
                        wire:click="deleteProduct" 
                        class="px-6 py-3 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-md flex items-center gap-2"
                    >
                        <i class="fa-solid fa-trash-can"></i> Oui, Supprimer
                    </button>
                </div>

            </div>
        </div>
    @endif

    <!-- Modal de Traitement / Ajout au Stock / Nouveau Lot -->
    @if($showFulfillModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop animate-fadeIn">
            <div class="bg-white border border-emerald-300 rounded-3xl max-w-lg w-full p-6 shadow-2xl relative">
                <button wire:click="$set('showFulfillModal', false)" class="absolute top-4 right-4 text-slate-400 hover:text-slate-800">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-200">
                    <div class="w-12 h-12 rounded-2xl bg-[#00c9a7]/15 text-[#00c9a7] border border-[#00c9a7]/30 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-boxes-packing"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-lg text-[#0f172a]">Traiter la Réquisition & Entrée Stock</h3>
                        <p class="text-xs text-[#05a88b] font-mono font-extrabold">{{ $fulfillProductName }}</p>
                    </div>
                </div>

                <form wire:submit.prevent="completeFulfillRequisition" class="space-y-4 text-xs">
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 space-y-1">
                        <div class="flex justify-between text-slate-600 font-bold">
                            <span>Stock Actuel :</span>
                            <span class="font-black text-[#0f172a] font-mono">{{ $fulfillCurrentStock }} unités</span>
                        </div>
                        <div class="flex justify-between text-slate-600 font-bold">
                            <span>N° Lot généré :</span>
                            <span class="font-black text-[#05a88b] font-mono">{{ $fulfillBatchNumber }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-extrabold text-slate-700 mb-1">Quantité à Ajouter *</label>
                            <input 
                                type="number" 
                                wire:model="fulfillAddQuantity" 
                                min="1" 
                                class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-[#0f172a] font-mono font-black text-sm focus:border-[#00c9a7] focus:outline-none" 
                                required
                            >
                        </div>

                        <div>
                            <label class="block font-extrabold text-slate-700 mb-1">Date Péremption Lot</label>
                            <x-date-picker wire:model="fulfillExpirationDate" placeholder="Choisir une date..." />
                        </div>

                        <div>
                            <label class="block font-extrabold text-slate-700 mb-1">Prix d'Achat Unitaire (FC)</label>
                            <input 
                                type="number" 
                                step="50" 
                                wire:model="fulfillPurchasePrice" 
                                class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-[#0f172a] font-mono font-bold text-xs focus:border-[#00c9a7] focus:outline-none"
                            >
                        </div>

                        <div>
                            <label class="block font-extrabold text-slate-700 mb-1">Prix Vente Unitaire (FC)</label>
                            <input 
                                type="number" 
                                step="50" 
                                wire:model="fulfillPrice" 
                                class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-[#0f172a] font-mono font-bold text-xs focus:border-[#00c9a7] focus:outline-none"
                            >
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                        <button type="button" wire:click="$set('showFulfillModal', false)" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold border border-slate-300">
                            Annuler
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#00c9a7] hover:bg-[#00b899] text-white font-extrabold shadow-md flex items-center gap-2">
                            <i class="fa-solid fa-check-double"></i> Valider Entrée Stock & Traiter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal de Gestion des Lots (Batch Management & FEFO) -->
    @if($showBatchModal && $batchProduct)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop animate-fadeIn">
            <div class="bg-white border border-emerald-300 rounded-3xl max-w-2xl w-full p-6 shadow-2xl relative space-y-5">
                <button wire:click="$set('showBatchModal', false)" class="absolute top-4 right-4 text-slate-400 hover:text-slate-800">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
                    <div class="w-12 h-12 rounded-2xl bg-[#00c9a7]/15 text-[#00c9a7] border border-[#00c9a7]/30 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-lg text-[#0f172a]">Gestion des Lots & Péremptions FEFO</h3>
                        <p class="text-xs text-[#05a88b] font-mono font-extrabold">{{ $batchProduct->name }} (Stock Total: {{ $batchProduct->stock_quantity }})</p>
                    </div>
                </div>

                <!-- Form : Nouveau Lot -->
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-3">
                    <h4 class="font-extrabold text-xs text-[#0f172a] uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-plus-circle text-[#00c9a7]"></i> Ajouter un nouveau Lot
                    </h4>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
                        <div>
                            <label class="block font-extrabold text-slate-700 text-[10px] mb-1">N° de Lot *</label>
                            <input type="text" wire:model="newBatchNumber" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-xl text-[#0f172a] font-mono font-bold text-xs focus:border-[#00c9a7] focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block font-extrabold text-slate-700 text-[10px] mb-1">Péremption *</label>
                            <x-date-picker wire:model="newBatchExpirationDate" placeholder="Péremption..." input-class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-xl text-[#0f172a] font-mono font-bold text-xs focus:border-[#00c9a7] focus:outline-none cursor-pointer" />
                        </div>
                        <div>
                            <label class="block font-extrabold text-slate-700 text-[10px] mb-1">Quantité *</label>
                            <input type="number" min="1" wire:model="newBatchQuantity" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-xl text-[#0f172a] font-mono font-bold text-xs focus:border-[#00c9a7] focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block font-extrabold text-slate-700 text-[10px] mb-1">Prix Achat (FC)</label>
                            <input type="number" step="50" wire:model="newBatchPurchasePrice" class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-xl text-[#0f172a] font-mono font-bold text-xs focus:border-[#00c9a7] focus:outline-none">
                        </div>
                    </div>
                    <div class="flex justify-end pt-1">
                        <button wire:click="saveNewBatch" class="px-4 py-2 bg-[#00c9a7] hover:bg-[#00b899] text-white font-extrabold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5">
                            <i class="fa-solid fa-plus"></i> Enregistrer ce Lot
                        </button>
                    </div>
                </div>

                <!-- Table des Lots Actifs -->
                <div class="space-y-2">
                    <h4 class="font-extrabold text-xs text-[#0f172a] uppercase tracking-wider">Lots enregistrés</h4>
                    <div class="max-h-60 overflow-y-auto rounded-2xl border border-slate-200">
                        <table class="w-full text-left text-xs text-slate-800">
                            <thead class="bg-slate-100 text-[10px] uppercase font-extrabold text-slate-600">
                                <tr>
                                    <th class="p-3">N° Lot</th>
                                    <th class="p-3">Péremption</th>
                                    <th class="p-3">Qté Restante</th>
                                    <th class="p-3">Prix Achat</th>
                                    <th class="p-3 text-right">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($batchProduct->batches as $batch)
                                    <tr class="hover:bg-slate-50 font-mono">
                                        <td class="p-3 font-extrabold text-[#05a88b]">{{ $batch->batch_number }}</td>
                                        <td class="p-3">
                                            <span class="{{ $batch->isExpired() ? 'text-rose-600 font-black' : ($batch->isExpiringSoon() ? 'text-amber-700 font-extrabold' : 'text-slate-700 font-bold') }}">
                                                {{ $batch->expiration_date->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td class="p-3 font-extrabold text-[#0f172a]">{{ $batch->quantity }}</td>
                                        <td class="p-3 text-slate-600 font-bold">{{ number_format($batch->purchase_price, 0, ',', ' ') }} FC</td>
                                        <td class="p-3 text-right">
                                            <button 
                                                wire:click="toggleBatchStatus({{ $batch->id }})" 
                                                class="px-2 py-0.5 rounded-full text-[10px] font-extrabold border transition-colors {{ $batch->is_active ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-slate-100 text-slate-600 border-slate-300' }}"
                                            >
                                                {{ $batch->is_active ? 'Actif (FEFO)' : 'Inactif' }}
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-4 text-center text-slate-500 italic">Aucun lot enregistré pour ce produit.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex justify-end pt-3 border-t border-slate-200">
                    <button wire:click="$set('showBatchModal', false)" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs border border-slate-300">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Mouvements de Stock (Stock Movement History) -->
    @if($showMovementsModal && $movementProduct)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop animate-fadeIn">
            <div class="bg-white border border-cyan-300 rounded-3xl max-w-2xl w-full p-6 shadow-2xl relative space-y-4">
                <button wire:click="$set('showMovementsModal', false)" class="absolute top-4 right-4 text-slate-400 hover:text-slate-800">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
                    <div class="w-12 h-12 rounded-2xl bg-cyan-100 text-cyan-800 border border-cyan-300 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-timeline"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-lg text-[#0f172a]">Historique des Mouvements de Stock</h3>
                        <p class="text-xs text-cyan-800 font-mono font-extrabold">{{ $movementProduct->name }}</p>
                    </div>
                </div>

                <div class="max-h-80 overflow-y-auto rounded-2xl border border-slate-200">
                    <table class="w-full text-left text-xs text-slate-800">
                        <thead class="bg-slate-100 text-[10px] uppercase font-extrabold text-slate-600">
                            <tr>
                                <th class="p-3">Date / Heure</th>
                                <th class="p-3">Type</th>
                                <th class="p-3">Variations</th>
                                <th class="p-3">N° Lot / Ref</th>
                                <th class="p-3">Opérateur</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($movementProduct->stockMovements as $mov)
                                <tr class="hover:bg-slate-50 font-mono">
                                    <td class="p-3 text-slate-600 text-[11px] font-bold">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="p-3">
                                        @if($mov->type === 'entree')
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 font-extrabold text-[10px]">Entrée</span>
                                        @elseif($mov->type === 'vente')
                                            <span class="px-2 py-0.5 rounded-full bg-cyan-100 text-cyan-800 border border-cyan-300 font-extrabold text-[10px]">Vente</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-900 border border-amber-300 font-extrabold text-[10px]">{{ ucfirst($mov->type) }}</span>
                                        @endif
                                    </td>
                                    <td class="p-3 font-black {{ $mov->quantity > 0 ? 'text-[#05a88b]' : 'text-rose-600' }}">
                                        {{ $mov->quantity > 0 ? '+' : '' }}{{ $mov->quantity }}
                                        <span class="text-[10px] text-slate-500 font-normal">({{ $mov->previous_quantity }} ➔ {{ $mov->new_quantity }})</span>
                                    </td>
                                    <td class="p-3 text-slate-700 font-bold">{{ $mov->batch ? $mov->batch->batch_number : ($mov->reference_number ?: '-') }}</td>
                                    <td class="p-3 text-slate-600 text-[11px] font-bold">{{ $mov->user ? $mov->user->name : 'Système' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-slate-500 italic font-medium">Aucun mouvement de stock enregistré pour ce produit.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end pt-3 border-t border-slate-200">
                    <button wire:click="$set('showMovementsModal', false)" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs border border-slate-300">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>

