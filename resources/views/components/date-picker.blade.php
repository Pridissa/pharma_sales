@props([
    'placeholder' => 'Sélectionner une date...',
    'inputClass' => 'w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-mono font-bold text-[#0f172a] focus:border-[#00c9a7] focus:outline-none cursor-pointer'
])

<div 
    wire:ignore 
    x-data="{
        value: @entangle($attributes->wire('model')),
        fp: null,
        init() {
            this.fp = flatpickr(this.$refs.input, {
                locale: 'fr',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd/m/Y',
                defaultDate: this.value || '',
                onChange: (selectedDates, dateStr) => {
                    this.value = dateStr;
                }
            });
            this.$watch('value', (newVal) => {
                if (this.fp) {
                    if (newVal) {
                        this.fp.setDate(newVal, false);
                    } else {
                        this.fp.clear();
                    }
                }
            });
        }
    }"
    class="relative"
>
    <input 
        x-ref="input" 
        type="text" 
        placeholder="{{ $placeholder }}"
        class="{{ $inputClass }}"
    >
    <i class="fa-solid fa-calendar-days absolute right-3 top-2.5 text-slate-400 pointer-events-none z-10"></i>
</div>
