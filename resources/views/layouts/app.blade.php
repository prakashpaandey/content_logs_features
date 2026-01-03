<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script>
        // Immediate Theme Detection to prevent FOUC
        if (localStorage.getItem('dark-mode') === 'true' || 
            (!('dark-mode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ContentLog - Client Dashboard</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                        secondary: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-in': 'slideIn 0.3s ease-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideIn: {
                            '0%': { transform: 'translateX(-100%)' },
                            '100%': { transform: 'translateX(0)' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(100%)' },
                            '100%': { transform: 'translateY(0)' },
                        },
                    }
                }
            }
        }
    </script>
    
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- Alpine.js (required for dropdowns, toggles, and x-data/x-show directives) -->
    <script src="https://unpkg.com/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('nepaliMonthPicker', (config) => ({
                open: false,
                nepaliMonths: [
                    { nepali: 'बैशाख', english: 'Baisakh' },
                    { nepali: 'जेठ', english: 'Jestha' },
                    { nepali: 'असार', english: 'Asar' },
                    { nepali: 'साउन', english: 'Shrawan' },
                    { nepali: 'भदौ', english: 'Bhadra' },
                    { nepali: 'असोज', english: 'Ashwin' },
                    { nepali: 'कार्तिक', english: 'Kartik' },
                    { nepali: 'मंसिर', english: 'Mangsir' },
                    { nepali: 'पौष', english: 'Poush' },
                    { nepali: 'माघ', english: 'Magh' },
                    { nepali: 'फागुन', english: 'Falgun' },
                    { nepali: 'चैत', english: 'Chaitra' }
                ],
                selectedYear: null,
                selectedMonth: null,
                viewYear: 2081,
                displayBs: '',
                adInputId: config.adInputId,
                bsMonthInputId: config.bsMonthInputId,
                bsYearInputId: config.bsYearInputId,

                init() {
                    // If we have an initial value (BS format YYYY-MM)
                    if (config.initialBsValue && config.initialBsValue !== '') {
                        const parts = config.initialBsValue.split('-');
                        if (parts.length >= 2) {
                            this.selectedYear = parseInt(parts[0]);
                            this.selectedMonth = parseInt(parts[1]) - 1;
                            this.viewYear = this.selectedYear;
                            this.updateDisplay();
                            this.syncToBSFields();
                        }
                    } else {
                        // Default to current BS month using stable logic
                        const todayAD = new Date();
                        const adM = todayAD.getMonth() + 1;
                        const adY = todayAD.getFullYear();
                        
                        let bsM, bsY;
                        if (adM >= 4) {
                            bsM = adM - 3;
                            bsY = adY + 57;
                        } else {
                            bsM = adM + 9;
                            bsY = adY + 56;
                        }
                        
                        this.selectedMonth = bsM - 1;
                        this.selectedYear = bsY;
                        this.viewYear = bsY;
                        this.updateDisplay();
                        this.syncToAd();
                        this.syncToBSFields();
                    }
                },

                toggle() {
                    this.open = !this.open;
                },

                changeYear(dir) {
                    this.viewYear += dir;
                },

                selectMonth(index) {
                    this.selectedMonth = index;
                    this.selectedYear = this.viewYear;
                    this.updateDisplay();
                    this.syncToAd();
                    this.syncToBSFields();
                    this.open = false;

                    if (config.redirectPattern && config.redirectPattern !== '') {
                        const url = config.redirectPattern
                            .replace(':month', this.selectedMonth + 1)
                            .replace(':year', this.selectedYear);
                        window.location.href = url;
                    }
                },

                updateDisplay() {
                    if (this.selectedMonth === null) return;
                    const month = this.nepaliMonths[this.selectedMonth];
                    if (month) {
                        this.displayBs = `${month.english} ${this.selectedYear}`;
                    }
                },

                syncToAd() {
                    if (!this.adInputId || this.selectedMonth === null) return;
                    const adInput = document.getElementById(this.adInputId);
                    if (adInput) {
                        // Stable 1-to-1 Mapping:
                        // BS 1-9 (Baisakh-Poush) -> AD Month = BS + 3, AD Year = BS - 57
                        // BS 10-12 (Magh-Chaitra) -> AD Month = BS - 9, AD Year = BS - 56
                        const bsM = this.selectedMonth + 1;
                        const bsY = this.selectedYear;
                        let adM, adY;
                        
                        if (bsM <= 9) {
                            adM = bsM + 3;
                            adY = bsY - 57;
                        } else {
                            adM = bsM - 9;
                            adY = bsY - 56;
                        }
                        
                        const monthStr = String(adM).padStart(2, '0');
                        adInput.value = `${adY}-${monthStr}`;
                    }
                },

                syncToBSFields() {
                    if (this.selectedMonth === null) return;
                    const bsM = this.selectedMonth + 1;
                    const bsY = this.selectedYear;

                    if (this.bsMonthInputId) {
                        const mInput = document.getElementById(this.bsMonthInputId);
                        if (mInput) mInput.value = bsM;
                    }
                    if (this.bsYearInputId) {
                        const yInput = document.getElementById(this.bsYearInputId);
                        if (yInput) yInput.value = bsY;
                    }
                }
            }));
        });
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Nepali Datepicker CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@sajanm/nepali-date-picker@5.0.6/dist/nepali.datepicker.v5.0.6.min.css" rel="stylesheet" type="text/css"/>
    
    <style>
        [x-cloak] { display: none !important; }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        .dark .custom-scrollbar::-webkit-scrollbar-track {
            background: #374151;
        }
        
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #6b7280;
        }
        
        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
        
        /* Smooth transitions */
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        
        /* Chart tooltip styling */
        .apexcharts-tooltip {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        /* Loading skeleton animation */
        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }
            100% {
                background-position: 1000px 0;
            }
        }
        
        .shimmer {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 1000px 100%;
            animation: shimmer 2s infinite;
        }
        
        .dark .shimmer {
            background: linear-gradient(90deg, #374151 25%, #4b5563 50%, #374151 75%);
            background-size: 1000px 100%;
            animation: shimmer 2s infinite;
        }
        
        /* ApexCharts Toolbar Dark Mode Fix */
        .dark .apexcharts-toolbar {
            background: transparent !important;
        }
        
        .dark .apexcharts-menu {
            background: #1f2937 !important;
            border: 1px solid #374151 !important;
        }
        
        .dark .apexcharts-menu-item {
            color: #e5e7eb !important;
        }
        
        .dark .apexcharts-menu-item:hover {
            background: #374151 !important;
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-sans antialiased" x-data="{ sidebarOpen: false }">
    <!-- Top Navigation -->
    @include('components.top-navigation')
    
    <div class="flex pt-16 h-screen">
        <!-- Sidebar -->
        @include('components.sidebar')
        
        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto custom-scrollbar p-4 md:p-6">
            @yield('content')
        </main>
    </div>
    
    <div x-data="{ 
            open: false, 
            actionUrl: '', 
            clientName: '',
            confirmationInput: '',
            init() {
                window.openSecureDeleteModal = (url, name) => {
                    this.actionUrl = url;
                    this.clientName = name;
                    this.confirmationInput = '';
                    this.open = true;
                }
            }
         }"
         x-init="init()"
         x-show="open" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" x-cloak x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="open" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-radiation text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-bold text-red-600 dark:text-red-400" id="modal-title">
                                Delete Client Database?
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    This action is <span class="font-bold text-red-600">IRREVERSIBLE</span>.
                                    All contents, targets, and history for <span class="font-bold text-gray-900 dark:text-white" x-text="clientName"></span> will be permanently destroyed.
                                </p>
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Type <span class="font-mono bg-gray-100 dark:bg-gray-700 px-1 rounded" x-text="clientName"></span> to confirm:
                                    </label>
                                    <input type="text" 
                                           x-model="confirmationInput" 
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                           placeholder="Type client name here...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <form :action="actionUrl" method="POST" class="inline-block w-full sm:w-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                :disabled="confirmationInput !== clientName"
                                :class="{'opacity-50 cursor-not-allowed': confirmationInput !== clientName, 'hover:bg-red-700': confirmationInput === clientName}"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm transition-all">
                            Delete Client Permanently
                        </button>
                    </form>
                    <button @click="open = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-data="{ 
            open: false, 
            actionUrl: '', 
            init() {
                window.openDeleteModal = (url) => {
                    this.actionUrl = url;
                    this.open = true;
                }
            }
         }"
         x-init="init()"
         x-show="open" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="open" x-cloak x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="open" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                Delete Item
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Are you sure you want to delete this? This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <form :action="actionUrl" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Yes, Delete
                        </button>
                    </form>
                    <button @click="open = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div x-data="{ 
            show: false, 
            message: '', 
            type: 'success',
            init() {
                @if(session('success'))
                    this.showToast('{{ session('success') }}', 'success');
                @endif
                @if(session('error'))
                    this.showToast('{{ session('error') }}', 'error');
                @endif
                @if($errors->any())
                    this.showToast('{{ $errors->first() }}', 'error');
                @endif
            },
            showToast(msg, type = 'success') {
                this.message = msg;
                this.type = type;
                this.show = true;
                setTimeout(() => {
                    this.show = false;
                }, 3000);
            }
         }" 
         x-init="init()"
         class="fixed top-20 right-4 z-50">
        <div x-show="show" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform opacity-0 translate-y-2"
             x-transition:enter-end="transform opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="transform opacity-100 translate-y-0"
             x-transition:leave-end="transform opacity-0 translate-y-2"
             :class="{
                'bg-green-500': type === 'success',
                'bg-red-500': type === 'error'
             }"
             class="flex items-center text-white px-6 py-3 rounded-lg shadow-lg">
            <i :class="{
                'fas fa-check-circle': type === 'success',
                'fas fa-exclamation-circle': type === 'error'
            }" class="mr-3 text-xl"></i>
            <span x-text="message" class="font-medium"></span>
            <button @click="show = false" class="ml-4 focus:outline-none hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    
    <script>
        // Global Variables
        let lineChart, barChart, pieChart;
        
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Loaded - Initializing Dashboard');

            // 1. Theme Management
            initializeTheme();

            // 2. Charts
            initializeCharts();
            
            // 3. Progress Bars
            initializeProgressBars();
            
            // 4. Pagination
            updatePagination();
        });

        // Theme Initialization Logic
        function initializeTheme() {
            const themeToggle = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');

            if (!themeToggle || !themeIcon) {
                console.warn('Theme toggle elements not found');
                return;
            }

            // Sync Toggle Icon State based on current class (set by head script)
            const isDark = document.documentElement.classList.contains('dark');
            themeIcon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';

            // Event Listener for Manual Toggles
            themeToggle.addEventListener('click', () => {
                document.documentElement.classList.toggle('dark');
                const nowDark = document.documentElement.classList.contains('dark');
                localStorage.setItem('dark-mode', nowDark);
                themeIcon.className = nowDark ? 'fas fa-sun' : 'fas fa-moon';
                    
                // Re-draw charts to update colors
                setTimeout(initializeCharts, 100);
                setTimeout(initializeProgressBars, 100);
            });
        }

        // Initialize Charts
        function initializeCharts() {
            if (typeof ApexCharts === 'undefined') {
                console.error('ApexCharts library not loaded');
                return;
            }

            const chartData = window.dashboardChartData;
            if (!chartData) {
                console.warn('Chart Data not found');
                return;
            }

            // Destroy existing instances
            if (lineChart) lineChart.destroy();
            if (barChart) barChart.destroy();
            if (pieChart) pieChart.destroy();

            // 1. Line Chart
            const lineContainer = document.querySelector("#line-chart");
            if (lineContainer) {
                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#9CA3AF' : '#6B7280';
                const gridColor = isDark ? '#374151' : '#E5E7EB';
                const titleColor = isDark ? '#F9FAFB' : '#111827';

                const lineOptions = {
                    series: [{
                        name: 'Posts',
                        data: chartData.monthlyProgression.posts
                    }, {
                        name: 'Reels',
                        data: chartData.monthlyProgression.reels
                    }, {
                        name: 'Boosts',
                        data: chartData.monthlyProgression.boosts
                    }],
                    chart: {
                        height: 350,
                        type: 'line',
                        zoom: { enabled: false },
                        toolbar: { show: true },
                        foreColor: textColor
                    },
                    colors: ['#3B82F6', '#10B981', '#F59E0B'],
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 3 },
                    title: {
                        text: 'Monthly Content Progression',
                        align: 'left',
                        style: { fontSize: '16px', fontWeight: '600', color: titleColor }
                    },
                    grid: { borderColor: gridColor, strokeDashArray: 4 },
                    xaxis: {
                        categories: chartData.monthlyProgression.categories,
                        labels: { style: { colors: textColor } }
                    },
                    yaxis: {
                        labels: { style: { colors: textColor } }
                    },
                    legend: {
                        position: 'top',
                        labels: { colors: textColor }
                    },
                    tooltip: { theme: isDark ? 'dark' : 'light' }
                };
                lineChart = new ApexCharts(lineContainer, lineOptions);
                lineChart.render();
            }

            // 2. Bar Chart
            const barContainer = document.querySelector("#bar-chart");
            if (barContainer) {
                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#9CA3AF' : '#6B7280';
                const gridColor = isDark ? '#374151' : '#E5E7EB';
                const titleColor = isDark ? '#F9FAFB' : '#111827';

                const barOptions = {
                    series: [{
                        name: 'Target Posts',
                        data: chartData.targetVsActual.targetPosts
                    }, {
                        name: 'Actual Posts',
                        data: chartData.targetVsActual.actualPosts
                    }, {
                        name: 'Target Reels',
                        data: chartData.targetVsActual.targetReels
                    }, {
                        name: 'Actual Reels',
                        data: chartData.targetVsActual.actualReels
                    }, {
                        name: 'Boost Budget',
                        data: chartData.targetVsActual.targetBoostBudget
                    }, {
                        name: 'Boost Amount',
                        data: chartData.targetVsActual.actualBoostAmount
                    }],
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: { show: true },
                        foreColor: textColor
                    },
                    colors: ['#3B82F6', '#60A5FA', '#10B981', '#34D399', '#F59E0B', '#FBBF24'],
                    plotOptions: {
                        bar: { horizontal: false, columnWidth: '55%', borderRadius: 4 }
                    },
                    dataLabels: { enabled: false },
                    stroke: { show: true, width: 2, colors: ['transparent'] },
                    title: {
                        text: 'Target vs Actual Performance',
                        align: 'left',
                        style: { fontSize: '16px', fontWeight: '600', color: titleColor }
                    },
                    grid: { borderColor: gridColor, strokeDashArray: 4 },
                    xaxis: {
                        categories: chartData.targetVsActual.categories,
                        labels: { style: { colors: textColor } }
                    },
                    yaxis: {
                        labels: { style: { colors: textColor } }
                    },
                    legend: {
                        position: 'top',
                        labels: { colors: textColor }
                    },
                    tooltip: { theme: isDark ? 'dark' : 'light' }
                };
                barChart = new ApexCharts(barContainer, barOptions);
                barChart.render();
            }

            // 3. Pie Chart
            const pieContainer = document.querySelector("#pie-chart");
            if (pieContainer) {
                const isDark = document.documentElement.classList.contains('dark');
                const textColor = isDark ? '#9CA3AF' : '#6B7280';
                const titleColor = isDark ? '#F9FAFB' : '#111827';

                const pieOptions = {
                    series: chartData.contentDistribution.series,
                    chart: {
                        type: 'pie',
                        height: 350,
                        toolbar: { show: true },
                        foreColor: textColor
                    },
                    colors: ['#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#EF4444'],
                    labels: chartData.contentDistribution.labels,
                    title: {
                        text: 'Content Type Distribution',
                        align: 'center',
                        style: { fontSize: '16px', fontWeight: '600', color: titleColor }
                    },
                    legend: {
                        position: 'bottom',
                        labels: { colors: textColor }
                    },
                    tooltip: { theme: isDark ? 'dark' : 'light' }
                };
                pieChart = new ApexCharts(pieContainer, pieOptions);
                pieChart.render();
            }
        }
        
        // Initialize Progress Bars
        function initializeProgressBars() {
            const progressBars = document.querySelectorAll('.progress-bar');
            progressBars.forEach(bar => {
                const value = bar.getAttribute('data-value');
                bar.style.width = `${value}%`;
            });
        }
        
        // Client Selection
        let selectedClientId =1;
        function selectClient(clientId) {
            selectedClientId = clientId;
            document.querySelectorAll('.client-item').forEach(item => {
                item.classList.remove('bg-primary-50', 'dark:bg-primary-900', 'border-primary-500');
                item.classList.add('hover:bg-gray-100', 'dark:hover:bg-gray-800');
            });
            const selectedItem = document.querySelector(`.client-item[data-client-id="${clientId}"]`);
            if (selectedItem) {
                selectedItem.classList.add('bg-primary-50', 'dark:bg-primary-900', 'border-primary-500');
                selectedItem.classList.remove('hover:bg-gray-100', 'dark:hover:bg-gray-800');
            }
        }
        
        // Modal Functions
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        }
        
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }
        
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-overlay')) {
                const modal = e.target.closest('.modal');
                if (modal) closeModal(modal.id);
            }
        });
        
        // Search & Pagination Logic
        function searchClients() {
            const searchTerm = document.getElementById('client-search').value.toLowerCase();
            document.querySelectorAll('.client-item').forEach(item => {
                const text = item.textContent.toLowerCase();
                item.classList.toggle('hidden', !text.includes(searchTerm));
            });
        }
        
        let currentPage = 1;
        function goToPage(page) {
            currentPage = page;
            updatePagination();
        }
        
        function updatePagination() {
            document.querySelectorAll('.page-button').forEach(button => {
                const isCurrent = parseInt(button.textContent) === currentPage;
                button.classList.toggle('bg-primary-600', isCurrent);
                button.classList.toggle('text-white', isCurrent);
                button.classList.toggle('bg-white', !isCurrent);
                button.classList.toggle('text-gray-700', !isCurrent);
            });
        }
    </script>

    <!-- Nepali Datepicker JS -->
    <script src="https://cdn.jsdelivr.net/npm/@sajanm/nepali-date-picker@5.0.6/dist/nepali.datepicker.v5.0.6.min.js" type="text/javascript"></script>
    <script>
        window.initializeNepaliDatePicker = function() {
            const inputs = document.querySelectorAll(".nepali-datepicker");
            inputs.forEach(input => {
                if (input.dataset.initialized) return;
                
                const adInputId = input.getAttribute('data-ad-id');
                const adInput = adInputId ? document.getElementById(adInputId) : null;

                const syncValue = (val) => {
                    if (!adInput || !val) return;
                    try {
                        let bsDate;
                        if (typeof val === 'object' && val.year) {
                            bsDate = val;
                        } else {
                            const cleanVal = String(val).trim().replace(/\//g, '-');
                            bsDate = NepaliFunctions.ConvertToDateObject(cleanVal, "YYYY-MM-DD");
                        }

                        if (bsDate && bsDate.year) {
                            const adDate = NepaliFunctions.BS2AD(bsDate);
                            const adDateStr = NepaliFunctions.ConvertDateFormat(adDate, "YYYY-MM-DD");
                            adInput.value = adDateStr;
                        }
                    } catch (err) {
                        console.error("Date sync error:", err);
                    }
                };

                if (typeof input.nepaliDatePicker === 'function') {
                    input.nepaliDatePicker({
                        ndpYear: true,
                        ndpMonth: true,
                        ndpYearCount: 20,
                        dateFormat: "YYYY-MM-DD",
                        language: "english",
                        onChange: function(selected) {
                            // The library may pass a string or an object
                            syncValue(selected || input.value);
                        }
                    });
                    
                    // Sync initial value if present
                    if (input.value) syncValue(input.value);
                    
                    input.addEventListener('change', () => syncValue(input.value));
                    input.addEventListener('input', () => syncValue(input.value));
                    input.dataset.initialized = "true";
                }
            });
        };

        // Safety: Ensure all forms sync before submit
        document.addEventListener('submit', (e) => {
            const form = e.target;
            const nepaliInputs = form.querySelectorAll('.nepali-datepicker');
            nepaliInputs.forEach(input => {
                const adInputId = input.getAttribute('data-ad-id');
                const adInput = adInputId ? document.getElementById(adInputId) : null;
                if (adInput && input.value) {
                    try {
                        const cleanVal = String(input.value).trim().replace(/\//g, '-');
                        const bsDate = NepaliFunctions.ConvertToDateObject(cleanVal, "YYYY-MM-DD");
                        if (bsDate && bsDate.year) {
                            const adDate = NepaliFunctions.BS2AD(bsDate);
                            const adDateStr = NepaliFunctions.ConvertDateFormat(adDate, "YYYY-MM-DD");
                            adInput.value = adDateStr;
                        }
                    } catch (err) { }
                }
            });
        });

        window.initializeNepaliMonthPicker = function() {
            const inputs = document.querySelectorAll(".nepali-monthpicker");
            inputs.forEach(input => {
                if (input.dataset.initialized) return;
                
                if (typeof input.nepaliDatePicker === 'function') {
                    input.nepaliDatePicker({
                        ndpYear: true,
                        ndpMonth: true,
                        ndpYearCount: 10,
                        language: "english",
                        onChange: function() {
                            const adInputId = input.getAttribute('data-ad-id');
                            if (adInputId) {
                                const val = input.value;
                                const bsDate = NepaliFunctions.ConvertToDateObject(val, "YYYY-MM-DD");
                                const adDate = NepaliFunctions.BS2AD(bsDate);
                                // Manually build YYYY-MM to ensure format
                                const year = adDate.year;
                                const month = String(adDate.month).padStart(2, '0');
                                const adMonthStr = `${year}-${month}`;
                                document.getElementById(adInputId).value = adMonthStr;
                                console.log(`Syncing Month ${val} (BS) to ${adMonthStr} (AD)`);
                            }
                        }
                    });
                    input.dataset.initialized = "true";
                }
            });
        };

        document.addEventListener('DOMContentLoaded', () => {
            // Early attempt
            initializeNepaliDatePicker();
            initializeNepaliMonthPicker();
            
            // Delayed attempt for slow loading libraries
            setTimeout(() => {
                initializeNepaliDatePicker();
                initializeNepaliMonthPicker();
            }, 500);
            setTimeout(() => {
                initializeNepaliDatePicker();
                initializeNepaliMonthPicker();
            }, 2000);
        });

        // Global watcher for dynamic inputs or modals
        document.body.addEventListener('focusin', (e) => {
            if (e.target.classList.contains('nepali-datepicker')) {
                initializeNepaliDatePicker();
                // Ensure date grid is visible
                const calendar = document.getElementById('ndp-nepali-datepicker');
                if (calendar) calendar.style.display = 'block';
            }
        });
    </script>
</body>
</html>