@props(['id', 'name' => null, 'value' => null, 'adInputId' => null, 'bsMonthInputId' => null, 'bsYearInputId' => null, 'placeholder' => 'Select Month', 'redirectPattern' => null])

<div x-data="nepaliMonthPicker({
    adInputId: '{{ $adInputId }}',
    bsMonthInputId: '{{ $bsMonthInputId }}',
    bsYearInputId: '{{ $bsYearInputId }}',
    initialBsValue: '{{ $value }}',
    redirectPattern: '{{ $redirectPattern }}'
})" 
@set-month.window="if($event.detail.targetId === '{{ $id }}') { selectedYear = parseInt($event.detail.year); selectedMonth = parseInt($event.detail.month) - 1; viewYear = selectedYear; updateDisplay(); }"
class="relative w-full" id="{{ $id }}">
    
    <!-- Display/Toggle Button -->
    <button type="button" @click="toggle" 
        class="w-full h-11 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 flex items-center justify-between hover:border-primary-400 dark:hover:border-primary-500 hover:shadow-lg hover:shadow-primary-500/5 transition-all duration-300 text-left group"
        :class="open ? 'ring-2 ring-primary-500/20 border-primary-500 bg-primary-50/30 dark:bg-primary-900/10' : ''">
        <span class="flex items-center gap-3 text-gray-700 dark:text-gray-200">
            <div class="w-8 h-8 rounded-lg bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 group-hover:bg-primary-100 dark:group-hover:bg-primary-900/50 transition-colors">
                <i class="fas fa-calendar-alt text-sm"></i>
            </div>
            <span class="font-bold text-sm tracking-tight" x-text="displayBs || '{{ $placeholder }}'"></span>
        </span>
        <div class="flex items-center gap-2">
            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500" x-show="displayBs"></div>
            <i class="fas fa-chevron-down text-[10px] text-gray-400 group-hover:text-primary-500 transition-all duration-300" :class="open ? 'rotate-180 text-primary-500' : ''"></i>
        </div>
    </button>

    <!-- Hidden Input for BS value (if needed) -->
    @if($name)
    <input type="hidden" name="{{ $name }}" :value="selectedYear && selectedMonth !== null ? `${selectedYear}-${String(selectedMonth + 1).padStart(2, '0')}` : ''">
    @endif

    <!-- Mobile Backdrop -->
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[90] bg-gray-900/50 backdrop-blur-sm sm:hidden"
         @click="open = false"></div>

    <!-- Dropdown Picker -->
    <div x-show="open" @click.away="open = false" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
        class="fixed left-4 right-4 top-[15vh] z-[100] max-h-[85vh] overflow-hidden md:absolute md:left-1/2 md:-translate-x-1/2 md:right-auto md:top-full md:mt-3 w-auto md:w-[320px] bg-white/90 dark:bg-gray-800/95 backdrop-blur-xl rounded-[24px] shadow-[0_20px_50px_rgba(0,0,0,0.2)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.4)] border border-white/20 dark:border-gray-700/50">
        
        <!-- Premium Header -->
        <div class="relative overflow-hidden bg-gradient-to-br from-primary-600 to-indigo-700 dark:from-primary-500 dark:to-indigo-600 px-6 py-5 text-white">
            <!-- Background Decorative Elements -->
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute -left-4 -bottom-4 w-20 h-20 bg-indigo-400/20 rounded-full blur-xl"></div>
            
            <div class="relative flex items-center justify-between">
                <button type="button" @click="changeYear(-1)" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 active:scale-90 transition-all shadow-inner">
                    <i class="fas fa-chevron-left text-xs"></i>
                </button>
                <div class="flex flex-col items-center">
                    <span class="text-[10px] font-bold uppercase tracking-[0.2em] opacity-80 mb-0.5">Select Year</span>
                    <div class="text-xl font-black tracking-tight" x-text="viewYear + ' BS'"></div>
                </div>
                <button type="button" @click="changeYear(1)" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 active:scale-90 transition-all shadow-inner">
                    <i class="fas fa-chevron-right text-xs"></i>
                </button>
            </div>
        </div>

        <!-- Month Grid -->
        <div class="p-4 grid grid-cols-3 gap-2.5">
            <template x-for="(month, index) in nepaliMonths" :key="index">
                <button type="button" @click="selectMonth(index)"
                    class="relative overflow-hidden group p-3 rounded-2xl text-center transition-all duration-300 border border-transparent"
                    :class="(selectedMonth === index && selectedYear === viewYear) 
                        ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' 
                        : 'bg-gray-50/50 dark:bg-gray-700/40 text-gray-700 dark:text-gray-200 hover:bg-white dark:hover:bg-gray-700 hover:border-primary-200 dark:hover:border-primary-800 hover:shadow-md'">
                    
                    <div class="relative z-10">
                        <div class="font-bold text-sm tracking-tight" x-text="month.nepali"></div>
                        <div class="text-[10px] mt-0.5 font-medium opacity-50 group-hover:opacity-80 transition-opacity" x-text="month.english"></div>
                    </div>
                    
                    <!-- Hover Glow Effect -->
                    <div class="absolute inset-0 bg-gradient-to-br from-primary-500/0 to-primary-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                </button>
            </template>
        </div>
        
        <!-- Action Footer -->
        <div class="p-4 pt-0">
             <button type="button" @click="open = false" 
                class="w-full py-3 px-4 bg-gray-100 dark:bg-gray-700/50 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs uppercase tracking-widest rounded-xl transition-all active:scale-[0.98]">
                Close Picker
             </button>
        </div>
    </div>
</div>
