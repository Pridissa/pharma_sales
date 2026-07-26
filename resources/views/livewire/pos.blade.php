<div class="h-full flex flex-col lg:flex-row gap-6">

    <!-- Left Column: Catalog & Search -->
    <div class="flex-1 flex flex-col min-w-0">

        <!-- Cash Session Status Banner -->
        <div class="mb-3 p-3 rounded-2xl glass-card border border-slate-800 flex items-center justify-between gap-3 text-xs">
            @if($activeSession)
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></span>
                    <div>
                        <span class="font-bold text-white">Session Caisse Active #{{ $activeSession->id }}</span>
                        <span class="text-slate-400 text-[11px] block">Ouverte à {{ $activeSession->opened_at->format('H:i') }} • Fond initial : <span class="font-mono text-emerald-400 font-bold">{{ number_format($activeSession->opening_balance, 0, ',', ' ') }} FC</span></span>
                    </div>
                </div>
                <button 
                    wire:click="openCloseSessionModal" 
                    class="px-3 py-1.5 rounded-xl bg-amber-500/20 hover:bg-amber-500 text-amber-300 hover:text-slate-950 font-bold border border-amber-500/30 transition-all flex items-center gap-1.5"
                >
                    <i class="fa-solid fa-lock"></i> Clôturer Caisse (Z)
                </button>
            @else
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                    <div>
                        <span class="font-bold text-rose-400">Aucune Session Caisse Ouverte</span>
                        <span class="text-slate-400 text-[11px] block">Veuillez ouvrir votre session avec le fond de caisse initial</span>
                    </div>
                </div>
                <button 
                    wire:click="openSessionModal" 
                    class="px-3.5 py-1.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold shadow-md shadow-emerald-500/20 transition-all flex items-center gap-1.5"
                >
                    <i class="fa-solid fa-key"></i> Ouvrir Session Caisse
                </button>
            @endif
        </div>

        <!-- Search Bar & Category Pills -->
        <div class="mb-4 space-y-3">
            <!-- Search input -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-emerald-400">
                    <i class="fa-solid fa-magnifying-glass text-lg"></i>
                </div>
                <input 
                    wire:model.live.debounce.250ms="search" 
                    type="text" 
                    placeholder="Rechercher par nom (ex: Paracétamol), DCI ou Code-barres..."
                    class="w-full pl-12 pr-10 py-3.5 bg-slate-900/90 border border-slate-700/80 rounded-2xl text-white placeholder-slate-400 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 shadow-xl transition-all font-medium"
                    autofocus
                >
                @if($search)
                    <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-white">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                @endif
            </div>

            <!-- Categories pills & Client requisition trigger -->
            <div class="flex items-center justify-between gap-2 overflow-x-auto scrollbar-thin pb-1">
                <div class="flex items-center gap-2">
                    <button 
                        wire:click="$set('selectedCategory', null)" 
                        class="px-3.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-200 {{ is_null($selectedCategory) ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/20 font-bold' : 'glass-card text-slate-300 hover:bg-slate-800' }}"
                    >
                        Tous les médicaments
                    </button>
                    @foreach($categories as $category)
                        <button 
                            wire:click="$set('selectedCategory', {{ $category->id }})" 
                            class="px-3.5 py-1.5 rounded-xl text-xs font-semibold whitespace-nowrap transition-all duration-200 {{ $selectedCategory === $category->id ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/20 font-bold' : 'glass-card text-slate-300 hover:bg-slate-800' }}"
                        >
                            {{ $category->name }} ({{ $category->products_count }})
                        </button>
                    @endforeach
                </div>

                <button 
                    wire:click="openCustomReqModal('')" 
                    class="px-3.5 py-1.5 rounded-xl text-xs font-bold whitespace-nowrap bg-rose-500/20 hover:bg-rose-500 text-rose-300 hover:text-slate-950 border border-rose-500/30 transition-all flex items-center gap-1.5 shrink-0"
                >
                    <i class="fa-solid fa-cart-flatbed"></i> Produit Absent ? Réquisition
                </button>
            </div>
        </div>

        <!-- Alert messages -->
        @if($errorMessage)
            <div class="mb-4 p-3.5 bg-rose-500/15 border border-rose-500/30 rounded-2xl text-rose-300 text-xs font-medium flex items-center justify-between animate-fadeIn">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-exclamation text-base"></i>
                    <span>{{ $errorMessage }}</span>
                </div>
                <button wire:click="$set('errorMessage', '')" class="text-rose-400 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if($successMessage)
            <div class="mb-4 p-3.5 bg-emerald-500/15 border border-emerald-500/30 rounded-2xl text-emerald-300 text-xs font-medium flex items-center justify-between animate-fadeIn">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-base"></i>
                    <span>{{ $successMessage }}</span>
                </div>
                <button wire:click="$set('successMessage', '')" class="text-emerald-400 hover:text-white">
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
                        class="glass-card hover:bg-slate-800/80 p-4 rounded-2xl cursor-pointer border border-slate-800/80 hover:border-emerald-500/40 transition-all duration-200 flex flex-col justify-between group relative overflow-hidden shadow-md hover:shadow-emerald-500/5 {{ $product->stock_quantity <= 0 ? 'opacity-60 grayscale cursor-not-allowed' : '' }}"
                    >
                        @if($product->requires_prescription)
                            <span class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded-md bg-amber-500/20 border border-amber-500/30 text-amber-300 text-[10px] font-semibold flex items-center gap-1">
                                <i class="fa-solid fa-file-prescription text-[9px]"></i> Ordonnance
                            </span>
                        @endif

                        <div>
                            <div class="text-[11px] text-slate-400 font-medium mb-1 truncate">
                                {{ $product->category->name }}
                            </div>
                            <h3 class="font-heading font-bold text-slate-100 text-sm group-hover:text-emerald-400 transition-colors line-clamp-1">
                                {{ $product->name }}
                            </h3>
                            @if($product->dci)
                                <p class="text-[11px] text-emerald-400/80 italic font-mono truncate mb-2">
                                    DCI: {{ $product->dci }}
                                </p>
                            @endif
                            <p class="text-[11px] text-slate-400">
                                {{ $product->dosage_unit ?: 'Unité' }}
                            </p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-800/60 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-emerald-400 font-mono text-base">
                                    {{ number_format($product->price, 0, ',', ' ') }} FC
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[11px] px-2 py-0.5 rounded-lg font-medium {{ $product->stock_quantity > $product->min_stock_alert ? 'bg-slate-800 text-slate-300' : ($product->stock_quantity > 0 ? 'bg-amber-500/20 text-amber-300' : 'bg-rose-500/20 text-rose-400') }}">
                                    Stock: {{ $product->stock_quantity }}
                                </span>
                                @if($product->stock_quantity > 0)
                                    <button class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 group-hover:bg-emerald-500 group-hover:text-slate-950 flex items-center justify-center transition-colors">
                                        <i class="fa-solid fa-plus text-xs"></i>
                                    </button>
                                @else
                                    <button 
                                        wire:click.stop="requestRequisition({{ $product->id }})" 
                                        title="Enregistrer une demande d'approvisionnement"
                                        class="px-2 py-1 rounded-lg bg-rose-500/20 hover:bg-rose-500 text-rose-300 hover:text-slate-950 flex items-center gap-1 text-[10px] font-bold transition-colors z-10"
                                    >
                                        <i class="fa-solid fa-cart-flatbed text-[10px]"></i> Réquisition
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full glass-card rounded-2xl p-10 text-center space-y-4">
                        <i class="fa-solid fa-box-open text-4xl text-slate-600"></i>
                        <div class="space-y-1">
                            <p class="text-slate-300 font-bold text-base">Aucun médicament trouvé pour cette recherche.</p>
                            <p class="text-slate-400 text-xs">Le produit demandé par le client n'est peut-être pas présent en stock.</p>
                        </div>
                        @if($search)
                            <div>
                                <button 
                                    wire:click="openCustomReqModal('{{ addslashes($search) }}')"
                                    class="px-4 py-2.5 bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-400 hover:to-pink-400 text-white font-bold text-xs rounded-xl shadow-lg shadow-rose-500/20 inline-flex items-center gap-2 transition-all cursor-pointer"
                                >
                                    <i class="fa-solid fa-cart-flatbed text-sm"></i>
                                    <span>Ajouter une Réquisition Client pour "{{ $search }}"</span>
                                </button>
                            </div>
                        @else
                            <div>
                                <button 
                                    wire:click="openCustomReqModal('')"
                                    class="px-4 py-2.5 bg-gradient-to-r from-rose-500 to-pink-500 hover:from-rose-400 hover:to-pink-400 text-white font-bold text-xs rounded-xl shadow-lg shadow-rose-500/20 inline-flex items-center gap-2 transition-all cursor-pointer"
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
    <div class="w-full lg:w-96 flex flex-col glass-panel rounded-3xl p-5 border border-slate-800/80 shadow-2xl">
        
        <!-- Cart Header -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-basket-shopping text-emerald-400 text-lg"></i>
                <h3 class="font-heading font-bold text-white text-base">Panier de Vente</h3>
                <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-bold font-mono">
                    {{ count($cart) }}
                </span>
            </div>
            @if(count($cart) > 0)
                <button wire:click="clearCart" class="text-xs text-rose-400 hover:text-rose-300 font-medium transition-colors">
                    <i class="fa-solid fa-trash-can mr-1"></i> Vider
                </button>
            @endif
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 overflow-y-auto scrollbar-thin py-3 space-y-2.5 my-2 min-h-[180px] max-h-[300px] lg:max-h-none">
            @forelse($cart as $id => $item)
                <div class="glass-card p-3 rounded-xl border border-slate-800 flex items-center justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-xs font-bold text-slate-100 truncate">{{ $item['name'] }}</h4>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-[11px] font-mono text-emerald-400 font-semibold">
                                {{ number_format($item['price'], 0, ',', ' ') }} FC
                            </span>
                            @if($item['requires_prescription'])
                                <span class="text-[9px] px-1.5 py-0.2 rounded bg-amber-500/20 text-amber-300 font-bold">Ord.</span>
                            @endif
                        </div>
                    </div>

                    <!-- Quantity controls -->
                    <div class="flex items-center gap-1.5 bg-slate-950/70 p-1 rounded-xl border border-slate-800">
                        <button 
                            wire:click="updateQuantity({{ $id }}, {{ $item['qty'] - 1 }})" 
                            class="w-6 h-6 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center text-xs font-bold transition-colors"
                        >
                            -
                        </button>
                        <span class="w-6 text-center text-xs font-mono font-bold text-white">{{ $item['qty'] }}</span>
                        <button 
                            wire:click="updateQuantity({{ $id }}, {{ $item['qty'] + 1 }})" 
                            class="w-6 h-6 rounded-lg bg-emerald-500/20 hover:bg-emerald-500 text-emerald-400 hover:text-slate-950 flex items-center justify-center text-xs font-bold transition-colors"
                        >
                            +
                        </button>
                    </div>

                    <!-- Subtotal & Remove -->
                    <div class="text-right min-w-[70px]">
                        <div class="text-xs font-mono font-bold text-white">
                            {{ number_format($item['subtotal'], 0, ',', ' ') }} FC
                        </div>
                        <button wire:click="removeFromCart({{ $id }})" class="text-[10px] text-slate-400 hover:text-rose-400 transition-colors mt-0.5">
                            Supprimer
                        </button>
                    </div>
                </div>
            @empty
                <div class="h-full flex flex-col items-center justify-center text-slate-400 py-12">
                    <i class="fa-solid fa-cart-shopping text-3xl mb-2 text-slate-600"></i>
                    <p class="text-xs">Sélectionnez un médicament pour l'ajouter au panier.</p>
                </div>
            @endforelse
        </div>

        <!-- Prescription Warning Banner -->
        @if($this->hasPrescriptionItem())
            <div class="mb-3 p-2.5 bg-amber-500/10 border border-amber-500/30 rounded-xl text-amber-300 text-xs flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-amber-400 text-sm"></i>
                <div class="flex-1">
                    <span class="font-bold">Présence de produit sur ordonnance !</span>
                </div>
            </div>
        @endif

        <!-- Customer & Prescriber Info -->
        <div class="space-y-2 py-2 border-t border-slate-800">
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Patient (Nom)</label>
                    <input 
                        type="text" 
                        wire:model.live="patientName" 
                        placeholder="Optionnel..." 
                        class="w-full px-2.5 py-1.5 bg-slate-900 border border-slate-800 rounded-xl text-xs text-white placeholder-slate-500 focus:border-emerald-500 focus:outline-none"
                    >
                </div>
                <div>
                    <label class="text-[10px] font-semibold uppercase tracking-wider {{ $this->hasPrescriptionItem() ? 'text-amber-400 font-bold' : 'text-slate-400' }}">
                        Médecin Prescripteur {{ $this->hasPrescriptionItem() ? '*' : '' }}
                    </label>
                    <input 
                        type="text" 
                        wire:model.live="doctorName" 
                        placeholder="{{ $this->hasPrescriptionItem() ? 'Obligatoire *' : 'Optionnel...' }}" 
                        class="w-full px-2.5 py-1.5 bg-slate-900 border {{ $this->hasPrescriptionItem() && empty($doctorName) ? 'border-amber-500/80 ring-1 ring-amber-500/30' : 'border-slate-800' }} rounded-xl text-xs text-white placeholder-slate-500 focus:border-emerald-500 focus:outline-none"
                    >
                </div>
            </div>

            <!-- Payment Method & Amount Paid -->
            <div class="space-y-2 pt-1">
                <div>
                    <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Mode Règlement</label>
                    <div class="grid grid-cols-2 gap-2 mt-1">
                        <button 
                            type="button" 
                            wire:click="$set('paymentMethod', 'Espèces')"
                            class="py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-center gap-2 {{ $paymentMethod === 'Espèces' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/50 shadow-md shadow-emerald-500/10' : 'bg-slate-900 text-slate-400 border-slate-800 hover:bg-slate-800' }}"
                        >
                            <i class="fa-solid fa-money-bill-wave"></i> Espèces
                        </button>
                        <button 
                            type="button" 
                            wire:click="$set('paymentMethod', 'Mobile Money')"
                            class="py-2 px-3 rounded-xl border text-xs font-bold transition-all flex items-center justify-center gap-2 {{ $paymentMethod === 'Mobile Money' ? 'bg-cyan-500/20 text-cyan-400 border-cyan-500/50 shadow-md shadow-cyan-500/10' : 'bg-slate-900 text-slate-400 border-slate-800 hover:bg-slate-800' }}"
                        >
                            <i class="fa-solid fa-mobile-screen-button"></i> Mobile Money
                        </button>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <label class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Montant Versé (FC)</label>
                        <button 
                            type="button" 
                            wire:click="setAmountPaid({{ $total }})" 
                            class="text-[10px] font-bold text-emerald-400 hover:underline"
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
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-sm text-emerald-400 font-mono font-bold focus:border-emerald-500 focus:outline-none"
                    >
                    <!-- Quick Amount Addition Pills -->
                    <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-thin pt-1.5">
                        <button type="button" wire:click="addAmountPaid(500)" class="px-2 py-1 bg-slate-900 hover:bg-slate-800 border border-slate-800 rounded-lg text-[10px] font-mono text-slate-300">+500</button>
                        <button type="button" wire:click="addAmountPaid(1000)" class="px-2 py-1 bg-slate-900 hover:bg-slate-800 border border-slate-800 rounded-lg text-[10px] font-mono text-slate-300">+1 000</button>
                        <button type="button" wire:click="addAmountPaid(5000)" class="px-2 py-1 bg-slate-900 hover:bg-slate-800 border border-slate-800 rounded-lg text-[10px] font-mono text-slate-300">+5 000</button>
                        <button type="button" wire:click="addAmountPaid(10000)" class="px-2 py-1 bg-slate-900 hover:bg-slate-800 border border-slate-800 rounded-lg text-[10px] font-mono text-slate-300">+10 000</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Breakdown & Complete Sale Button -->
        <div class="pt-3 border-t border-slate-800 space-y-2">
            <div class="flex items-center justify-between text-xs text-slate-400">
                <span>Sous-total:</span>
                <span class="font-mono text-slate-200">{{ number_format($total, 0, ',', ' ') }} FC</span>
            </div>
            
            <div class="flex items-center justify-between text-xs text-slate-400">
                <span>Monnaie à rendre:</span>
                <span class="font-mono font-bold {{ $this->changeAmount() > 0 ? 'text-amber-400' : 'text-slate-400' }}">
                    {{ number_format($this->changeAmount(), 0, ',', ' ') }} FC
                </span>
            </div>

            <div class="flex items-center justify-between pt-1 text-base font-extrabold text-white">
                <span>Total à Payer:</span>
                <span class="text-xl font-mono text-emerald-400">
                    {{ number_format($total, 0, ',', ' ') }} FC
                </span>
            </div>

            <button 
                wire:click="completeSale" 
                @if(count($cart) === 0) disabled @endif
                class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-bold text-sm shadow-xl shadow-emerald-500/20 hover:shadow-emerald-500/30 transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <i class="fa-solid fa-check-circle text-base"></i>
                Valider & Imprimer Ticket
            </button>
        </div>

    </div>

    <!-- Printable Receipt Modal -->
    @if($showReceiptModal && $lastSale)
        <div id="printable-receipt-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-fadeIn">
            <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl relative">
                
                <button wire:click="closeReceiptModal" class="absolute top-4 right-4 text-slate-400 hover:text-white no-print-element">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <div class="text-center mb-6 no-print-element">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 mx-auto flex items-center justify-center text-2xl mb-2">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <h3 class="font-heading font-bold text-xl text-white">Ticket de Caisse Validé</h3>
                    <p class="text-xs text-emerald-400 font-mono font-semibold">{{ $lastSale->invoice_number }}</p>
                </div>

                <!-- Receipt Printable Area -->
                <div id="printable-receipt" class="bg-white text-slate-900 p-6 rounded-xl font-mono text-xs shadow-inner space-y-3 mb-6">
                    <div class="text-center border-b border-dashed border-slate-400 pb-3">
                        <h2 class="font-bold text-base tracking-wider uppercase text-slate-950">BITA PHARMA</h2>
                        <p class="text-[10px] italic font-serif text-slate-700 font-bold mb-1">"La confiance au cœur de vos soins"</p>
                        <p class="text-[10px] font-semibold text-slate-800">Tél: +243 80 88 58 326 / +243 99 45 50 510</p>
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
                                <span class="truncate max-w-[180px] font-medium">{{ $item->quantity }}x {{ $item->product_name }}</span>
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

                    <div class="text-center pt-3 border-t border-dashed border-slate-400 text-[9px] italic text-slate-700">
                        Merci de votre confiance. Bon rétablissement !
                    </div>
                </div>

                <div class="flex items-center gap-3 no-print-element">
                    <button onclick="window.print()" class="flex-1 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs flex items-center justify-center gap-2">
                        <i class="fa-solid fa-print"></i> Imprimer Ticket
                    </button>
                    <button wire:click="closeReceiptModal" class="px-4 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs">
                        Fermer
                    </button>
                </div>

            </div>
        </div>
    @endif

    <!-- Custom Requisition Modal (For Client missing/unregistered product requests) -->
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

    <!-- Open Cash Session Modal -->
    @if($showOpenSessionModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-fadeIn">
            <div class="bg-slate-900 border border-emerald-500/40 rounded-3xl max-w-md w-full p-6 shadow-2xl relative space-y-4">
                <button wire:click="$set('showOpenSessionModal', false)" class="absolute top-4 right-4 text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <div class="flex items-center gap-3 pb-3 border-b border-slate-800">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-lg text-white">Ouverture de Session Caisse</h3>
                        <p class="text-xs text-slate-400">Déclarer le fond de caisse initial pour le service</p>
                    </div>
                </div>

                <form wire:submit.prevent="saveOpenSession" class="space-y-4 text-xs">
                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Fond de caisse initial (FC) *</label>
                        <input 
                            type="number" 
                            step="100" 
                            wire:model="openingBalance" 
                            class="w-full px-4 py-3 bg-slate-950 border border-slate-700 rounded-2xl text-white font-mono text-lg font-bold focus:border-emerald-500 focus:outline-none" 
                            required
                        >
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Notes d'ouverture (Optionnel)</label>
                        <textarea 
                            wire:model="openingNotes" 
                            rows="2" 
                            placeholder="ex: Fond constitué de billets de 10 000 FC..." 
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                        <button type="button" wire:click="$set('showOpenSessionModal', false)" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold">
                            Annuler
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold shadow-lg shadow-emerald-500/20 flex items-center gap-2">
                            <i class="fa-solid fa-check"></i> Confirmer Ouverture Caisse
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Close Cash Session Modal (Z de Caisse) -->
    @if($showCloseSessionModal && $activeSession)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md animate-fadeIn">
            <div class="bg-slate-900 border border-amber-500/40 rounded-3xl max-w-md w-full p-6 shadow-2xl relative space-y-4">
                <button wire:click="$set('showCloseSessionModal', false)" class="absolute top-4 right-4 text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>

                <div class="flex items-center gap-3 pb-3 border-b border-slate-800">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-400 border border-amber-500/30 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-calculator"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-lg text-white">Clôture de Caisse (Z-de-Caisse)</h3>
                        <p class="text-xs text-amber-400 font-mono font-bold">Session #{{ $activeSession->id }}</p>
                    </div>
                </div>

                <form wire:submit.prevent="saveCloseSession" class="space-y-4 text-xs">
                    <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 space-y-2 font-mono">
                        <div class="flex justify-between text-slate-400">
                            <span>Fond initial :</span>
                            <span class="font-bold text-white">{{ number_format($activeSession->opening_balance, 0, ',', ' ') }} FC</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>Ventes Espèces encaissées :</span>
                            <span class="font-bold text-emerald-400">+{{ number_format(\App\Models\Sale::where('cash_session_id', $activeSession->id)->where('payment_method', 'Espèces')->sum('total_amount'), 0, ',', ' ') }} FC</span>
                        </div>
                        <div class="pt-2 border-t border-slate-800 flex justify-between text-sm">
                            <span class="font-bold text-slate-200">Total Attendu en Caisse :</span>
                            <span class="font-bold text-amber-300">
                                {{ number_format($activeSession->opening_balance + \App\Models\Sale::where('cash_session_id', $activeSession->id)->where('payment_method', 'Espèces')->sum('total_amount'), 0, ',', ' ') }} FC
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Montant Espèces Physiquement Compté dans le Tiroir (FC) *</label>
                        <input 
                            type="number" 
                            step="100" 
                            wire:model="closingActualCash" 
                            class="w-full px-4 py-3 bg-slate-950 border border-slate-700 rounded-2xl text-white font-mono text-lg font-bold focus:border-amber-500 focus:outline-none" 
                            required
                        >
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-300 mb-1">Notes de clôture / Écart éventuel</label>
                        <textarea 
                            wire:model="closingNotes" 
                            rows="2" 
                            placeholder="ex: Comptage régulier fin de service..." 
                            class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                        <button type="button" wire:click="$set('showCloseSessionModal', false)" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold">
                            Annuler
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold shadow-lg shadow-amber-500/20 flex items-center gap-2">
                            <i class="fa-solid fa-lock"></i> Valider Clôture Z
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
