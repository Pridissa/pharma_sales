<div class="space-y-6">

    <!-- Header Actions & Filters -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 glass-panel p-4 rounded-3xl border border-slate-800">
        
        <!-- Search & Filter inputs -->
        <div class="flex-1 flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[220px]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.250ms="search" 
                    placeholder="Chercher médicament, DCI, code-barres..." 
                    class="w-full pl-9 pr-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500"
                >
            </div>

            <select wire:model.live="selectedCategory" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500">
                <option value="">Toutes les catégories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="filterStock" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500">
                <option value="">Tous les stocks</option>
                <option value="low">Stock faible</option>
                <option value="out">Rupture de stock</option>
                <option value="expiring">Péremption imminente</option>
            </select>
        </div>

        <!-- Add Product Button -->
        <button 
            wire:click="openCreateModal" 
            class="px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-bold text-xs rounded-xl shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-2 whitespace-nowrap transition-all"
        >
            <i class="fa-solid fa-plus-circle text-sm"></i>
            Nouveau Médicament
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

    <!-- Pending Requisitions Section -->
    @if(isset($requisitions) && count($requisitions) > 0)
        <div class="glass-card p-4 rounded-3xl border border-rose-500/30 bg-rose-500/5 space-y-3 animate-fadeIn">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-cart-flatbed text-rose-400 text-base"></i>
                    <h3 class="font-heading font-bold text-sm text-white">Table des Réquisitions pour Approvisionnement</h3>
                    <span class="px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-400 text-xs font-bold font-mono">
                        {{ count($requisitions) }}
                    </span>
                </div>
                <span class="text-[11px] text-slate-400 hidden sm:inline">Réquisitions clients & produits en/proche alerte stock</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                @foreach($requisitions as $req)
                    <div class="glass-panel p-3.5 rounded-2xl border border-slate-800 flex flex-col justify-between gap-2.5">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs font-bold text-white truncate">{{ $req->product_name }}</h4>
                                <p class="text-[10px] text-slate-400 mt-0.5 italic truncate">{{ $req->notes ?: 'Aucune note' }}</p>
                            </div>
                            @if($req->type === 'seuil_alerte')
                                <span class="px-2 py-0.5 rounded-md bg-amber-500/20 border border-amber-500/30 text-amber-300 text-[9px] font-bold shrink-0">
                                    ⚠️ Seuil alerte
                                </span>
                            @elseif($req->type === 'approche_alerte')
                                <span class="px-2 py-0.5 rounded-md bg-cyan-500/20 border border-cyan-500/30 text-cyan-300 text-[9px] font-bold shrink-0">
                                    ⚡ Proche alerte
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-md bg-rose-500/20 border border-rose-500/30 text-rose-300 text-[9px] font-bold shrink-0">
                                    🛒 Demande client
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between pt-1 border-t border-slate-800/60 text-[10px]">
                            <div class="text-slate-400">
                                Qté demandée: <span class="font-bold text-white font-mono">{{ $req->requested_quantity }}</span>
                            </div>
                            <button 
                                wire:click="startFulfillRequisition({{ $req->id }})" 
                                title="Traiter l'approvisionnement et mettre à jour le stock/lot"
                                class="px-2.5 py-1.5 rounded-xl bg-emerald-500/20 hover:bg-emerald-500 text-emerald-400 hover:text-slate-950 text-[10px] font-bold transition-all flex items-center gap-1 shrink-0"
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
    <div class="glass-panel rounded-3xl border border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 uppercase font-semibold text-slate-400 text-[10px] border-b border-slate-800">
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
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="p-3.5">
                                <div class="font-bold text-white text-sm">{{ $product->name }}</div>
                                <div class="text-[10px] font-mono text-slate-400">{{ $product->code_barre ?: 'Sans code-barres' }} • {{ $product->dosage_unit }}</div>
                            </td>
                            <td class="p-3.5 italic text-emerald-400 font-mono">
                                {{ $product->dci ?: '-' }}
                            </td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded-md bg-slate-800 text-slate-300 text-[10px]">
                                    {{ $product->category->name }}
                                </span>
                            </td>
                            <td class="p-3.5 font-mono font-bold text-emerald-400">
                                {{ number_format($product->price, 0, ',', ' ') }} FC
                            </td>
                            <td class="p-3.5 font-mono">
                                <span class="font-bold {{ $product->stock_quantity <= $product->min_stock_alert ? 'text-amber-400' : 'text-slate-200' }}">
                                    {{ $product->stock_quantity }}
                                </span>
                                <span class="text-[10px] text-slate-500">(Min: {{ $product->min_stock_alert }})</span>
                            </td>
                            <td class="p-3.5 font-mono">
                                @if($product->expiration_date)
                                    <span class="{{ $product->isExpired() ? 'text-rose-400 font-bold' : ($product->isExpiringSoon() ? 'text-amber-400 font-bold' : 'text-slate-300') }}">
                                        {{ $product->expiration_date->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-slate-500">-</span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                @if($product->stock_quantity <= 0)
                                    <span class="px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-400 border border-rose-500/30 text-[10px] font-semibold">
                                        Rupture
                                    </span>
                                @elseif($product->isLowStock())
                                    <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px] font-semibold">
                                        Stock faible
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[10px] font-semibold">
                                        Disponible
                                    </span>
                                @endif

                                @if($product->requires_prescription)
                                    <span class="px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 text-[9px] font-bold ml-1">
                                        Ordonnance
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-right space-x-1">
                                <button 
                                    wire:click="openBatchModal({{ $product->id }})" 
                                    class="px-2 py-1.5 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-400 text-[10px] font-bold inline-flex items-center gap-1 transition-colors"
                                    title="Gérer les numéros de lot & péremptions FEFO"
                                >
                                    <i class="fa-solid fa-boxes-stacked"></i> Lots ({{ $product->batches->count() }})
                                </button>
                                <button 
                                    wire:click="openMovementsModal({{ $product->id }})" 
                                    class="px-2 py-1.5 rounded-lg bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-400 text-[10px] font-bold inline-flex items-center gap-1 transition-colors"
                                    title="Historique des mouvements de stock"
                                >
                                    <i class="fa-solid fa-timeline"></i> Flux
                                </button>
                                <button 
                                    wire:click="editProduct({{ $product->id }})" 
                                    class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-emerald-400 inline-flex items-center justify-center transition-colors"
                                    title="Modifier"
                                >
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button 
                                    wire:click="confirmDelete({{ $product->id }})" 
                                    class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-rose-500/20 text-rose-400 inline-flex items-center justify-center transition-colors"
                                    title="Supprimer le médicament"
                                >
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-slate-400">Aucun produit trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Create / Edit Product Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-fadeIn">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-xl w-full p-6 shadow-2xl relative">
                <button wire:click="$set('showModal', false)" class="absolute top-4 right-4 text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <h3 class="font-heading font-bold text-lg text-white mb-4">
                    {{ $editingProductId ? 'Modifier le Médicament' : 'Ajouter un nouveau Médicament' }}
                </h3>

                <form wire:submit.prevent="saveProduct" class="space-y-4 text-xs">
                    
                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2">
                            <label class="block font-semibold text-slate-300 mb-1">Nom du produit / Marque *</label>
                            <input type="text" wire:model="name" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:outline-none" required>
                            @error('name') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">DCI (Principe actif)</label>
                            <input type="text" wire:model="dci" placeholder="ex: Paracétamol" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Code-barres / EAN</label>
                            <input type="text" wire:model="code_barre" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Catégorie *</label>
                            <select wire:model="category_id" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:outline-none">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Conditionnement</label>
                            <input type="text" wire:model="dosage_unit" placeholder="ex: Boîte 16 gélules" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Prix Vente (FC) *</label>
                            <input type="number" step="50" wire:model="price" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono focus:border-emerald-500 focus:outline-none" required>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Prix d'Achat (FC)</label>
                            <input type="number" step="50" wire:model="purchase_price" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono focus:border-emerald-500 focus:outline-none">
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Quantité Stock *</label>
                            <input type="number" wire:model="stock_quantity" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono focus:border-emerald-500 focus:outline-none" required>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Seuil Alerte Stock *</label>
                            <input type="number" wire:model="min_stock_alert" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono focus:border-emerald-500 focus:outline-none" required>
                        </div>

                        <!-- Flatpickr Datepicker Field -->
                        <div 
                            x-data 
                            x-init="flatpickr($refs.datePicker, { 
                                locale: 'fr', 
                                dateFormat: 'Y-m-d', 
                                altInput: true, 
                                altFormat: 'd/m/Y', 
                                theme: 'dark', 
                                onChange: function(selectedDates, dateStr) { $wire.set('expiration_date', dateStr); } 
                            })"
                        >
                            <label class="block font-semibold text-slate-300 mb-1">Date Péremption</label>
                            <div class="relative">
                                <input 
                                    x-ref="datePicker" 
                                    type="text" 
                                    wire:model="expiration_date" 
                                    placeholder="Sélectionner une date..." 
                                    class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:outline-none font-mono cursor-pointer"
                                >
                                <i class="fa-solid fa-calendar-days absolute right-3 top-2.5 text-slate-400 pointer-events-none"></i>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 pt-5">
                            <input type="checkbox" id="requires_prescription" wire:model="requires_prescription" class="w-4 h-4 rounded text-emerald-500 bg-slate-950 border-slate-700">
                            <label for="requires_prescription" class="font-semibold text-amber-300 cursor-pointer">Nécessite une ordonnance</label>
                        </div>
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

    <!-- Centered Deletion Confirmation Modal (Strict Persistent Modal) -->
    @if($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-fadeIn">
            <!-- No backdrop click event attached: modal persists until explicit user button action -->
            <div class="bg-slate-900 border border-rose-500/30 rounded-3xl max-w-md w-full p-6 shadow-2xl relative text-center">
                
                <div class="w-16 h-16 rounded-full bg-rose-500/20 text-rose-400 border border-rose-500/40 flex items-center justify-center text-3xl mx-auto mb-4 animate-pulse">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <h3 class="font-heading font-extrabold text-xl text-white mb-2">Confirmation de Suppression</h3>
                
                <p class="text-xs text-slate-300 mb-6 leading-relaxed">
                    Êtes-vous absolument sûr de vouloir supprimer le produit <br>
                    <span class="font-bold text-rose-400 text-sm font-mono mt-1 block">"{{ $deletingProductName }}"</span> ?
                    <br><span class="text-slate-400 text-[11px] font-normal">Cette action est irréversible et retirera définitivement ce produit du système.</span>
                </p>

                <div class="flex items-center justify-center gap-3">
                    <button 
                        type="button" 
                        wire:click="cancelDelete" 
                        class="px-5 py-3 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all border border-slate-700 shadow-md"
                    >
                        <i class="fa-solid fa-xmark mr-1.5"></i> Annuler
                    </button>
                    
                    <button 
                        type="button" 
                        wire:click="deleteProduct" 
                        class="px-6 py-3 rounded-2xl bg-gradient-to-r from-rose-600 to-red-500 hover:from-rose-500 hover:to-red-400 text-white font-bold text-xs shadow-lg shadow-rose-500/30 transition-all flex items-center gap-2"
                    >
                        <i class="fa-solid fa-trash-can"></i> Oui, Supprimer
                    </button>
                </div>

            </div>
        </div>
    @endif

    <!-- Modal de Traitement / Ajout au Stock / Nouveau Lot -->
    @if($showFulfillModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-fadeIn">
            <div class="bg-slate-900 border border-emerald-500/30 rounded-3xl max-w-lg w-full p-6 shadow-2xl relative">
                <button wire:click="$set('showFulfillModal', false)" class="absolute top-4 right-4 text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-800">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-boxes-packing"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-lg text-white">Traiter la Réquisition & Entrée Stock</h3>
                        <p class="text-xs text-emerald-400 font-mono font-bold">{{ $fulfillProductName }}</p>
                    </div>
                </div>

                <form wire:submit.prevent="completeFulfillRequisition" class="space-y-4 text-xs">
                    <div class="p-3 bg-slate-950/80 rounded-2xl border border-slate-800 space-y-1">
                        <div class="flex justify-between text-slate-400">
                            <span>Stock Actuel :</span>
                            <span class="font-bold text-white font-mono">{{ $fulfillCurrentStock }} unités</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>N° Lot généré :</span>
                            <span class="font-bold text-emerald-400 font-mono">{{ $fulfillBatchNumber }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Quantité à Ajouter *</label>
                            <input 
                                type="number" 
                                wire:model="fulfillAddQuantity" 
                                min="1" 
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono font-bold text-sm focus:border-emerald-500 focus:outline-none" 
                                required
                            >
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Date Péremption Lot</label>
                            <div 
                                x-data 
                                x-init="flatpickr($refs.prodFulfillExpPicker, { 
                                    locale: 'fr', 
                                    dateFormat: 'Y-m-d', 
                                    altInput: true, 
                                    altFormat: 'd/m/Y', 
                                    theme: 'dark', 
                                    onChange: function(dates, dateStr) { $wire.set('fulfillExpirationDate', dateStr); } 
                                })"
                                class="relative"
                            >
                                <input 
                                    x-ref="prodFulfillExpPicker" 
                                    type="text" 
                                    wire:model="fulfillExpirationDate" 
                                    placeholder="Choisir une date..." 
                                    class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono focus:border-emerald-500 focus:outline-none cursor-pointer"
                                >
                                <i class="fa-solid fa-calendar-days absolute right-3 top-2.5 text-slate-400 pointer-events-none"></i>
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Prix d'Achat Unitaire (FC)</label>
                            <input 
                                type="number" 
                                step="50" 
                                wire:model="fulfillPurchasePrice" 
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono focus:border-emerald-500 focus:outline-none"
                            >
                        </div>

                        <div>
                            <label class="block font-semibold text-slate-300 mb-1">Prix Vente Unitaire (FC)</label>
                            <input 
                                type="number" 
                                step="50" 
                                wire:model="fulfillPrice" 
                                class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono focus:border-emerald-500 focus:outline-none"
                            >
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                        <button type="button" wire:click="$set('showFulfillModal', false)" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold">
                            Annuler
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                            <i class="fa-solid fa-check-double"></i> Valider Entrée Stock & Traiter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal de Gestion des Lots (Batch Management & FEFO) -->
    @if($showBatchModal && $batchProduct)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-fadeIn">
            <div class="bg-slate-900 border border-emerald-500/30 rounded-3xl max-w-2xl w-full p-6 shadow-2xl relative space-y-5">
                <button wire:click="$set('showBatchModal', false)" class="absolute top-4 right-4 text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <div class="flex items-center gap-3 pb-3 border-b border-slate-800">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-lg text-white">Gestion des Lots & Péremptions FEFO</h3>
                        <p class="text-xs text-emerald-400 font-mono font-bold">{{ $batchProduct->name }} (Stock Total: {{ $batchProduct->stock_quantity }})</p>
                    </div>
                </div>

                <!-- Form : Nouveau Lot -->
                <div class="glass-card p-4 rounded-2xl border border-slate-800 space-y-3">
                    <h4 class="font-bold text-xs text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-plus-circle text-emerald-400"></i> Ajouter un nouveau Lot
                    </h4>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-400 text-[10px] mb-1">N° de Lot *</label>
                            <input type="text" wire:model="newBatchNumber" class="w-full px-2.5 py-1.5 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono text-xs focus:border-emerald-500 focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-400 text-[10px] mb-1">Péremption *</label>
                            <input type="date" wire:model="newBatchExpirationDate" class="w-full px-2.5 py-1.5 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono text-xs focus:border-emerald-500 focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-400 text-[10px] mb-1">Quantité *</label>
                            <input type="number" min="1" wire:model="newBatchQuantity" class="w-full px-2.5 py-1.5 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono text-xs focus:border-emerald-500 focus:outline-none" required>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-400 text-[10px] mb-1">Prix Achat Unitaire (FC)</label>
                            <input type="number" step="50" wire:model="newBatchPurchasePrice" class="w-full px-2.5 py-1.5 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono text-xs focus:border-emerald-500 focus:outline-none">
                        </div>
                    </div>
                    <div class="flex justify-end pt-1">
                        <button wire:click="saveNewBatch" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5">
                            <i class="fa-solid fa-plus"></i> Enregistrer ce Lot
                        </button>
                    </div>
                </div>

                <!-- Table des Lots Actifs -->
                <div class="space-y-2">
                    <h4 class="font-bold text-xs text-white uppercase tracking-wider">Lots enregistrés</h4>
                    <div class="max-h-60 overflow-y-auto rounded-2xl border border-slate-800">
                        <table class="w-full text-left text-xs text-slate-300">
                            <thead class="bg-slate-950/90 text-[10px] uppercase font-bold text-slate-400">
                                <tr>
                                    <th class="p-3">N° Lot</th>
                                    <th class="p-3">Péremption</th>
                                    <th class="p-3">Qté Restante</th>
                                    <th class="p-3">Prix Achat</th>
                                    <th class="p-3 text-right">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800">
                                @forelse($batchProduct->batches as $batch)
                                    <tr class="hover:bg-slate-800/40 font-mono">
                                        <td class="p-3 font-bold text-emerald-400">{{ $batch->batch_number }}</td>
                                        <td class="p-3">
                                            <span class="{{ $batch->isExpired() ? 'text-rose-400 font-bold' : ($batch->isExpiringSoon() ? 'text-amber-400 font-bold' : 'text-slate-300') }}">
                                                {{ $batch->expiration_date->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td class="p-3 font-bold text-white">{{ $batch->quantity }}</td>
                                        <td class="p-3 text-slate-400">{{ number_format($batch->purchase_price, 0, ',', ' ') }} FC</td>
                                        <td class="p-3 text-right">
                                            <button 
                                                wire:click="toggleBatchStatus({{ $batch->id }})" 
                                                class="px-2 py-0.5 rounded-full text-[10px] font-bold border transition-colors {{ $batch->is_active ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'bg-slate-800 text-slate-400 border-slate-700' }}"
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

                <div class="flex justify-end pt-3 border-t border-slate-800">
                    <button wire:click="$set('showBatchModal', false)" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Mouvements de Stock (Stock Movement History) -->
    @if($showMovementsModal && $movementProduct)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-fadeIn">
            <div class="bg-slate-900 border border-cyan-500/30 rounded-3xl max-w-2xl w-full p-6 shadow-2xl relative space-y-4">
                <button wire:click="$set('showMovementsModal', false)" class="absolute top-4 right-4 text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <div class="flex items-center gap-3 pb-3 border-b border-slate-800">
                    <div class="w-12 h-12 rounded-2xl bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-timeline"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-lg text-white">Historique des Mouvements de Stock</h3>
                        <p class="text-xs text-cyan-400 font-mono font-bold">{{ $movementProduct->name }}</p>
                    </div>
                </div>

                <div class="max-h-80 overflow-y-auto rounded-2xl border border-slate-800">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-950/90 text-[10px] uppercase font-bold text-slate-400">
                            <tr>
                                <th class="p-3">Date / Heure</th>
                                <th class="p-3">Type</th>
                                <th class="p-3">Variations</th>
                                <th class="p-3">N° Lot / Ref</th>
                                <th class="p-3">Opérateur</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @forelse($movementProduct->stockMovements as $mov)
                                <tr class="hover:bg-slate-800/40 font-mono">
                                    <td class="p-3 text-slate-400 text-[11px]">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="p-3">
                                        @if($mov->type === 'entree')
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-bold text-[10px]">Entrée</span>
                                        @elseif($mov->type === 'vente')
                                            <span class="px-2 py-0.5 rounded-full bg-cyan-500/20 text-cyan-400 font-bold text-[10px]">Vente</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 font-bold text-[10px]">{{ ucfirst($mov->type) }}</span>
                                        @endif
                                    </td>
                                    <td class="p-3 font-bold {{ $mov->quantity > 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                        {{ $mov->quantity > 0 ? '+' : '' }}{{ $mov->quantity }}
                                        <span class="text-[10px] text-slate-500">({{ $mov->previous_quantity }} ➔ {{ $mov->new_quantity }})</span>
                                    </td>
                                    <td class="p-3 text-slate-300">{{ $mov->batch ? $mov->batch->batch_number : ($mov->reference_number ?: '-') }}</td>
                                    <td class="p-3 text-slate-400 text-[11px]">{{ $mov->user ? $mov->user->name : 'Système' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-slate-500 italic">Aucun mouvement de stock enregistré pour ce produit.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end pt-3 border-t border-slate-800">
                    <button wire:click="$set('showMovementsModal', false)" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
