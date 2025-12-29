<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
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
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
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
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-sans antialiased">
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
    
    <!-- Modal Container -->
    <div id="modal-container"></div>
    
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

            // Set Initial State
            if (localStorage.getItem('dark-mode') === 'true' || 
                (!('dark-mode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
                themeIcon.className = 'fas fa-sun';
            } else {
                document.documentElement.classList.remove('dark');
                themeIcon.className = 'fas fa-moon';
            }

            // Event Listener
            themeToggle.addEventListener('click', () => {
                document.documentElement.classList.toggle('dark');
                const isDark = document.documentElement.classList.contains('dark');
                localStorage.setItem('dark-mode', isDark);
                themeIcon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
                    
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
                    }],
                    chart: {
                        height: 350,
                        type: 'line',
                        zoom: { enabled: false },
                        toolbar: { show: true },
                        foreColor: textColor
                    },
                    colors: ['#3B82F6', '#10B981'],
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
                    }],
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: { show: true },
                        foreColor: textColor
                    },
                    colors: ['#3B82F6', '#60A5FA', '#10B981', '#34D399'],
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
</body>
</html>