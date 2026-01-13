@extends('layouts.app')

@section('content')
@section('content')
<div class="animate-fade-in" x-data="{ searchQuery: '' }">
    <!-- Agency Summary Header -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">All Clients Overview</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    @php $currentBs = ['month' => $bsMonth, 'year' => $bsYear]; @endphp
                    Monitoring progress for all active clients in {{ $nepaliTranslate($currentBs['month'], 'month') }} {{ $currentBs['year'] }}
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 w-full md:w-auto">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <input 
                        type="text" 
                        x-model="searchQuery"
                        placeholder="Search clients..."
                        class="pl-10 pr-4 py-2 w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                    >
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                
                <div class="flex items-center space-x-2 min-w-0 sm:min-w-[220px]">
                    <x-nepali-month-picker 
                        id="overview-month-nav" 
                        value="{{ $bsYear . '-' . str_pad($bsMonth, 2, '0', STR_PAD_LEFT) }}"
                        placeholder="Select Month"
                        redirectPattern="{{ route('clients.overview') }}?month=:month&year=:year" 
                    />
                </div>
            </div>
        </div>

        <!-- Status Filter Tabs -->
        @php
            $statusFilter = request()->query('status', 'all');
        @endphp
        <div class="mt-6 border-b border-gray-200 dark:border-gray-700 overflow-x-auto hide-scrollbar">
            <nav class="flex space-x-2 min-w-max pb-px" aria-label="Tabs">
                <a href="{{ route('clients.overview', ['month' => $bsMonth, 'year' => $bsYear, 'status' => 'all']) }}" 
                   class="px-4 py-3 text-sm font-medium rounded-t-lg transition-colors {{ $statusFilter === 'all' 
                       ? 'bg-primary-50 text-primary-700 border-b-2 border-primary-600 dark:bg-primary-900/30 dark:text-primary-400' 
                       : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700/50' }}">
                    <i class="fas fa-th mr-2"></i>All
                </a>
                <a href="{{ route('clients.overview', ['month' => $bsMonth, 'year' => $bsYear, 'status' => 'no-target']) }}" 
                   class="px-4 py-3 text-sm font-medium rounded-t-lg transition-colors {{ $statusFilter === 'no-target' 
                       ? 'bg-gray-50 text-gray-700 border-b-2 border-gray-600 dark:bg-gray-700/50 dark:text-gray-300' 
                       : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700/50' }}">
                    <i class="fas fa-minus-circle mr-2"></i>No Target
                </a>
                <a href="{{ route('clients.overview', ['month' => $bsMonth, 'year' => $bsYear, 'status' => 'active']) }}" 
                   class="px-4 py-3 text-sm font-medium rounded-t-lg transition-colors {{ $statusFilter === 'active' 
                       ? 'bg-yellow-50 text-yellow-700 border-b-2 border-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400' 
                       : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700/50' }}">
                    <i class="fas fa-play-circle mr-2"></i>Active
                </a>
                <a href="{{ route('clients.overview', ['month' => $bsMonth, 'year' => $bsYear, 'status' => 'completed']) }}" 
                   class="px-4 py-3 text-sm font-medium rounded-t-lg transition-colors {{ $statusFilter === 'completed' 
                       ? 'bg-green-50 text-green-700 border-b-2 border-green-600 dark:bg-green-900/30 dark:text-green-400' 
                       : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:bg-gray-700/50' }}">
                    <i class="fas fa-check-circle mr-2"></i>Completed
                </a>
            </nav>
        </div>

        <!-- Portfolio Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-8">
            <div class="bg-purple-50 dark:bg-purple-900/10 p-4 rounded-xl border border-purple-100 dark:border-purple-900/20">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider">Total Posts</span>
                    <i class="fas fa-file-alt text-purple-500"></i>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $totalAgencyMetrics['posts'] }}</span>
                    <span class="text-sm text-gray-500">/ {{ $totalAgencyMetrics['target_posts'] }}</span>
                </div>
            </div>
            
            <div class="bg-green-50 dark:bg-green-900/10 p-4 rounded-xl border border-green-100 dark:border-green-900/20">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-green-600 dark:text-green-400 uppercase tracking-wider">Total Reels</span>
                    <i class="fas fa-video text-green-500"></i>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $totalAgencyMetrics['reels'] }}</span>
                    <span class="text-sm text-gray-500">/ {{ $totalAgencyMetrics['target_reels'] }}</span>
                </div>
            </div>

            <div class="bg-blue-50 dark:bg-blue-900/10 p-4 rounded-xl border border-blue-100 dark:border-blue-900/20">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Total Boosts</span>
                    <i class="fas fa-rocket text-blue-500"></i>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $totalAgencyMetrics['boosts'] }}</span>
                    <span class="text-sm text-gray-500">of Budget $ {{ number_format($totalAgencyMetrics['target_boost_budget']) }}</span>
                </div>
            </div>

            <div class="bg-yellow-50 dark:bg-yellow-900/10 p-4 rounded-xl border border-yellow-100 dark:border-yellow-900/20">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-yellow-600 dark:text-yellow-400 uppercase tracking-wider">Boost Amount</span>
                    <i class="fas fa-hand-holding-usd text-yellow-500"></i>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl font-black text-gray-900 dark:text-white">$ {{ number_format($totalAgencyMetrics['boost_amount'], 0) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Client Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($clientsData as $data)
            @php
                $postProgress = $data['posts_completion'];
                $reelProgress = $data['reels_completion'];
                $boostProgress = $data['boost_completion'];
            @endphp
            
            <!-- Hidden Data Block for Alpine -->
            <script type="application/json" id="client-data-{{ $data['client']->id }}">
                @json([
                    'contents' => $data['contents'],
                    'boosts' => $data['boosts'],
                    'clientName' => $data['client']->name
                ])
            </script>

            <!-- Client Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden hover:shadow-lg transition-shadow duration-300"
                 x-show="searchQuery === '' || '{{ strtolower($data['client']->name) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($data['client']->business_name ?? '') }}'.includes(searchQuery.toLowerCase())"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95">
                <!-- Card Header -->
                <div class="bg-gradient-to-r from-primary-50 to-purple-50 dark:from-primary-900/20 dark:to-purple-900/20 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-primary-500 to-purple-500 flex items-center justify-center text-white font-bold text-lg shadow-lg shrink-0">
                                {{ $data['client']->initials ?? strtoupper(substr($data['client']->name, 0, 2)) }}
                            </div>
                            <div class="ml-4 min-w-0">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white truncate">{{ $data['client']->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate" title="{{ $data['client']->business_name ?? 'Client' }}">{{ $data['client']->business_name ?? 'Client' }}</p>
                            </div>
                        </div>
                        @if($data['target'])
                            <span class="px-3 py-1 text-[10px] font-bold rounded-full uppercase tracking-widest self-start sm:self-center
                                {{ $data['target']->status === 'completed' 
                                    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' 
                                    : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                                {{ ucfirst($data['target']->status) }}
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 uppercase tracking-widest">
                                No Target
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Progress Section -->
                <div class="p-6">
                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Monthly Progress</h4>
                    
                    <!-- Overall Completion -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Overall Completion</span>
                            <span class="text-lg font-bold text-primary-600">{{ $data['completion'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                            <div class="bg-gradient-to-r from-primary-500 to-purple-500 h-3 rounded-full transition-all duration-500" 
                                 style="width: {{ $data['completion'] }}%"></div>
                        </div>
                    </div>

                    <!-- Posts Progress -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-primary-500 mr-2"></div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Posts</span>
                            </div>
                            <div class="flex flex-col items-end text-right">
                                <span class="text-xs font-bold text-gray-900 dark:text-white cursor-pointer hover:text-primary-600 transition-colors" @click="$dispatch('open-contributors-modal', { clientId: {{ $data['client']->id }}, type: 'Post' })">
                                    {{ $postProgress }}%
                                </span>
                                <span class="text-[10px] text-gray-500">{{ $data['actual_posts'] }}/{{ $data['target_posts'] }} Posts</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                            <div class="bg-primary-500 h-2.5 rounded-full transition-all duration-500" 
                                 style="width: {{ $postProgress }}%"></div>
                        </div>
                    </div>

                    <!-- Reels Progress -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Reels</span>
                            </div>
                            <div class="flex flex-col items-end text-right">
                                <span class="text-xs font-bold text-gray-900 dark:text-white cursor-pointer hover:text-green-600 transition-colors" @click="$dispatch('open-contributors-modal', { clientId: {{ $data['client']->id }}, type: 'Reel' })">
                                    {{ $reelProgress }}%
                                </span>
                                <span class="text-[10px] text-gray-500">{{ $data['actual_reels'] }}/{{ $data['target_reels'] }} Reels</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                            <div class="bg-green-500 h-2.5 rounded-full transition-all duration-500" 
                                 style="width: {{ $reelProgress }}%"></div>
                        </div>
                    </div>

                    <!-- Boosts Progress -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-blue-500 mr-2"></div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Boosts</span>
                            </div>
                            <div class="flex flex-col items-end text-right">
                                <span class="text-xs font-bold text-gray-900 dark:text-white cursor-pointer hover:text-blue-600 transition-colors" @click="$dispatch('open-contributors-modal', { clientId: {{ $data['client']->id }}, type: 'Boost' })">
                                    {{ $boostProgress }}%
                                </span>
                                <span class="text-[10px] text-gray-500">${{ $data['boost_amount'] }}/${{ $data['target_boost_budget'] ?? 0 }}</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                            <div class="bg-blue-500 h-2.5 rounded-full transition-all duration-500" 
                                 style="width: {{ $boostProgress }}%"></div>
                        </div>
                    </div>

                    <!-- Stats Summary -->
                    <div class="grid grid-cols-3 gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $data['total_actual'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Total Done</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $data['total_left'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Total Left</div>
                        </div>
                        <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div class="text-xl font-bold text-blue-600 dark:text-blue-400">$ {{ number_format($data['boost_amount'], 0) }}</div>
                            <div class="text-xs text-blue-500 dark:text-blue-300">Boost Amount</div>
                        </div>
                    </div>
                </div>

                <!-- Card Footer -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('dashboard.index', ['client_id' => $data['client']->id]) }}" 
                       class="flex items-center justify-center w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        View Full Dashboard
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close on overlay click
    window.onclick = function(event) {
        if (event.target.classList.contains('modal-overlay')) {
            event.target.parentElement.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }
        }
    }

   
</script>

<!-- Content Contributors Modal -->
<div x-show="showModal" 
     style="display: none;"
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true">
    
    <!-- Overlay -->
    <div x-show="showModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-filter backdrop-blur-sm" 
         @click="closeContributors"></div>

    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="showModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
            
            <!-- Modal Header -->
            <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white" id="modal-title">
                        <span x-text="activeType + ' Contributors'"></span>
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400" x-text="'For ' + clientName"></p>
                </div>
                <button @click="closeContributors" type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <span class="sr-only">Close</span>
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-4 max-h-[60vh] overflow-y-auto">
                <template x-if="filteredItems.length === 0">
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        No records found for this category.
                    </div>
                </template>

                <template x-if="filteredItems.length > 0">
                    <div class="overflow-hidden ring-1 ring-black ring-opacity-5 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Date</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Title</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Username</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                <template x-for="item in filteredItems" :key="item.title + item.date">
                                    <tr>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 dark:text-gray-400" x-text="item.date"></td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-gray-900 dark:text-white" x-text="item.title"></td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                                            <div class="flex items-center">
                                                <div class="h-6 w-6 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-xs font-bold text-primary-600 dark:text-primary-400 mr-2" x-text="item.avatar"></div>
                                                <span x-text="item.user"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>
            
            <div class="bg-gray-50 dark:bg-gray-700/30 px-6 py-4 flex justify-end">
                <button type="button" @click="closeContributors" class="w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

<x-contributors-modal />
