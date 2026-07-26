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
                    placeholder="Rechercher par nom de produit ou note..." 
                    class="w-full pl-9 pr-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500"
                >
            </div>

            <select wire:model.live="typeFilter" class="px-3 py-2 bg-slate-900 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-emerald-500">
                <option value="">Toutes les réquisitions</option>
                <option value="demande_client">🛒 Demandes Clients</option>
                <option value="seuil_alerte">⚠️ Seuils d'Alerte Atteints</option>
                <option value="approche_alerte">⚡ Stocks Proches de l'Alerte</option>
            </select>
        </div>

        <!-- Add Customer Requisition Button -->
        <button 
            wire:click="openCustomReqModal" 
            class="px-4 py-2.5 bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-400 hover:to-pink-400 text-white font-bold text-xs rounded-xl shadow-lg shadow-rose-500/20 flex items-center justify-center gap-2 whitespace-nowrap transition-all"
        >
            <i class="fa-solid fa-cart-flatbed text-sm"></i>
            Nouvelle Demande Client (Produit Absent)
        </button>
    </div>

    <!-- Requisitions Grid / Table -->
    <div class="glass-panel rounded-3xl border border-slate-800 overflow-hidden">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-boxes-packing text-emerald-400 text-lg"></i>
                <h3 class="font-heading font-bold text-base text-white">Tableau des Réquisitions pour Approvisionnement</h3>
            </div>
            <span class="text-xs text-slate-400 font-mono font-semibold">
                Total en attente: {{ $requisitions->total() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 uppercase font-semibold text-slate-400 text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="p-3.5">Produit / Demande</th>
                        <th class="p-3.5">Type de Réquisition</th>
                        <th class="p-3.5">Qté Demandée</th>
                        <th class="p-3.5">Demandeur</th>
                        <th class="p-3.5">Notes & Détails</th>
                        <th class="p-3.5">Date Demande</th>
                        <th class="p-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($requisitions as $req)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="p-3.5 font-bold text-white text-sm">
                                {{ $req->product_name }}
                            </td>
                            <td class="p-3.5">
                                @if($req->type === 'seuil_alerte')
                                    <span class="px-2.5 py-1 rounded-full bg-amber-500/20 border border-amber-500/30 text-amber-300 text-[10px] font-bold inline-flex items-center gap-1">
                                        ⚠️ Seuil d'alerte atteint
                                    </span>
                                @elseif($req->type === 'approche_alerte')
                                    <span class="px-2.5 py-1 rounded-full bg-cyan-500/20 border border-cyan-500/30 text-cyan-300 text-[10px] font-bold inline-flex items-center gap-1">
                                        ⚡ Proche du seuil d'alerte
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-rose-500/20 border border-rose-500/30 text-rose-300 text-[10px] font-bold inline-flex items-center gap-1">
                                        🛒 Demande Client (Produit Absent)
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 font-mono font-bold text-white text-sm">
                                {{ $req->requested_quantity }}
                            </td>
                            <td class="p-3.5 text-slate-300">
                                <span class="px-2 py-0.5 rounded-lg bg-slate-800 text-slate-300 text-[11px] font-medium">
                                    {{ $req->user ? $req->user->name : 'Système / Vendeur' }}
                                </span>
                            </td>
                            <td class="p-3.5 text-slate-400 italic">
                                {{ $req->notes ?: 'Aucune note' }}
                            </td>
                            <td class="p-3.5 text-slate-400 font-mono">
                                {{ $req->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="p-3.5 text-right">
                                @if(auth()->check() && auth()->user()->isAdmin())
                                    <button 
                                        wire:click="startFulfillRequisition({{ $req->id }})" 
                                        class="px-3 py-1.5 rounded-xl bg-emerald-500/20 hover:bg-emerald-500 text-emerald-400 hover:text-slate-950 font-bold text-xs transition-all inline-flex items-center gap-1.5 shadow-md"
                                    >
                                        <i class="fa-solid fa-boxes-packing"></i> Marquer Traité
                                    </button>
                                @else
                                    <span class="text-[10px] text-slate-500 font-medium">Traitement réservé Admin</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                <i class="fa-solid fa-clipboard-check text-3xl text-slate-600 mb-2"></i>
                                <p>Aucune réquisition en attente.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-800">
            {{ $requisitions->links() }}
        </div>
    </div>

    <!-- Custom Requisition Modal -->
    @if($showCustomReqModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-fadeIn">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl relative">
                <button wire:click="$set('showCustomReqModal', false)" class="absolute top-4 right-4 text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-800">
                    <div class="w-10 h-10 rounded-2xl bg-rose-500/20 text-rose-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-cart-flatbed"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-lg text-white">Réquisition Produit Client</h3>
                        <p class="text-xs text-slate-400">Enregistrer une demande de produit non disponible en stock</p>
                    </div>
                </div>

                <form wire:submit.prevent="saveCustomRequisition" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Nom du Produit Demandé *</label>
                        <input 
                            type="text" 
                            wire:model="customReqProductName" 
                            placeholder="ex: Paracétamol Sirop 125mg, Insuline Mixtard..." 
                            class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:outline-none" 
                            required
                            autofocus
                        >
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Quantité Souhaitée</label>
                        <input 
                            type="number" 
                            wire:model="customReqQuantity" 
                            min="1" 
                            class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono focus:border-emerald-500 focus:outline-none" 
                            required
                        >
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Notes / Détails (Optionnel)</label>
                        <textarea 
                            wire:model="customReqNotes" 
                            rows="3" 
                            placeholder="ex: Demande urgente pour un client régulier, dosage spécifique..." 
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                        <button type="button" wire:click="$set('showCustomReqModal', false)" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold">
                            Annuler
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold shadow-lg shadow-emerald-500/20">
                            Enregistrer Réquisition
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Stock Lot Fulfillment Modal (Existing or New Product) -->
    @if($showFulfillModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-fadeIn">
            <div class="bg-slate-900 border {{ $fulfillingProductId ? 'border-emerald-500/30' : 'border-rose-500/30' }} rounded-3xl max-w-xl w-full p-6 shadow-2xl relative max-h-[90vh] overflow-y-auto scrollbar-thin">
                <button wire:click="$set('showFulfillModal', false)" class="absolute top-4 right-4 text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                @if($fulfillingProductId)
                    <!-- Mode Produit Existant -->
                    <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-800">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-boxes-packing"></i>
                        </div>
                        <div>
                            <h3 class="font-heading font-bold text-lg text-white">Traiter Réquisition & Entrée Stock</h3>
                            <p class="text-xs text-emerald-400 font-mono font-bold">{{ $fulfillProductName }} (Produit Existant)</p>
                        </div>
                    </div>

                    <form wire:submit.prevent="completeFulfillRequisition" class="space-y-4 text-xs">
                        <div class="p-3.5 bg-slate-950/80 rounded-2xl border border-slate-800 space-y-1.5">
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
                                    class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono font-bold text-sm focus:border-emerald-500 focus:outline-none" 
                                    required
                                >
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-300 mb-1">Date Péremption Lot</label>
                                <div 
                                    x-data 
                                    x-init="flatpickr($refs.expPickerExisting, { 
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
                                        x-ref="expPickerExisting" 
                                        type="text" 
                                        wire:model="fulfillExpirationDate" 
                                        placeholder="Choisir une date..." 
                                        class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono focus:border-emerald-500 focus:outline-none cursor-pointer"
                                    >
                                    <i class="fa-solid fa-calendar-days absolute right-3 top-3 text-slate-400 pointer-events-none"></i>
                                </div>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-300 mb-1">Prix d'Achat Unitaire (FC)</label>
                                <input 
                                    type="number" 
                                    step="50" 
                                    wire:model="fulfillPurchasePrice" 
                                    class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono focus:border-emerald-500 focus:outline-none"
                                >
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-300 mb-1">Prix Vente Unitaire (FC)</label>
                                <input 
                                    type="number" 
                                    step="50" 
                                    wire:model="fulfillPrice" 
                                    class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono focus:border-emerald-500 focus:outline-none"
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
                @else
                    <!-- Mode Nouveau Produit Absent du Stock -->
                    <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-800">
                        <div class="w-12 h-12 rounded-2xl bg-rose-500/20 text-rose-400 border border-rose-500/30 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-folder-plus"></i>
                        </div>
                        <div>
                            <h3 class="font-heading font-bold text-lg text-white">Ajouter au Stock & Traiter Réquisition</h3>
                            <p class="text-xs text-rose-400 font-medium">Ce produit n'existe pas en stock. Créez-le ci-dessous.</p>
                        </div>
                    </div>

                    <form wire:submit.prevent="completeFulfillRequisition" class="space-y-4 text-xs">
                        <div class="p-3 bg-rose-500/10 border border-rose-500/20 rounded-2xl text-rose-300 text-[11px]">
                            <i class="fa-solid fa-circle-info mr-1"></i> Renseignez la catégorie et le prix pour créer l'article et l'intégrer au stock.
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="md:col-span-2">
                                <label class="block font-semibold text-slate-300 mb-1">Nom du Produit *</label>
                                <input 
                                    type="text" 
                                    wire:model="fulfillProductName" 
                                    class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white font-bold focus:border-emerald-500 focus:outline-none" 
                                    required
                                >
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-300 mb-1">Catégorie *</label>
                                <select 
                                    wire:model="fulfillCategoryId" 
                                    class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:outline-none"
                                    required
                                >
                                    <option value="">Sélectionner une catégorie</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-300 mb-1">Code Barre</label>
                                <input 
                                    type="text" 
                                    wire:model="fulfillCodeBarre" 
                                    placeholder="ex: 3400938472910"
                                    class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono focus:border-emerald-500 focus:outline-none"
                                >
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-300 mb-1">DCI / Principe Actif</label>
                                <input 
                                    type="text" 
                                    wire:model="fulfillDci" 
                                    placeholder="ex: Paracétamol"
                                    class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:outline-none"
                                >
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-300 mb-1">Forme / Dosage</label>
                                <input 
                                    type="text" 
                                    wire:model="fulfillDosageUnit" 
                                    placeholder="ex: Boîte de 20 comprimés"
                                    class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:outline-none"
                                >
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-300 mb-1">Prix de Vente Unitaire (FC) *</label>
                                <input 
                                    type="number" 
                                    step="50" 
                                    wire:model="fulfillPrice" 
                                    class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono font-bold text-emerald-400 focus:border-emerald-500 focus:outline-none"
                                    required
                                >
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
                                <label class="block font-semibold text-slate-300 mb-1">Quantité Initialement Entrée *</label>
                                <input 
                                    type="number" 
                                    wire:model="fulfillAddQuantity" 
                                    min="1" 
                                    class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono font-bold focus:border-emerald-500 focus:outline-none"
                                    required
                                >
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-300 mb-1">Seuil d'Alerte Stock</label>
                                <input 
                                    type="number" 
                                    wire:model="fulfillMinStockAlert" 
                                    min="1" 
                                    class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono focus:border-emerald-500 focus:outline-none"
                                >
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-300 mb-1">Date Péremption Lot</label>
                                <div 
                                    x-data 
                                    x-init="flatpickr($refs.expPickerNew, { 
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
                                        x-ref="expPickerNew" 
                                        type="text" 
                                        wire:model="fulfillExpirationDate" 
                                        placeholder="Choisir une date..." 
                                        class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white font-mono focus:border-emerald-500 focus:outline-none cursor-pointer"
                                    >
                                    <i class="fa-solid fa-calendar-days absolute right-3 top-2.5 text-slate-400 pointer-events-none"></i>
                                </div>
                            </div>

                            <div>
                                <label class="block font-semibold text-slate-300 mb-1">N° Lot Généré</label>
                                <input 
                                    type="text" 
                                    wire:model="fulfillBatchNumber" 
                                    class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-emerald-400 font-mono font-bold focus:border-emerald-500 focus:outline-none"
                                    readonly
                                >
                            </div>
                        </div>

                        <div class="pt-2">
                            <label class="flex items-center gap-2 cursor-pointer text-slate-300">
                                <input type="checkbox" wire:model="fulfillRequiresPrescription" class="w-4 h-4 rounded text-emerald-500 bg-slate-950 border-slate-800">
                                <span>Délivrance sur ordonnance médicale obligatoire</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                            <button type="button" wire:click="$set('showFulfillModal', false)" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold">
                                Annuler
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                                <i class="fa-solid fa-plus-circle"></i> Créer le Produit & Traiter Réquisition
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif

</div>
