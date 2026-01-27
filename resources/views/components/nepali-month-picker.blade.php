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
        class="w-full bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-lg px-4 py-2.5 flex items-center justify-between hover:border-primary-400 focus:border-primary-500 transition-colors text-left shadow-sm">
        <span class="flex items-center gap-2 text-gray-700 dark:text-gray-200">
            <i class="far fa-calendar-alt text-primary-500"></i>
            <span x-text="displayBs || '{{ $placeholder }}'"></span>
        </span>
        <i class="fas fa-chevron-right text-gray-400 transition-transform duration-200" :class="open ? 'rotate-90' : ''"></i>
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
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="fixed left-4 right-4 top-[15vh] z-[100] max-h-[80vh] overflow-y-auto sm:absolute sm:inset-auto sm:top-full sm:mt-2 w-auto sm:w-full bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 sm:min-w-[280px]">
        
        <!-- Year Selector -->
        <div class="bg-primary-600 dark:bg-primary-700 text-white p-3 flex items-center justify-between">
            <button type="button" @click="changeYear(-1)" class="p-1.5 rounded-full hover:bg-white/20 transition-colors">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="text-lg font-bold tracking-wide" x-text="viewYear + ' BS'"></div>
            <button type="button" @click="changeYear(1)" class="p-1.5 rounded-full hover:bg-white/20 transition-colors">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <!-- Month Grid -->
        <div class="p-3 grid grid-cols-3 gap-2 bg-white dark:bg-gray-800">
            <template x-for="(month, index) in nepaliMonths" :key="index">
                <button type="button" @click="selectMonth(index)"
                    class="p-2.5 rounded-lg text-center transition-all group border border-transparent"
                    :class="(selectedMonth === index && selectedYear === viewYear) 
                        ? 'bg-primary-600 text-white shadow-md' 
                        : 'bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-200 hover:border-primary-300 dark:hover:border-primary-700 hover:bg-primary-50 dark:hover:bg-primary-900/40 hover:text-primary-600 dark:hover:text-primary-400'">
                    <div class="font-bold text-xs" x-text="month.nepali"></div>
                    <div class="text-[10px] mt-0.5 opacity-60 group-hover:opacity-100" x-text="month.english"></div>
                </button>
            </template>
        </div>
        
        <div class="p-2 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex justify-center">
             <button type="button" @click="open = false" class="text-xs text-gray-500 hover:text-primary-600 dark:hover:text-primary-400">
                Close
             </button>
        </div>
    </div>
</div>
