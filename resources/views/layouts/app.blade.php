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
        // Theme Management
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        
        // Chart instances
        let lineChart, barChart, pieChart;
        
        if (localStorage.getItem('dark-mode') === 'true' || 
            (!('dark-mode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            themeIcon.className = 'fas fa-sun';
        } else {
            document.documentElement.classList.remove('dark');
            themeIcon.className = 'fas fa-moon';
        }
        
        themeToggle.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem('dark-mode', document.documentElement.classList.contains('dark'));
            themeIcon.className = document.documentElement.classList.contains('dark') 
                ? 'fas fa-sun' 
                : 'fas fa-moon';
                
            // Re-initialize charts with new theme colors
            initializeCharts();
        });
        
        // Client Selection
        let selectedClientId =1;
        
        function selectClient(clientId) {
            selectedClientId = clientId;
            
            // Update active state in sidebar
            document.querySelectorAll('.client-item').forEach(item => {
                item.classList.remove('bg-primary-50', 'dark:bg-primary-900', 'border-primary-500');
                item.classList.add('hover:bg-gray-100', 'dark:hover:bg-gray-800');
            });
            
            const selectedItem = document.querySelector(`.client-item[data-client-id="${clientId}"]`);
            if (selectedItem) {
                selectedItem.classList.add('bg-primary-50', 'dark:bg-primary-900', 'border-primary-500');
                selectedItem.classList.remove('hover:bg-gray-100', 'dark:hover:bg-gray-800');
            }
            
            // In a real app, this would trigger a page reload or fetch new data
            console.log(`Selected client: ${clientId}`);
        }
        
        // Modal System
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
        
        // Close modal when clicking outside
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-overlay')) {
                const modal = e.target.closest('.modal');
                if (modal) {
                    closeModal(modal.id);
                }
            }
        });
        
        // Initialize charts when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
            initializeProgressBars();
        });
        
        // Sample data for charts
        const chartData = {
            monthlyProgression: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                posts: [120, 135, 150, 145, 160, 175, 180, 190, 200, 195, 210, 220],
                reels: [45, 50, 55, 60, 65, 70, 75, 80, 85, 90, 95, 100]
            },
            targetVsActual: {
                categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                targetPosts: [50, 50, 50, 50],
                actualPosts: [45, 52, 48, 55],
                targetReels: [20, 20, 20, 20],
                actualReels: [18, 22, 19, 21]
            },
            contentDistribution: {
                labels: ['Instagram Posts', 'Instagram Reels', 'Facebook Posts', 'LinkedIn Posts', 'Twitter Posts'],
                series: [35, 25, 20, 15, 5]
            }
        };
        
        // Initialize Charts
        function initializeCharts() {
            // Destroy existing charts if they exist
            if (lineChart) lineChart.destroy();
            if (barChart) barChart.destroy();
            if (pieChart) pieChart.destroy();
            // Line Chart - Monthly Progression
            const lineChartOptions = {
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
                    zoom: {
                        enabled: false
                    },
                    toolbar: {
                        show: true
                    },
                    foreColor: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                },
                colors: ['#3B82F6', '#10B981'],
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                title: {
                    text: 'Monthly Content Progression',
                    align: 'left',
                    style: {
                        fontSize: '16px',
                        fontWeight: '600',
                        color: document.documentElement.classList.contains('dark') ? '#F9FAFB' : '#111827'
                    }
                },
                grid: {
                    borderColor: document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB',
                    strokeDashArray: 4
                },
                markers: {
                    size: 5,
                    hover: {
                        size: 7
                    }
                },
                xaxis: {
                    categories: chartData.monthlyProgression.categories,
                    labels: {
                        style: {
                            colors: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    labels: {
                        colors: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                    }
                },
                tooltip: {
                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                }
            };
            
            lineChart = new ApexCharts(document.querySelector("#line-chart"), lineChartOptions);
            lineChart.render();
            
            // Bar Chart - Target vs Actual
            const barChartOptions = {
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
                    toolbar: {
                        show: true
                    },
                    foreColor: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                },
                colors: ['#3B82F6', '#60A5FA', '#10B981', '#34D399'],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 4
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                title: {
                    text: 'Target vs Actual Performance',
                    align: 'left',
                    style: {
                        fontSize: '16px',
                        fontWeight: '600',
                        color: document.documentElement.classList.contains('dark') ? '#F9FAFB' : '#111827'
                    }
                },
                grid: {
                    borderColor: document.documentElement.classList.contains('dark') ? '#374151' : '#E5E7EB',
                    strokeDashArray: 4
                },
                xaxis: {
                    categories: chartData.targetVsActual.categories,
                    labels: {
                        style: {
                            colors: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                        }
                    }
                },
                fill: {
                    opacity: 1
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    labels: {
                        colors: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                    }
                },
                tooltip: {
                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                    y: {
                        formatter: function (val) {
                            return val + " units"
                        }
                    }
                }
            };
            
            barChart = new ApexCharts(document.querySelector("#bar-chart"), barChartOptions);
            barChart.render();
            
            // Pie Chart - Content Distribution
            const pieChartOptions = {
                series: chartData.contentDistribution.series,
                chart: {
                    type: 'pie',
                    height: 350,
                    toolbar: {
                        show: true
                    },
                    foreColor: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                },
                colors: ['#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#EF4444'],
                labels: chartData.contentDistribution.labels,
                title: {
                    text: 'Content Type Distribution',
                    align: 'center',
                    style: {
                        fontSize: '16px',
                        fontWeight: '600',
                        color: document.documentElement.classList.contains('dark') ? '#F9FAFB' : '#111827'
                    }
                },
                legend: {
                    position: 'bottom',
                    labels: {
                        colors: document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280'
                    }
                },
                tooltip: {
                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                    y: {
                        formatter: function (val) {
                            return val + "%"
                        }
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 300
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };
            
            pieChart = new ApexCharts(document.querySelector("#pie-chart"), pieChartOptions);
            pieChart.render();
        }
        
        // Initialize Progress Bars
        function initializeProgressBars() {
            const progressBars = document.querySelectorAll('.progress-bar');
            progressBars.forEach(bar => {
                const value = bar.getAttribute('data-value');
                bar.style.width = `${value}%`;
            });
        }
        
        // Update progress bars on theme change
        themeToggle.addEventListener('click', () => {
            setTimeout(initializeProgressBars, 100);
        });
        
        // Form validation for modal
        function validateTargetForm() {
            const month = document.getElementById('target-month').value;
            const posts = document.getElementById('target-posts').value;
            const reels = document.getElementById('target-reels').value;
            
            if (!month || !posts || !reels) {
                alert('Please fill in all required fields');
                return false;
            }
            
            if (posts < 0 || reels < 0) {
                alert('Target values cannot be negative');
                return false;
            }
            
            return true;
        }
        
        // Add new monthly target (UI only)
        function addMonthlyTarget(event) {
            event.preventDefault();
            
            if (!validateTargetForm()) return;
            
            const month = document.getElementById('target-month').value;
            const posts = document.getElementById('target-posts').value;
            const reels = document.getElementById('target-reels').value;
            const notes = document.getElementById('target-notes').value;
            
            // Create new row for the table
            const tableBody = document.getElementById('targets-table-body');
            const newRow = document.createElement('tr');
            newRow.className = 'hover:bg-gray-50 dark:hover:bg-gray-800';
            newRow.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                    ${new Date(month).toLocaleDateString('en-US', { month: 'long', year: 'numeric' })}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                    ${posts}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                    ${reels}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                    ${new Date().toLocaleDateString()}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        Active
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right space-x-2">
                    <button onclick="viewTargetDetails('${month}')" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button onclick="editTarget('${month}')" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="viewTargetHistory('${month}')" class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300">
                        <i class="fas fa-history"></i>
                    </button>
                </td>
            `;
            
            tableBody.prepend(newRow);
            
            // Reset form and close modal
            document.getElementById('create-target-form').reset();
            closeModal('create-target-modal');
            
            // Show success message
            showNotification('Monthly target created successfully!', 'success');
        }
        
        // Show notification
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-20 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transform transition-transform duration-300 translate-x-full ${
                type === 'success' ? 'bg-green-100 text-green-900 dark:bg-green-900 dark:text-green-100' :
                type === 'error' ? 'bg-red-100 text-red-900 dark:bg-red-900 dark:text-red-100' :
                'bg-blue-100 text-blue-900 dark:bg-blue-900 dark:text-blue-100'
            }`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${
                        type === 'success' ? 'fa-check-circle' :
                        type === 'error' ? 'fa-exclamation-circle' :
                        'fa-info-circle'
                    } mr-2"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-current hover:opacity-75">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
                notification.classList.add('translate-x-0');
            }, 10);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.classList.add('translate-x-full');
                    setTimeout(() => {
                        if (notification.parentElement) {
                            notification.remove();
                        }
                    }, 300);
                }
            }, 5000);
        }
        
        // Mock functions for actions
        function viewTargetDetails(month) {
            showNotification(`Viewing details for ${month}`, 'info');
        }
        
        function editTarget(month) {
            showNotification(`Editing target for ${month}`, 'info');
        }
        
        function viewTargetHistory(month) {
            showNotification(`Viewing history for ${month}`, 'info');
        }
        
        // Search functionality for client list
        function searchClients() {
            const searchTerm = document.getElementById('client-search').value.toLowerCase();
            const clientItems = document.querySelectorAll('.client-item');
            
            clientItems.forEach(item => {
                const clientName = item.querySelector('.client-name').textContent.toLowerCase();
                const businessName = item.querySelector('.client-business').textContent.toLowerCase();
                
                if (clientName.includes(searchTerm) || businessName.includes(searchTerm)) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        }
        
        // Pagination for content table
        let currentPage = 1;
        const itemsPerPage = 10;
        
        function goToPage(page) {
            currentPage = page;
            updatePagination();
            showNotification(`Navigated to page ${page}`, 'info');
        }
        
        function updatePagination() {
            const pageButtons = document.querySelectorAll('.page-button');
            pageButtons.forEach(button => {
                button.classList.remove('bg-primary-600', 'text-white');
                button.classList.add('bg-white', 'text-gray-700', 'dark:bg-gray-800', 'dark:text-gray-300');
                
                if (parseInt(button.textContent) === currentPage) {
                    button.classList.add('bg-primary-600', 'text-white');
                    button.classList.remove('bg-white', 'text-gray-700', 'dark:bg-gray-800', 'dark:text-gray-300');
                }
            });
        }
        
        // Initialize pagination
        document.addEventListener('DOMContentLoaded', updatePagination);
    </script>
</body>
</html>