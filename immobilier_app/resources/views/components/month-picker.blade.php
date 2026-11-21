@props([
    'value' => now()->format('Y-m'),
    'label' => now()->translatedFormat('F Y'),
    'action' => url()->current(),
    'resetUrl' => url()->current(),
    'formId' => 'monthFilterForm',
])

@php
    $parts = explode('-', $value);
    $initialYear = (int)($parts[0] ?? date('Y'));
    $initialMonth = (int)($parts[1] ?? date('m'));
    $isFiltered = request()->has('mois') && request('mois') !== now()->format('Y-m');
@endphp

<div class="relative inline-flex items-center select-none" 
     x-data="{
        open: false,
        currentValue: '{{ $value }}',
        selectedYear: {{ $initialYear }},
        selectedMonth: {{ $initialMonth }},
        navYear: {{ $initialYear }},
        realYear: new Date().getFullYear(),
        realMonth: new Date().getMonth() + 1,
        months: [
            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
        ],
        isSelected(m) {
            return this.selectedYear === this.navYear && this.selectedMonth === m;
        },
        isRealCurrentMonth(m) {
            return this.realYear === this.navYear && this.realMonth === m;
        },
        prevYear() {
            this.navYear--;
        },
        nextYear() {
            this.navYear++;
        },
        selectMonth(m) {
            const mm = String(m).padStart(2, '0');
            this.submitMonth(`${this.navYear}-${mm}`);
        },
        prevMonth() {
            let y = this.selectedYear;
            let m = this.selectedMonth - 1;
            if (m < 1) { m = 12; y--; }
            const mm = String(m).padStart(2, '0');
            this.submitMonth(`${y}-${mm}`);
        },
        nextMonth() {
            let y = this.selectedYear;
            let m = this.selectedMonth + 1;
            if (m > 12) { m = 1; y++; }
            const mm = String(m).padStart(2, '0');
            this.submitMonth(`${y}-${mm}`);
        },
        goToday() {
            const mm = String(this.realMonth).padStart(2, '0');
            this.submitMonth(`${this.realYear}-${mm}`);
        },
        submitMonth(val) {
            this.currentValue = val;
            this.$nextTick(() => {
                const form = document.getElementById('{{ $formId }}');
                if (form) {
                    const input = form.querySelector('input[name=\'mois\']');
                    if (input) input.value = val;
                    form.submit();
                }
            });
        }
     }">

    <!-- Formulaire sous-jacent -->
    <form id="{{ $formId }}" method="GET" action="{{ $action }}" class="hidden">
        <input type="hidden" name="mois" x-ref="inputMois" :value="currentValue">
    </form>

    <!-- Barre de contrôle moderne (Pill) -->
    <div class="flex items-center gap-1 bg-white border border-slate-200 rounded-2xl p-1 shadow-sm hover:border-slate-300 transition-all">
        <!-- Bouton Mois Précédent (Navigation instantanée 1 clic) -->
        <button type="button" 
                @click="prevMonth()" 
                title="Mois précédent" 
                class="w-8 h-8 rounded-xl text-slate-500 hover:text-blue-600 hover:bg-blue-50/80 flex items-center justify-center transition-all cursor-pointer">
            <i class="fas fa-chevron-left text-xs"></i>
        </button>

        <!-- Bouton Calendrier Principal -->
        <button type="button" 
                @click="open = !open" 
                class="px-3.5 py-1.5 rounded-xl hover:bg-slate-50 flex items-center gap-2.5 text-xs font-black uppercase tracking-wider text-slate-800 transition-all cursor-pointer">
            <div class="w-6 h-6 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                <i class="far fa-calendar-alt"></i>
            </div>
            <span class="capitalize">{{ $label }}</span>
            <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
        </button>

        <!-- Bouton Mois Suivant (Navigation instantanée 1 clic) -->
        <button type="button" 
                @click="nextMonth()" 
                title="Mois suivant" 
                class="w-8 h-8 rounded-xl text-slate-500 hover:text-blue-600 hover:bg-blue-50/80 flex items-center justify-center transition-all cursor-pointer">
            <i class="fas fa-chevron-right text-xs"></i>
        </button>

        @if($isFiltered)
        <!-- Bouton Réinitialiser au mois en cours -->
        <a href="{{ $resetUrl }}" 
           title="Revenir au mois en cours" 
           class="w-8 h-8 rounded-xl text-slate-400 hover:text-blue-600 hover:bg-blue-50 flex items-center justify-center transition-all ml-0.5">
            <i class="fas fa-rotate-left text-xs"></i>
        </a>
        @endif
    </div>

    <!-- Popover Calendrier Moderne -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
         @click.outside="open = false"
         x-cloak
         class="absolute right-0 top-full mt-2 w-72 sm:w-80 bg-white rounded-2xl shadow-2xl border border-slate-100 p-4 z-50">
        
        <!-- En-tête : Sélecteur d'Année -->
        <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
            <button type="button" 
                    @click="prevYear()" 
                    class="w-8 h-8 rounded-xl hover:bg-slate-100 text-slate-500 hover:text-slate-900 flex items-center justify-center transition-colors cursor-pointer"
                    title="Année précédente">
                <i class="fas fa-chevron-left text-xs"></i>
            </button>
            
            <div class="flex items-center gap-2">
                <i class="far fa-calendar text-blue-600 text-xs"></i>
                <span class="text-sm font-black text-slate-900 tracking-wider" x-text="navYear"></span>
            </div>
            
            <button type="button" 
                    @click="nextYear()" 
                    class="w-8 h-8 rounded-xl hover:bg-slate-100 text-slate-500 hover:text-slate-900 flex items-center justify-center transition-colors cursor-pointer"
                    title="Année suivante">
                <i class="fas fa-chevron-right text-xs"></i>
            </button>
        </div>

        <!-- Grille des 12 Mois -->
        <div class="grid grid-cols-3 gap-2">
            <template x-for="(mName, idx) in months" :key="idx">
                <button type="button" 
                        @click="selectMonth(idx + 1)"
                        :class="{
                            'bg-blue-600 text-white font-black shadow-md shadow-blue-500/25': isSelected(idx + 1),
                            'text-slate-700 hover:bg-blue-50 hover:text-blue-600 font-bold': !isSelected(idx + 1),
                            'border border-blue-300': isRealCurrentMonth(idx + 1) && !isSelected(idx + 1)
                        }"
                        class="py-2.5 px-2 rounded-xl text-xs transition-all text-center capitalize cursor-pointer">
                    <span x-text="mName"></span>
                </button>
            </template>
        </div>

        <!-- Pied de page du calendrier -->
        <div class="mt-3.5 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
            <button type="button" 
                    @click="goToday()" 
                    class="text-[11px] font-black uppercase tracking-wider text-blue-600 hover:text-blue-700 transition-colors inline-flex items-center gap-1.5 cursor-pointer">
                <i class="fas fa-calendar-check text-xs"></i>
                <span>Mois en cours</span>
            </button>
            
            <a href="{{ $resetUrl }}" class="text-[11px] font-bold text-slate-400 hover:text-slate-600 uppercase tracking-wider transition-colors">
                Réinitialiser
            </a>
        </div>
    </div>
</div>
