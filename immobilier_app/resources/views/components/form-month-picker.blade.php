@props([
    'id' => 'mois_select',
    'name' => null,
    'value' => '',
    'placeholder' => 'Sélectionnez un mois',
    'minYear' => 2020,
    'maxYear' => 2050,
    'onChange' => null,
    'required' => false,
])

@php
    $name = $name ?? $id;
    $initialValue = old($name, $value);
    $initialYear = $initialValue ? (int)substr($initialValue, 0, 4) : (int)date('Y');
    $initialMonth = $initialValue ? (int)substr($initialValue, 5, 2) : 0;
@endphp

<div class="relative w-full"
     x-data="{
        open: false,
        value: '{{ $initialValue }}',
        selectedYear: {{ $initialYear }},
        selectedMonth: {{ $initialMonth }},
        navYear: {{ $initialYear }},
        realYear: new Date().getFullYear(),
        realMonth: new Date().getMonth() + 1,
        months: [
            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
        ],
        get label() {
            if (!this.selectedMonth || !this.selectedYear) {
                return '{{ $placeholder }}';
            }
            return this.months[this.selectedMonth - 1] + ' ' + this.selectedYear;
        },
        isSelected(m) {
            return this.selectedYear === this.navYear && this.selectedMonth === m;
        },
        isRealCurrent(m) {
            return this.realYear === this.navYear && this.realMonth === m;
        },
        prevYear() {
            if (this.navYear > {{ $minYear }}) this.navYear--;
        },
        nextYear() {
            if (this.navYear < {{ $maxYear }}) this.navYear++;
        },
        selectMonth(m) {
            this.selectedYear = this.navYear;
            this.selectedMonth = m;
            const mm = String(m).padStart(2, '0');
            this.setValue(`${this.selectedYear}-${mm}`);
            this.open = false;
        },
        prevMonth() {
            if (!this.selectedMonth) {
                this.selectedYear = this.realYear;
                this.selectedMonth = this.realMonth;
            }
            let y = this.selectedYear;
            let m = this.selectedMonth - 1;
            if (m < 1) { m = 12; y--; }
            this.selectedYear = y;
            this.selectedMonth = m;
            this.navYear = y;
            const mm = String(m).padStart(2, '0');
            this.setValue(`${y}-${mm}`);
        },
        nextMonth() {
            if (!this.selectedMonth) {
                this.selectedYear = this.realYear;
                this.selectedMonth = this.realMonth;
            }
            let y = this.selectedYear;
            let m = this.selectedMonth + 1;
            if (m > 12) { m = 1; y++; }
            this.selectedYear = y;
            this.selectedMonth = m;
            this.navYear = y;
            const mm = String(m).padStart(2, '0');
            this.setValue(`${y}-${mm}`);
        },
        goToday() {
            this.navYear = this.realYear;
            this.selectMonth(this.realMonth);
        },
        setValue(val) {
            this.value = val;
            const hiddenInput = document.getElementById('{{ $id }}');
            if (hiddenInput) {
                hiddenInput.value = val;
                hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
            @if($onChange)
            if (typeof window['{{ $onChange }}'] === 'function') {
                window['{{ $onChange }}']();
            }
            @endif
        }
     }"
     x-init="
        if (value) {
            selectedYear = parseInt(value.substring(0, 4), 10);
            selectedMonth = parseInt(value.substring(5, 7), 10);
            navYear = selectedYear;
        }
        $watch('value', val => {
            if (val) {
                selectedYear = parseInt(val.substring(0, 4), 10);
                selectedMonth = parseInt(val.substring(5, 7), 10);
                navYear = selectedYear;
            } else {
                selectedYear = 0;
                selectedMonth = 0;
            }
        });
     "
     @set-month-{{ $id }}.window="
        if ($event.detail) {
            const val = $event.detail;
            value = val;
            selectedYear = parseInt(val.substring(0, 4), 10);
            selectedMonth = parseInt(val.substring(5, 7), 10);
            navYear = selectedYear;
            const hiddenInput = document.getElementById('{{ $id }}');
            if (hiddenInput) hiddenInput.value = val;
            @if($onChange)
            if (typeof window['{{ $onChange }}'] === 'function') {
                window['{{ $onChange }}']();
            }
            @endif
        }
     ">

    <!-- Input caché conservant la valeur pour le formulaire et les scripts -->
    <input type="hidden" id="{{ $id }}" name="{{ $name }}" :value="value" {{ $required ? 'required' : '' }}>

    <!-- Champ d'affichage élégant façon sélecteur interactif -->
    <div class="flex items-center gap-1.5 w-full bg-slate-50 border border-slate-200 rounded-xl p-1 shadow-sm hover:border-blue-400 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-100 transition-all">
        <!-- Bouton Mois Précédent (1 clic) -->
        <button type="button" 
                @click="prevMonth()" 
                title="Mois précédent" 
                class="w-9 h-9 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 flex items-center justify-center transition-all cursor-pointer shrink-0">
            <i class="fas fa-chevron-left text-xs"></i>
        </button>

        <!-- Bouton Principal Popover -->
        <button type="button" 
                @click="open = !open; if(open && selectedYear) navYear = selectedYear;" 
                class="flex-1 px-3 py-2 rounded-lg hover:bg-white flex items-center justify-between text-xs font-black uppercase tracking-wider text-slate-800 transition-all cursor-pointer">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-6 h-6 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center text-xs shrink-0">
                    <i class="far fa-calendar-alt"></i>
                </div>
                <span class="truncate capitalize" 
                      :class="{ 'text-slate-400 font-bold': !selectedMonth }" 
                      x-text="selectedMonth && selectedYear ? months[selectedMonth - 1] + ' ' + selectedYear : '{{ $placeholder }}'">
                </span>
            </div>
            <i class="fas fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
        </button>

        <!-- Bouton Mois Suivant (1 clic) -->
        <button type="button" 
                @click="nextMonth()" 
                title="Mois suivant" 
                class="w-9 h-9 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 flex items-center justify-center transition-all cursor-pointer shrink-0">
            <i class="fas fa-chevron-right text-xs"></i>
        </button>
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
         class="absolute left-0 top-full mt-2 w-72 sm:w-80 bg-white rounded-2xl shadow-2xl border border-slate-100 p-4 z-50">
        
        <!-- En-tête : Navigation Année -->
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

        <!-- Grille 3x4 des 12 Mois -->
        <div class="grid grid-cols-3 gap-2">
            <template x-for="(monthName, index) in months" :key="index">
                <button type="button"
                        @click="selectMonth(index + 1)"
                        class="py-2.5 px-2 rounded-xl text-xs font-bold transition-all flex flex-col items-center justify-center relative cursor-pointer"
                        :class="isSelected(index + 1)
                            ? 'bg-blue-600 text-white font-black shadow-md shadow-blue-500/30'
                            : (isRealCurrent(index + 1)
                                ? 'bg-blue-50/70 text-blue-700 font-black hover:bg-blue-100/80 border border-blue-200' 
                                : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900 font-medium')">
                    <span x-text="monthName.slice(0, 4) + '.'"></span>
                    <template x-if="isRealCurrent(index + 1)">
                        <span class="w-1.5 h-1.5 rounded-full absolute bottom-1"
                              :class="isSelected(index + 1) ? 'bg-white' : 'bg-blue-600'"></span>
                    </template>
                </button>
            </template>
        </div>

        <!-- Raccourcis Bas de Popover -->
        <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
            <button type="button" 
                    @click="goToday()"
                    class="font-black text-blue-600 hover:text-blue-800 transition-colors cursor-pointer flex items-center gap-1.5">
                <i class="fas fa-bullseye text-[10px]"></i> Mois actuel
            </button>
            <span class="text-[10px] text-slate-400 font-bold" x-text="navYear"></span>
        </div>
    </div>
</div>
