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
                    placeholder="Rechercher par nom de produit ou note..." 
                    class="w-full pl-9 pr-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] placeholder-slate-400 focus:outline-none focus:border-[#00c9a7]"
                >
            </div>

            <select wire:model.live="typeFilter" class="px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] focus:outline-none focus:border-[#00c9a7]">
                <option value="">Toutes les réquisitions</option>
                <option value="demande_client">🛒 Demandes Clients</option>
                <option value="seuil_alerte">⚠️ Seuils d'Alerte Atteints</option>
                <option value="approche_alerte">⚡ Stocks Proches de l'Alerte</option>
            </select>
        </div>

        <!-- Add Customer Requisition Button -->
        <button 
            wire:click="openCustomReqModal" 
            class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl shadow-md flex items-center justify-center gap-2 whitespace-nowrap transition-all"
        >
            <i class="fa-solid fa-cart-flatbed text-sm"></i>
            Nouvelle Demande Client (Produit Absent)
        </button>
    </div>

    <!-- Requisitions Grid / Table -->
    <div class="bg-white rounded-3xl border border-[#d6f0ea] overflow-hidden shadow-sm">
        <div class="p-4 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-boxes-packing text-[#00c9a7] text-lg"></i>
                <h3 class="font-heading font-extrabold text-base text-[#0f172a]">Tableau des Réquisitions pour Approvisionnement</h3>
            </div>
            <span class="text-xs text-slate-500 font-mono font-extrabold">
                Total en attente: {{ $requisitions->total() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-800">
                <thead class="bg-slate-100 uppercase font-extrabold text-slate-600 text-[10px] border-b border-slate-200">
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
                <tbody class="divide-y divide-slate-100">
                    @forelse($requisitions as $req)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-3.5 font-extrabold text-[#0f172a] text-sm">
                                {{ $req->product_name }}
                            </td>
                            <td class="p-3.5">
                                @if($req->type === 'seuil_alerte')
                                    <span class="px-2.5 py-1 rounded-full bg-amber-100 border border-amber-300 text-amber-900 text-[10px] font-extrabold inline-flex items-center gap-1">
                                        ⚠️ Seuil d'alerte atteint
                                    </span>
                                @elseif($req->type === 'approche_alerte')
                                    <span class="px-2.5 py-1 rounded-full bg-cyan-100 border border-cyan-300 text-cyan-900 text-[10px] font-extrabold inline-flex items-center gap-1">
                                        ⚡ Proche du seuil d'alerte
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-rose-100 border border-rose-300 text-rose-900 text-[10px] font-extrabold inline-flex items-center gap-1">
                                        🛒 Demande Client (Produit Absent)
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 font-mono font-black text-[#0f172a] text-sm">
                                {{ $req->requested_quantity }}
                            </td>
                            <td class="p-3.5 text-slate-700">
                                <span class="px-2 py-0.5 rounded-lg bg-slate-100 text-slate-800 text-[11px] font-bold border border-slate-200">
                                    {{ $req->user ? $req->user->name : 'Système / Vendeur' }}
                                </span>
                            </td>
                            <td class="p-3.5 text-slate-600 italic font-medium">
                                {{ $req->notes ?: 'Aucune note' }}
                            </td>
                            <td class="p-3.5 text-slate-500 font-mono font-bold">
                                {{ $req->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="p-3.5 text-right">
                                @if(auth()->check() && auth()->user()->isAdmin())
                                    <button 
                                        wire:click="startFulfillRequisition({{ $req->id }})" 
                                        class="px-3 py-1.5 rounded-xl bg-[#00c9a7] hover:bg-[#00b899] text-white font-extrabold text-xs transition-all inline-flex items-center gap-1.5 shadow-sm"
                                    >
                                        <i class="fa-solid fa-boxes-packing"></i> Marquer Traité
                                    </button>
                                @else
                                    <span class="text-[10px] text-slate-500 font-bold">Traitement réservé Admin</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-500 font-bold">
                                <i class="fa-solid fa-clipboard-check text-3xl text-slate-400 mb-2"></i>
                                <p>Aucune réquisition en attente.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-200">
            {{ $requisitions->links() }}
        </div>
    </div>

    <!-- Custom Requisition Modal -->
    @if($showCustomReqModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop animate-fadeIn">
            <div class="bg-white border border-slate-200 rounded-3xl max-w-md w-full p-6 shadow-2xl relative">
                <button wire:click="$set('showCustomReqModal', false)" class="absolute top-4 right-4 text-slate-400 hover:text-slate-800">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-200">
                    <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center text-lg border border-rose-300">
                        <i class="fa-solid fa-cart-flatbed"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-lg text-[#0f172a]">Réquisition Produit Client</h3>
                        <p class="text-xs text-slate-500">Enregistrer une demande de produit non disponible en stock</p>
                    </div>
                </div>

                <form wire:submit.prevent="saveCustomRequisition" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-extrabold text-slate-700 mb-1">Nom du Produit Demandé *</label>
                        <input 
                            type="text" 
                            wire:model="customReqProductName" 
                            placeholder="ex: Paracétamol Sirop 125mg, Insuline Mixtard..." 
                            class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] focus:border-[#00c9a7] focus:ring-2 focus:ring-[#00c9a7]/20 focus:outline-none" 
                            required
                            autofocus
                        >
                    </div>

                    <div>
                        <label class="block font-extrabold text-slate-700 mb-1">Quantité Souhaitée</label>
                        <input 
                            type="number" 
                            wire:model="customReqQuantity" 
                            min="1" 
                            class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold text-[#0f172a] focus:border-[#00c9a7] focus:ring-2 focus:ring-[#00c9a7]/20 focus:outline-none" 
                            required
                        >
                    </div>

                    <div>
                        <label class="block font-extrabold text-slate-700 mb-1">Notes / Détails (Optionnel)</label>
                        <textarea 
                            wire:model="customReqNotes" 
                            rows="3" 
                            placeholder="ex: Demande urgente pour un client régulier, dosage spécifique..." 
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] focus:border-[#00c9a7] focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200">
                        <button type="button" wire:click="$set('showCustomReqModal', false)" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold border border-slate-300">
                            Annuler
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#00c9a7] hover:bg-[#00b899] text-white font-extrabold shadow-md">
                            Enregistrer Réquisition
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Stock Lot Fulfillment Modal (Existing or New Product) -->
    @if($showFulfillModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop animate-fadeIn">
            <div class="bg-white border {{ $fulfillingProductId ? 'border-emerald-300' : 'border-rose-300' }} rounded-3xl max-w-xl w-full p-6 shadow-2xl relative max-h-[90vh] overflow-y-auto scrollbar-thin">
                <button wire:click="$set('showFulfillModal', false)" class="absolute top-4 right-4 text-slate-400 hover:text-slate-800">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                @if($fulfillingProductId)
                    <!-- Mode Produit Existant -->
                    <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-200">
                        <div class="w-12 h-12 rounded-2xl bg-[#00c9a7]/15 text-[#00c9a7] border border-[#00c9a7]/30 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-boxes-packing"></i>
                        </div>
                        <div>
                            <h3 class="font-heading font-extrabold text-lg text-[#0f172a]">Traiter Réquisition & Entrée Stock</h3>
                            <p class="text-xs text-[#05a88b] font-mono font-extrabold">{{ $fulfillProductName }} (Produit Existant)</p>
                        </div>
                    </div>

                    <form wire:submit.prevent="completeFulfillRequisition" class="space-y-4 text-xs">
                        <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 space-y-1.5">
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
                                    class="w-full px-3 py-2.5 bg-white border border-slate-300 rounded-xl text-[#0f172a] font-mono font-black text-sm focus:border-[#00c9a7] focus:outline-none" 
                                    required
                                >
                            </div>

                            <div>
                                <label class="block font-extrabold text-slate-700 mb-1">Date Péremption Lot</label>
                                <div 
                                    x-data 
                                    x-init="flatpickr($refs.expPickerExisting, { 
                                        locale: 'fr', 
                                        dateFormat: 'Y-m-d', 
                                        altInput: true, 
                                        altFormat: 'd/m/Y', 
                                        onChange: function(dates, dateStr) { $wire.set('fulfillExpirationDate', dateStr); } 
                                    })"
                                    class="relative"
                                >
                                    <input 
                                        x-ref="expPickerExisting" 
                                        type="text" 
                                        wire:model="fulfillExpirationDate" 
                                        placeholder="Choisir une date..." 
                                        class="w-full px-3 py-2.5 bg-white border border-slate-300 rounded-xl text-[#0f172a] font-mono font-bold text-xs focus:border-[#00c9a7] focus:outline-none cursor-pointer"
                                    >
                                    <i class="fa-solid fa-calendar-days absolute right-3 top-3 text-slate-400 pointer-events-none"></i>
                                </div>
                            </div>

                            <div>
                                <label class="block font-extrabold text-slate-700 mb-1">Prix d'Achat Unitaire (FC)</label>
                                <input 
                                    type="number" 
                                    step="50" 
                                    wire:model="fulfillPurchasePrice" 
                                    class="w-full px-3 py-2.5 bg-white border border-slate-300 rounded-xl text-[#0f172a] font-mono font-bold text-xs focus:border-[#00c9a7] focus:outline-none"
                                >
                            </div>

                            <div>
                                <label class="block font-extrabold text-slate-700 mb-1">Prix Vente Unitaire (FC)</label>
                                <input 
                                    type="number" 
                                    step="50" 
                                    wire:model="fulfillPrice" 
                                    class="w-full px-3 py-2.5 bg-white border border-slate-300 rounded-xl text-[#0f172a] font-mono font-bold text-xs focus:border-[#00c9a7] focus:outline-none"
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
                @else
                    <!-- Mode Nouveau Produit Absent du Stock -->
                    <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-200">
                        <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-700 border border-rose-300 flex items-center justify-center text-xl shrink-0">
                            <i class="fa-solid fa-folder-plus"></i>
                        </div>
                        <div>
                            <h3 class="font-heading font-extrabold text-lg text-[#0f172a]">Ajouter au Stock & Traiter Réquisition</h3>
                            <p class="text-xs text-rose-800 font-bold">Ce produit n'existe pas en stock. Créez-le ci-dessous.</p>
                        </div>
                    </div>

                    <form wire:submit.prevent="completeFulfillRequisition" class="space-y-4 text-xs">
                        <div class="p-3 bg-rose-50 border border-rose-200 rounded-2xl text-rose-900 text-[11px] font-bold">
                            <i class="fa-solid fa-circle-info mr-1"></i> Renseignez la catégorie et le prix pour créer l'article et l'intégrer au stock.
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="md:col-span-2">
                                <label class="block font-extrabold text-slate-700 mb-1">Nom du Produit *</label>
                                <input 
                                    type="text" 
                                    wire:model="fulfillProductName" 
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-[#0f172a] font-bold focus:border-[#00c9a7] focus:outline-none" 
                                    required
                                >
                            </div>

                            <div>
                                <label class="block font-extrabold text-slate-700 mb-1">Catégorie *</label>
                                <select 
                                    wire:model="fulfillCategoryId" 
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-[#0f172a] font-bold focus:border-[#00c9a7] focus:outline-none"
                                    required
                                >
                                    <option value="">Sélectionner une catégorie</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block font-extrabold text-slate-700 mb-1">Code Barre</label>
                                <input 
                                    type="text" 
                                    wire:model="fulfillCodeBarre" 
                                    placeholder="ex: 3400938472910"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-[#0f172a] font-mono font-bold focus:border-[#00c9a7] focus:outline-none"
                                >
                            </div>

                            <div>
                                <label class="block font-extrabold text-slate-700 mb-1">DCI / Principe Actif</label>
                                <input 
                                    type="text" 
                                    wire:model="fulfillDci" 
                                    placeholder="ex: Paracétamol"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-[#0f172a] font-bold focus:border-[#00c9a7] focus:outline-none"
                                >
                            </div>

                            <div>
                                <label class="block font-extrabold text-slate-700 mb-1">Forme / Dosage</label>
                                <input 
                                    type="text" 
                                    wire:model="fulfillDosageUnit" 
                                    placeholder="ex: Boîte de 20 comprimés"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-[#0f172a] font-bold focus:border-[#00c9a7] focus:outline-none"
                                >
                            </div>

                            <div>
                                <label class="block font-extrabold text-slate-700 mb-1">Prix de Vente Unitaire (FC) *</label>
                                <input 
                                    type="number" 
                                    step="50" 
                                    wire:model="fulfillPrice" 
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-[#0f172a] font-mono font-black text-[#05a88b] focus:border-[#00c9a7] focus:outline-none"
                                    required
                                >
                            </div>

                            <div>
                                <label class="block font-extrabold text-slate-700 mb-1">Prix d'Achat Unitaire (FC)</label>
                                <input 
                                    type="number" 
                                    step="50" 
                                    wire:model="fulfillPurchasePrice" 
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-[#0f172a] font-mono font-bold focus:border-[#00c9a7] focus:outline-none"
                                >
                            </div>

                            <div>
                                <label class="block font-extrabold text-slate-700 mb-1">Quantité Initialement Entrée *</label>
                                <input 
                                    type="number" 
                                    wire:model="fulfillAddQuantity" 
                                    min="1" 
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-[#0f172a] font-mono font-black focus:border-[#00c9a7] focus:outline-none"
                                    required
                                >
                            </div>

                            <div>
                                <label class="block font-extrabold text-slate-700 mb-1">Seuil d'Alerte Stock</label>
                                <input 
                                    type="number" 
                                    wire:model="fulfillMinStockAlert" 
                                    min="1" 
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-[#0f172a] font-mono font-bold focus:border-[#00c9a7] focus:outline-none"
                                >
                            </div>

                            <div>
                                <label class="block font-extrabold text-slate-700 mb-1">Date Péremption Lot</label>
                                <div 
                                    x-data 
                                    x-init="flatpickr($refs.expPickerNew, { 
                                        locale: 'fr', 
                                        dateFormat: 'Y-m-d', 
                                        altInput: true, 
                                        altFormat: 'd/m/Y', 
                                        onChange: function(dates, dateStr) { $wire.set('fulfillExpirationDate', dateStr); } 
                                    })"
                                    class="relative"
                                >
                                    <input 
                                        x-ref="expPickerNew" 
                                        type="text" 
                                        wire:model="fulfillExpirationDate" 
                                        placeholder="Choisir une date..." 
                                        class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-[#0f172a] font-mono font-bold text-xs focus:border-[#00c9a7] focus:outline-none cursor-pointer"
                                    >
                                    <i class="fa-solid fa-calendar-days absolute right-3 top-2.5 text-slate-400 pointer-events-none"></i>
                                </div>
                            </div>

                            <div>
                                <label class="block font-extrabold text-slate-700 mb-1">N° Lot Généré</label>
                                <input 
                                    type="text" 
                                    wire:model="fulfillBatchNumber" 
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-[#05a88b] font-mono font-black focus:border-[#00c9a7] focus:outline-none"
                                    readonly
                                >
                            </div>
                        </div>

                        <div class="pt-2">
                            <label class="flex items-center gap-2 cursor-pointer text-slate-800 font-extrabold">
                                <input type="checkbox" wire:model="fulfillRequiresPrescription" class="w-4 h-4 rounded text-[#00c9a7] border-slate-300">
                                <span>Délivrance sur ordonnance médicale obligatoire</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                            <button type="button" wire:click="$set('showFulfillModal', false)" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold border border-slate-300">
                                Annuler
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#00c9a7] hover:bg-[#00b899] text-white font-extrabold shadow-md flex items-center gap-2">
                                <i class="fa-solid fa-plus-circle"></i> Créer le Produit & Traiter Réquisition
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    @endif

</div>
