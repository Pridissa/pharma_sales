<div class="h-full flex flex-col lg:flex-row gap-6">

    <!-- Left Column: Catalog & Search -->
    <div class="flex-1 flex flex-col min-w-0">

        <!-- Cash Session Status Banner -->
        <div class="mb-3 p-3.5 rounded-2xl bg-white border border-[#d6f0ea] shadow-sm flex items-center justify-between gap-3 text-xs">
            @if($activeSession)
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-[#00c9a7] animate-pulse"></span>
                    <div>
                        <span class="font-bold text-[#0f172a] text-sm">Session Caisse Active #{{ $activeSession->id }}</span>
                        <span class="text-slate-600 text-[11px] block">Ouverte à {{ $activeSession->opened_at->format('H:i') }} • Fond initial : <span class="font-mono text-[#05a88b] font-extrabold">{{ number_format($activeSession->opening_balance, 0, ',', ' ') }} FC</span></span>
                    </div>
                </div>
                <button 
                    wire:click="openCloseSessionModal" 
                    class="px-3.5 py-1.5 rounded-xl bg-amber-100 hover:bg-amber-200 text-amber-900 font-extrabold border border-amber-300 transition-all flex items-center gap-1.5"
                >
                    <i class="fa-solid fa-lock"></i> Clôturer Caisse (Z)
                </button>
            @else
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                    <div>
                        <span class="font-extrabold text-rose-700 text-sm">Aucune Session Caisse Ouverte</span>
                        <span class="text-slate-600 text-[11px] block">Veuillez ouvrir votre session avec le fond de caisse initial</span>
                    </div>
                </div>
                <button 
                    wire:click="openSessionModal" 
                    class="px-4 py-1.5 rounded-xl bg-[#00c9a7] hover:bg-[#00b899] text-white font-extrabold shadow-md shadow-[#00c9a7]/20 transition-all flex items-center gap-1.5"
                >
                    <i class="fa-solid fa-key"></i> Ouvrir Session Caisse
                </button>
            @endif
        </div>

        <!-- Search Bar & Category Pills -->
        <div class="mb-4 space-y-3">
            <!-- Search input -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#00c9a7]">
                    <i class="fa-solid fa-magnifying-glass text-lg"></i>
                </div>
                <input 
                    wire:model.live.debounce.250ms="search" 
                    type="text" 
                    placeholder="Rechercher par nom (ex: Paracétamol), DCI ou Code-barres..."
                    class="w-full pl-12 pr-10 py-3.5 bg-white border-2 border-[#d6f0ea] rounded-2xl text-[#0f172a] font-bold text-sm placeholder-slate-400 focus:outline-none focus:border-[#00c9a7] focus:ring-2 focus:ring-[#00c9a7]/20 shadow-sm transition-all"
                    autofocus
                >
                @if($search)
                    <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-700">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                @endif
            </div>

            <!-- Categories pills & Client requisition trigger -->
            <div class="flex items-center justify-between gap-2 overflow-x-auto scrollbar-thin pb-1">
                <div class="flex items-center gap-2">
                    <button 
                        wire:click="$set('selectedCategory', null)" 
                        class="px-4 py-2 rounded-xl text-xs font-extrabold whitespace-nowrap transition-all duration-200 {{ is_null($selectedCategory) ? 'bg-[#00c9a7] text-white shadow-md shadow-[#00c9a7]/30' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300' }}"
                    >
                        Tous les médicaments
                    </button>
                    @foreach($categories as $category)
                        <button 
                            wire:click="$set('selectedCategory', {{ $category->id }})" 
                            class="px-4 py-2 rounded-xl text-xs font-extrabold whitespace-nowrap transition-all duration-200 {{ $selectedCategory === $category->id ? 'bg-[#00c9a7] text-white shadow-md shadow-[#00c9a7]/30' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-300' }}"
                        >
                            {{ $category->name }} ({{ $category->products_count }})
                        </button>
                    @endforeach
                </div>

                <button 
                    wire:click="openCustomReqModal('')" 
                    class="px-3.5 py-2 rounded-xl text-xs font-extrabold whitespace-nowrap bg-rose-100 hover:bg-rose-200 text-rose-800 border border-rose-300 transition-all flex items-center gap-1.5 shrink-0"
                >
                    <i class="fa-solid fa-cart-flatbed"></i> Produit Absent ? Réquisition
                </button>
            </div>
        </div>

        <!-- Alert messages -->
        @if($errorMessage)
            <div class="mb-4 p-3.5 bg-rose-100 border border-rose-300 rounded-2xl text-rose-800 text-xs font-bold flex items-center justify-between animate-fadeIn">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-exclamation text-base text-rose-600"></i>
                    <span>{{ $errorMessage }}</span>
                </div>
                <button wire:click="$set('errorMessage', '')" class="text-rose-600 hover:text-rose-900">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if($successMessage)
            <div class="mb-4 p-3.5 bg-emerald-100 border border-emerald-300 rounded-2xl text-emerald-900 text-xs font-bold flex items-center justify-between animate-fadeIn">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-base text-emerald-600"></i>
                    <span>{{ $successMessage }}</span>
                </div>
                <button wire:click="$set('successMessage', '')" class="text-emerald-700 hover:text-emerald-950">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        <!-- Products Grid -->
        <div class="flex-1 overflow-y-auto scrollbar-thin pr-1">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-3 gap-3.5">
                @forelse($products as $product)
                    <div 
                        wire:click="addToCart({{ $product->id }})"
                        class="bg-white hover:bg-[#f5fbf9] p-4 rounded-2xl cursor-pointer border border-[#d6f0ea] hover:border-[#00c9a7] transition-all duration-200 flex flex-col justify-between group relative overflow-hidden shadow-sm hover:shadow-md hover:shadow-[#00c9a7]/10 {{ $product->stock_quantity <= 0 ? 'opacity-60 grayscale cursor-not-allowed' : '' }}"
                    >
                        @if($product->requires_prescription)
                            <span class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded-md bg-amber-100 border border-amber-300 text-amber-900 text-[10px] font-extrabold flex items-center gap-1">
                                <i class="fa-solid fa-file-prescription text-[9px] text-amber-700"></i> Ordonnance
                            </span>
                        @endif

                        <div>
                            <div class="text-[11px] text-[#05a88b] font-extrabold mb-1 truncate uppercase tracking-wider">
                                {{ $product->category->name }}
                            </div>
                            <h3 class="font-heading font-extrabold text-[#0f172a] text-sm group-hover:text-[#00c9a7] transition-colors line-clamp-1">
                                {{ $product->name }}
                            </h3>
                            @if($product->dci)
                                <p class="text-[11px] text-slate-600 font-bold italic truncate mb-2">
                                    DCI: {{ $product->dci }}
                                </p>
                            @endif
                            <p class="text-[11px] text-slate-500 font-semibold">
                                {{ $product->dosage_unit ?: 'Unité' }}
                            </p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                            <div>
                                <span class="text-sm font-extrabold text-[#05a88b] font-mono">
                                    {{ number_format($product->price, 0, ',', ' ') }} FC
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] px-2 py-0.5 rounded-lg font-bold {{ $product->stock_quantity > $product->min_stock_alert ? 'bg-slate-100 text-slate-800 border border-slate-200' : ($product->stock_quantity > 0 ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-rose-100 text-rose-800 border border-rose-300') }}">
                                    Stock: {{ $product->stock_quantity }}
                                </span>
                                @if($product->stock_quantity > 0)
                                    <button class="w-8 h-8 rounded-xl bg-[#00c9a7]/15 text-[#00c9a7] group-hover:bg-[#00c9a7] group-hover:text-white flex items-center justify-center transition-colors shadow-sm">
                                        <i class="fa-solid fa-plus text-xs"></i>
                                    </button>
                                @else
                                    <button 
                                        wire:click.stop="requestRequisition({{ $product->id }})" 
                                        title="Enregistrer une demande d'approvisionnement"
                                        class="px-2.5 py-1 rounded-xl bg-rose-100 hover:bg-rose-600 text-rose-800 hover:text-white flex items-center gap-1 text-[10px] font-extrabold transition-colors z-10 border border-rose-300"
                                    >
                                        <i class="fa-solid fa-cart-flatbed text-[10px]"></i> Réquisition
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-2xl p-10 text-center space-y-4 border border-[#d6f0ea] shadow-sm">
                        <i class="fa-solid fa-box-open text-4xl text-slate-400"></i>
                        <div class="space-y-1">
                            <p class="text-[#0f172a] font-extrabold text-base">Aucun médicament trouvé pour cette recherche.</p>
                            <p class="text-slate-500 text-xs font-medium">Le produit demandé par le client n'est peut-être pas présent en stock.</p>
                        </div>
                        @if($search)
                            <div>
                                <button 
                                    wire:click="openCustomReqModal('{{ addslashes($search) }}')"
                                    class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl shadow-md inline-flex items-center gap-2 transition-all cursor-pointer"
                                >
                                    <i class="fa-solid fa-cart-flatbed text-sm"></i>
                                    <span>Ajouter une Réquisition Client pour "{{ $search }}"</span>
                                </button>
                            </div>
                        @else
                            <div>
                                <button 
                                    wire:click="openCustomReqModal('')"
                                    class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl shadow-md inline-flex items-center gap-2 transition-all cursor-pointer"
                                >
                                    <i class="fa-solid fa-cart-flatbed text-sm"></i>
                                    <span>Ajouter une Réquisition Client (Produit Absent)</span>
                                </button>
                            </div>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Right Column: Cart & Checkout Panel -->
    <div class="w-full lg:w-96 flex flex-col bg-white rounded-3xl p-5 border border-[#d6f0ea] shadow-xl">
        
        <!-- Cart Header -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-basket-shopping text-[#00c9a7] text-lg"></i>
                <h3 class="font-heading font-extrabold text-[#0f172a] text-base">Panier de Vente</h3>
                <span class="px-2.5 py-0.5 rounded-full bg-[#00c9a7] text-white text-xs font-extrabold font-mono shadow-sm">
                    {{ count($cart) }}
                </span>
            </div>
            @if(count($cart) > 0)
                <button wire:click="clearCart" class="text-xs text-rose-600 hover:text-rose-800 font-extrabold transition-colors">
                    <i class="fa-solid fa-trash-can mr-1"></i> Vider
                </button>
            @endif
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 overflow-y-auto scrollbar-thin py-3 space-y-2.5 my-2 min-h-[180px] max-h-[300px] lg:max-h-none">
            @forelse($cart as $id => $item)
                <div class="bg-[#f8faf9] p-3 rounded-2xl border border-slate-200 flex items-center justify-between gap-3 shadow-sm">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-xs font-extrabold text-[#0f172a] truncate">{{ $item['name'] }}</h4>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-[11px] font-mono text-[#05a88b] font-extrabold">
                                {{ number_format($item['price'], 0, ',', ' ') }} FC
                            </span>
                            @if($item['requires_prescription'])
                                <span class="text-[9px] px-1.5 py-0.2 rounded bg-amber-100 text-amber-900 font-extrabold border border-amber-300">Ord.</span>
                            @endif
                        </div>
                    </div>

                    <!-- Quantity controls -->
                    <div class="flex items-center gap-1.5 bg-white p-1 rounded-xl border border-slate-300 shadow-sm">
                        <button 
                            wire:click="updateQuantity({{ $id }}, {{ $item['qty'] - 1 }})" 
                            class="w-6 h-6 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 flex items-center justify-center text-xs font-bold transition-colors"
                        >
                            -
                        </button>
                        <span class="w-6 text-center text-xs font-mono font-extrabold text-[#0f172a]">{{ $item['qty'] }}</span>
                        <button 
                            wire:click="updateQuantity({{ $id }}, {{ $item['qty'] + 1 }})" 
                            class="w-6 h-6 rounded-lg bg-[#00c9a7] hover:bg-[#00b899] text-white flex items-center justify-center text-xs font-bold transition-colors"
                        >
                            +
                        </button>
                    </div>

                    <!-- Subtotal & Remove -->
                    <div class="text-right min-w-[70px]">
                        <div class="text-xs font-mono font-extrabold text-[#0f172a]">
                            {{ number_format($item['subtotal'], 0, ',', ' ') }} FC
                        </div>
                        <button wire:click="removeFromCart({{ $id }})" class="text-[10px] text-rose-600 hover:text-rose-800 font-bold transition-colors mt-0.5">
                            Supprimer
                        </button>
                    </div>
                </div>
            @empty
                <div class="h-full flex flex-col items-center justify-center text-slate-500 py-12">
                    <i class="fa-solid fa-cart-shopping text-3xl mb-2 text-slate-300"></i>
                    <p class="text-xs font-semibold">Sélectionnez un médicament pour l'ajouter au panier.</p>
                </div>
            @endforelse
        </div>

        <!-- Prescription Warning Banner -->
        @if($this->hasPrescriptionItem())
            <div class="mb-3 p-2.5 bg-amber-50 border border-amber-300 rounded-xl text-amber-900 text-xs flex items-center gap-2 font-bold">
                <i class="fa-solid fa-triangle-exclamation text-amber-600 text-sm"></i>
                <div class="flex-1">
                    <span>Présence de produit sur ordonnance !</span>
                </div>
            </div>
        @endif

        <!-- Customer & Prescriber Info -->
        <div class="space-y-3 py-3 border-t border-slate-200">
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Patient (Nom)</label>
                    <input 
                        type="text" 
                        wire:model.live="patientName" 
                        placeholder="Optionnel..." 
                        class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] placeholder-slate-400 focus:border-[#00c9a7] focus:ring-2 focus:ring-[#00c9a7]/20 focus:outline-none"
                    >
                </div>
                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider {{ $this->hasPrescriptionItem() ? 'text-amber-700 font-black' : 'text-slate-700' }}">
                        Médecin Prescripteur {{ $this->hasPrescriptionItem() ? '*' : '' }}
                    </label>
                    <input 
                        type="text" 
                        wire:model.live="doctorName" 
                        placeholder="{{ $this->hasPrescriptionItem() ? 'Obligatoire *' : 'Optionnel...' }}" 
                        class="w-full px-3 py-2 bg-white border {{ $this->hasPrescriptionItem() && empty($doctorName) ? 'border-amber-500 ring-2 ring-amber-500/20' : 'border-slate-300' }} rounded-xl text-xs font-bold text-[#0f172a] placeholder-slate-400 focus:border-[#00c9a7] focus:ring-2 focus:ring-[#00c9a7]/20 focus:outline-none"
                    >
                </div>
            </div>

            <!-- Payment Method & Amount Paid -->
            <div class="space-y-3 pt-1">
                <div>
                    <label class="text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Mode Règlement</label>
                    <div class="grid grid-cols-2 gap-2 mt-1">
                        <button 
                            type="button" 
                            wire:click="$set('paymentMethod', 'Espèces')"
                            class="py-2.5 px-3 rounded-xl border text-xs font-extrabold transition-all flex items-center justify-center gap-2 {{ $paymentMethod === 'Espèces' ? 'bg-[#00c9a7] text-white border-[#00c9a7] shadow-md shadow-[#00c9a7]/20' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}"
                        >
                            <i class="fa-solid fa-money-bill-wave"></i> Espèces
                        </button>
                        <button 
                            type="button" 
                            wire:click="$set('paymentMethod', 'Mobile Money')"
                            class="py-2.5 px-3 rounded-xl border text-xs font-extrabold transition-all flex items-center justify-center gap-2 {{ $paymentMethod === 'Mobile Money' ? 'bg-[#00c9a7] text-white border-[#00c9a7] shadow-md shadow-[#00c9a7]/20' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50' }}"
                        >
                            <i class="fa-solid fa-mobile-screen-button"></i> Mobile Money
                        </button>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <label class="text-[10px] font-extrabold text-slate-700 uppercase tracking-wider">Montant Versé (FC)</label>
                        <button 
                            type="button" 
                            wire:click="setAmountPaid({{ $total }})" 
                            class="text-[10px] font-extrabold text-[#05a88b] hover:underline"
                        >
                            [Net à payer]
                        </button>
                    </div>
                    <input 
                        type="number" 
                        wire:model.live.debounce.300ms="amountPaid" 
                        step="any" 
                        min="0"
                        placeholder="Entrer montant..."
                        class="w-full px-3.5 py-2.5 bg-white border-2 border-slate-300 rounded-xl text-base text-[#05a88b] font-mono font-black focus:border-[#00c9a7] focus:ring-2 focus:ring-[#00c9a7]/20 focus:outline-none"
                    >
                    <!-- Quick Amount Addition Pills -->
                    <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-thin pt-1.5">
                        <button type="button" wire:click="addAmountPaid(500)" class="px-2.5 py-1 bg-slate-100 hover:bg-[#00c9a7] hover:text-white border border-slate-300 rounded-lg text-[10px] font-mono font-extrabold text-slate-800 transition-colors">+500</button>
                        <button type="button" wire:click="addAmountPaid(1000)" class="px-2.5 py-1 bg-slate-100 hover:bg-[#00c9a7] hover:text-white border border-slate-300 rounded-lg text-[10px] font-mono font-extrabold text-slate-800 transition-colors">+1 000</button>
                        <button type="button" wire:click="addAmountPaid(5000)" class="px-2.5 py-1 bg-slate-100 hover:bg-[#00c9a7] hover:text-white border border-slate-300 rounded-lg text-[10px] font-mono font-extrabold text-slate-800 transition-colors">+5 000</button>
                        <button type="button" wire:click="addAmountPaid(10000)" class="px-2.5 py-1 bg-slate-100 hover:bg-[#00c9a7] hover:text-white border border-slate-300 rounded-lg text-[10px] font-mono font-extrabold text-slate-800 transition-colors">+10 000</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Breakdown & Complete Sale Button -->
        <div class="pt-3 border-t border-slate-200 space-y-2">
            <div class="flex items-center justify-between text-xs text-slate-600 font-bold">
                <span>Sous-total:</span>
                <span class="font-mono text-[#0f172a] font-extrabold">{{ number_format($total, 0, ',', ' ') }} FC</span>
            </div>
            
            <div class="flex items-center justify-between text-xs text-slate-600 font-bold">
                <span>Monnaie à rendre:</span>
                <span class="font-mono font-extrabold {{ $this->changeAmount() > 0 ? 'text-amber-700' : 'text-slate-600' }}">
                    {{ number_format($this->changeAmount(), 0, ',', ' ') }} FC
                </span>
            </div>

            <div class="flex items-center justify-between pt-1 text-base font-black text-[#0f172a]">
                <span>Total à Payer:</span>
                <span class="text-2xl font-mono text-[#05a88b] font-black">
                    {{ number_format($total, 0, ',', ' ') }} FC
                </span>
            </div>

            <button 
                wire:click="completeSale" 
                @if(count($cart) === 0) disabled @endif
                class="w-full py-3.5 rounded-2xl bg-[#00c9a7] hover:bg-[#00b899] text-white font-extrabold text-sm shadow-xl shadow-[#00c9a7]/30 transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <i class="fa-solid fa-check-circle text-base"></i>
                Valider & Imprimer Ticket
            </button>
        </div>

    </div>

    <!-- Printable Receipt Modal -->
    @if($showReceiptModal && $lastSale)
        <div id="printable-receipt-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop animate-fadeIn">
            <div class="bg-white border border-slate-200 rounded-3xl max-w-md w-full p-6 shadow-2xl relative">
                
                <button wire:click="closeReceiptModal" class="absolute top-4 right-4 text-slate-400 hover:text-slate-800 no-print-element">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <div class="text-center mb-6 no-print-element">
                    <div class="w-12 h-12 rounded-2xl bg-[#00c9a7]/15 text-[#00c9a7] mx-auto flex items-center justify-center text-2xl mb-2">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <h3 class="font-heading font-extrabold text-xl text-[#0f172a]">Ticket de Caisse Validé</h3>
                    <p class="text-xs text-[#05a88b] font-mono font-bold">{{ $lastSale->invoice_number }}</p>
                </div>

                <!-- Receipt Printable Area -->
                <div id="printable-receipt" class="bg-white text-slate-900 p-6 rounded-xl font-mono text-xs shadow-inner space-y-3 mb-6 border border-slate-200">
                    <div class="text-center border-b border-dashed border-slate-400 pb-3">
                        <h2 class="font-black text-base tracking-wider uppercase text-slate-950">BITA PHARMA</h2>
                        <p class="text-[10px] italic font-serif text-slate-700 font-bold mb-1">"La confiance au cœur de vos soins"</p>
                        <p class="text-[10px] font-bold text-slate-800">Tél: +243 80 88 58 326 / +243 99 45 50 510</p>
                        <div class="mt-2 text-[10px] text-slate-800 border-t border-slate-200 pt-1">
                            <p class="font-bold">Facture N°: {{ $lastSale->invoice_number }}</p>
                            <p>Date: {{ $lastSale->created_at->format('d/m/Y H:i:s') }}</p>
                            <p class="font-bold text-emerald-800">Vendeur / Caissier: {{ $lastSale->user ? $lastSale->user->name : (auth()->user()->name ?? 'Caissier') }}</p>
                            @if($lastSale->patient_name)
                                <p>Patient: {{ $lastSale->patient_name }}</p>
                            @endif
                            @if($lastSale->doctor_name)
                                <p>Prescripteur: {{ $lastSale->doctor_name }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Items list -->
                    <div class="space-y-1.5 py-1">
                        @foreach($lastSale->items as $item)
                            <div class="flex justify-between items-start text-xs">
                                <span class="truncate max-w-[180px] font-bold">{{ $item->quantity }}x {{ $item->product_name }}</span>
                                <span class="font-bold font-mono">{{ number_format($item->subtotal, 0, ',', ' ') }} FC</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Totals -->
                    <div class="border-t border-dashed border-slate-400 pt-2 space-y-1 font-bold">
                        <div class="flex justify-between text-sm text-slate-950">
                            <span>TOTAL NET:</span>
                            <span>{{ number_format($lastSale->total_amount, 0, ',', ' ') }} FC</span>
                        </div>
                        <div class="flex justify-between text-[11px] font-normal text-slate-800">
                            <span>Montant Versé ({{ $lastSale->payment_method }}):</span>
                            <span>{{ number_format($lastSale->amount_paid, 0, ',', ' ') }} FC</span>
                        </div>
                        <div class="flex justify-between text-[11px] font-normal text-slate-800">
                            <span>Monnaie Rendue:</span>
                            <span>{{ number_format($lastSale->change_amount, 0, ',', ' ') }} FC</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 no-print-element">
                    <button 
                        onclick="window.print()" 
                        class="flex-1 py-3 bg-[#00c9a7] hover:bg-[#00b899] text-white font-extrabold text-xs rounded-xl shadow-lg shadow-[#00c9a7]/20 flex items-center justify-center gap-2 transition-all"
                    >
                        <i class="fa-solid fa-print"></i> Imprimer Ticket (Imprimante Thermique)
                    </button>
                    <button 
                        wire:click="closeReceiptModal" 
                        class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl border border-slate-300 transition-all"
                    >
                        Fermer
                    </button>
                </div>

            </div>
        </div>
    @endif

    <!-- Open Cash Register Session Modal -->
    @if($showOpenSessionModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop animate-fadeIn">
            <div class="bg-white border border-slate-200 rounded-3xl max-w-md w-full p-6 shadow-2xl relative">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-200">
                    <div class="w-10 h-10 rounded-2xl bg-[#00c9a7]/15 text-[#00c9a7] flex items-center justify-center text-xl">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-lg text-[#0f172a]">Ouverture de Session Caisse</h3>
                        <p class="text-xs text-slate-500">Veuillez spécifier le fond de caisse disponible en tiroir</p>
                    </div>
                </div>

                <form wire:submit.prevent="confirmOpenSession" class="space-y-4 pt-4">
                    <div>
                        <label class="text-xs font-extrabold text-slate-700 block mb-1">Fond de Caisse Initial (FC) *</label>
                        <input 
                            type="number" 
                            wire:model="openingBalance" 
                            step="any" 
                            min="0"
                            placeholder="ex: 50000"
                            class="w-full px-4 py-3 bg-white border-2 border-slate-300 rounded-2xl text-base font-mono font-black text-[#05a88b] focus:border-[#00c9a7] focus:ring-2 focus:ring-[#00c9a7]/20 focus:outline-none"
                            required
                        >
                        @error('openingBalance') <span class="text-rose-600 text-xs font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="text-xs font-extrabold text-slate-700 block mb-1">Note / Observation (Optionnel)</label>
                        <textarea 
                            wire:model="sessionNotes" 
                            rows="2"
                            placeholder="ex: Billets et pièces de monnaie contrôlés"
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] focus:border-[#00c9a7] focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="pt-2 flex items-center gap-3">
                        <button 
                            type="submit" 
                            class="flex-1 py-3 bg-[#00c9a7] hover:bg-[#00b899] text-white font-extrabold text-xs rounded-xl shadow-lg shadow-[#00c9a7]/20 flex items-center justify-center gap-2 transition-all"
                        >
                            <i class="fa-solid fa-lock-open"></i> Valider & Démarrer la Caisse
                        </button>
                        <button 
                            type="button"
                            wire:click="$set('showOpenSessionModal', false)" 
                            class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl border border-slate-300 transition-all"
                        >
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Close Cash Register Session Modal (Z de Caisse) -->
    @if($showCloseSessionModal && $activeSession)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop animate-fadeIn">
            <div class="bg-white border border-slate-200 rounded-3xl max-w-lg w-full p-6 shadow-2xl relative">
                <div class="flex items-center gap-3 pb-4 border-b border-slate-200">
                    <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center text-xl border border-amber-300">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-lg text-[#0f172a]">Clôture de Caisse (Z de Caisse)</h3>
                        <p class="text-xs text-slate-500">Comptage physique final & calcul d'écart de tiroir</p>
                    </div>
                </div>

                <div class="my-4 p-3.5 bg-slate-50 rounded-2xl border border-slate-200 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-600 font-bold">
                        <span>Fond de caisse initial:</span>
                        <span class="font-mono text-[#0f172a] font-extrabold">{{ number_format($activeSession->opening_balance, 0, ',', ' ') }} FC</span>
                    </div>
                    <div class="flex justify-between text-slate-600 font-bold">
                        <span>Ventes de la session (Espèces):</span>
                        <span class="font-mono text-[#05a88b] font-extrabold">+ {{ number_format($this->getSessionCashSales(), 0, ',', ' ') }} FC</span>
                    </div>
                    <div class="flex justify-between text-slate-800 font-black border-t border-slate-200 pt-2 text-sm">
                        <span>Solde Théorique attendu dans le tiroir:</span>
                        <span class="font-mono text-[#05a88b] font-black">{{ number_format($activeSession->opening_balance + $this->getSessionCashSales(), 0, ',', ' ') }} FC</span>
                    </div>
                </div>

                <form wire:submit.prevent="confirmCloseSession" class="space-y-4">
                    <div>
                        <label class="text-xs font-extrabold text-slate-700 block mb-1">Comptage Physique Réel du Tiroir (FC) *</label>
                        <input 
                            type="number" 
                            wire:model.live.debounce.300ms="closingBalance" 
                            step="any" 
                            min="0"
                            placeholder="Saisir le montant exact compté physiquement..."
                            class="w-full px-4 py-3 bg-white border-2 border-slate-300 rounded-2xl text-base font-mono font-black text-[#0f172a] focus:border-[#00c9a7] focus:ring-2 focus:ring-[#00c9a7]/20 focus:outline-none"
                            required
                        >
                        @error('closingBalance') <span class="text-rose-600 text-xs font-bold">{{ $message }}</span> @enderror
                    </div>

                    @if(numeric_val($closingBalance) > 0)
                        @php
                            $expected = $activeSession->opening_balance + $this->getSessionCashSales();
                            $diff = numeric_val($closingBalance) - $expected;
                        @endphp
                        <div class="p-3 rounded-2xl border text-xs font-bold flex items-center justify-between {{ $diff === 0 ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : ($diff > 0 ? 'bg-cyan-50 text-cyan-800 border-cyan-300' : 'bg-rose-50 text-rose-800 border-rose-300') }}">
                            <span>Écart de Caisse:</span>
                            <span class="font-mono font-black text-sm">
                                {{ $diff > 0 ? '+' : '' }}{{ number_format($diff, 0, ',', ' ') }} FC
                                @if($diff === 0) (Parfait 👍) @elseif($diff > 0) (Excédent) @else (Manquant / Perte) @endif
                            </span>
                        </div>
                    @endif

                    <div>
                        <label class="text-xs font-extrabold text-slate-700 block mb-1">Raison de l'écart / Remarques finales</label>
                        <textarea 
                            wire:model="sessionNotes" 
                            rows="2"
                            placeholder="ex: Écart justifié par rendu de monnaie..."
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] focus:border-[#00c9a7] focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="pt-2 flex items-center gap-3">
                        <button 
                            type="submit" 
                            class="flex-1 py-3 bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs rounded-xl shadow-lg flex items-center justify-center gap-2 transition-all"
                        >
                            <i class="fa-solid fa-lock"></i> Valider le Z & Fermer la Caisse
                        </button>
                        <button 
                            type="button"
                            wire:click="$set('showCloseSessionModal', false)" 
                            class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl border border-slate-300 transition-all"
                        >
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Custom Client Requisition Modal -->
    @if($showCustomReqModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-backdrop animate-fadeIn">
            <div class="bg-white border border-slate-200 rounded-3xl max-w-md w-full p-6 shadow-2xl relative">
                
                <div class="flex items-center gap-3 pb-4 border-b border-slate-200">
                    <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center text-xl border border-rose-300">
                        <i class="fa-solid fa-cart-flatbed"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-extrabold text-lg text-[#0f172a]">Réquisition / Produit Demandé</h3>
                        <p class="text-xs text-slate-500">Enregistrer un besoin client non satisfait en stock</p>
                    </div>
                </div>

                <form wire:submit.prevent="saveCustomRequisition" class="space-y-4 pt-4">
                    <div>
                        <label class="text-xs font-extrabold text-slate-700 block mb-1">Nom du Produit / Médicament *</label>
                        <input 
                            type="text" 
                            wire:model="reqProductName" 
                            placeholder="ex: Coartem 20/120mg" 
                            class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-extrabold text-[#0f172a] focus:border-[#00c9a7] focus:ring-2 focus:ring-[#00c9a7]/20 focus:outline-none"
                            required
                        >
                        @error('reqProductName') <span class="text-rose-600 text-xs font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="text-xs font-extrabold text-slate-700 block mb-1">Quantité Demandée par le Client *</label>
                        <input 
                            type="number" 
                            wire:model="reqQuantity" 
                            min="1" 
                            class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs font-mono font-extrabold text-[#0f172a] focus:border-[#00c9a7] focus:ring-2 focus:ring-[#00c9a7]/20 focus:outline-none"
                            required
                        >
                        @error('reqQuantity') <span class="text-rose-600 text-xs font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="text-xs font-extrabold text-slate-700 block mb-1">Notes / Détails (Optionnel)</label>
                        <textarea 
                            wire:model="reqNotes" 
                            rows="2"
                            placeholder="ex: Demande urgente pour le patient M. Kasongo" 
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-bold text-[#0f172a] focus:border-[#00c9a7] focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="pt-2 flex items-center gap-3">
                        <button 
                            type="submit" 
                            class="flex-1 py-3 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl shadow-lg flex items-center justify-center gap-2 transition-all"
                        >
                            <i class="fa-solid fa-paper-plane"></i> Transmettre la Réquisition
                        </button>
                        <button 
                            type="button" 
                            wire:click="$set('showCustomReqModal', false)" 
                            class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl border border-slate-300 transition-all"
                        >
                            Annuler
                        </button>
                    </div>
                </form>

            </div>
        </div>
    @endif

</div>
