@extends('layouts.app')

@section('content')
<div class="animate-fade-in">
    <!-- Client Header -->
    @include('partials.client-header')
    

    <!-- Charts Section -->
    <div class="mt-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Content Analytics</h2>
        <div class="grid grid-cols-1 gap-6">

            <!-- Progress Visualization -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Monthly Progress</h3>
                    @if($currentTarget)
                        <span class="px-3 py-1 text-xs font-bold rounded-full uppercase tracking-widest
                            {{ $currentTarget->status === 'completed' 
                                ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' 
                                : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                            {{ ucfirst($currentTarget->status) }}
                        </span>
                    @endif
                </div>
                <div class="space-y-6">
                    @php
                        // $currentTarget is passed from controller and represents the target for the current month
                        $postProgress = $currentTarget && $currentTarget->target_posts > 0 ? min(100, round(($metrics['total_posts'] / $currentTarget->target_posts) * 100)) : 0;
                        $reelProgress = $currentTarget && $currentTarget->target_reels > 0 ? min(100, round(($metrics['total_reels'] / $currentTarget->target_reels) * 100)) : 0;
                        $boostProgress = $currentTarget && $currentTarget->target_boost_budget > 0 ? min(100, round(($metrics['total_boost_amount'] / $currentTarget->target_boost_budget) * 100)) : 0;
                    @endphp

                    <!-- Overall Progress -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Overall Completion</span>
                            <span class="text-lg font-bold text-primary-600">{{ $metrics['target_completion'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                            <div class="bg-gradient-to-r from-primary-500 to-purple-500 h-3 rounded-full" style="width: {{ $metrics['target_completion'] }}%"></div>
                        </div>
                    </div>
                    
                    <!-- Posts Progress -->
                    <div class="group">
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center">
                                <span class="text-xs md:text-sm font-semibold text-gray-600 dark:text-gray-400">Posts</span>
                            </div>
                            <span class="text-xs md:text-sm font-bold text-gray-900 dark:text-white">
                                {{ $postProgress }}% <span class="text-gray-400 font-medium ml-1">({{ $metrics['total_posts'] }}/{{ $currentTarget ? $currentTarget->target_posts : 0 }})</span>
                            </span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-700/50 rounded-full h-2 md:h-2.5 overflow-hidden">
                            <div class="bg-primary-500 h-full rounded-full transition-all duration-1000 ease-out" style="width: {{ $postProgress }}%"></div>
                        </div>
                    </div>
                    
                    <!-- Reels Progress -->
                    <div class="group">
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center">
                                <span class="text-xs md:text-sm font-semibold text-gray-600 dark:text-gray-400">Reels</span>
                            </div>
                            <span class="text-xs md:text-sm font-bold text-gray-900 dark:text-white">
                                {{ $reelProgress }}% <span class="text-gray-400 font-medium ml-1">({{ $metrics['total_reels'] }}/{{ $currentTarget ? $currentTarget->target_reels : 0 }})</span>
                            </span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-700/50 rounded-full h-2 md:h-2.5 overflow-hidden">
                            <div class="bg-green-500 h-full rounded-full transition-all duration-1000 ease-out" style="width: {{ $reelProgress }}%"></div>
                        </div>
                    </div>

                    <!-- Boosts Progress -->
                    <div class="group">
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center">
                                <span class="text-xs md:text-sm font-semibold text-gray-600 dark:text-gray-400">Boosts</span>
                            </div>
                            <span class="text-xs md:text-sm font-bold text-gray-900 dark:text-white">
                                {{ $boostProgress }}% <span class="text-gray-400 font-medium ml-1">($ {{ number_format($metrics['total_boost_amount']) }} / $ {{ number_format($currentTarget ? $currentTarget->target_boost_budget : 0) }})</span>
                            </span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-700/50 rounded-full h-2 md:h-2.5 overflow-hidden">
                            <div class="bg-blue-500 h-full rounded-full transition-all duration-1000 ease-out" style="width: {{ $boostProgress }}%"></div>
                        </div>
                    </div>
                    
                    <!-- Stats Summary -->
                    <div class="grid grid-cols-3 gap-2 md:gap-4 pt-6 border-t border-gray-100 dark:border-gray-700/50">
                        <div class="text-center p-2 md:p-3 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-100 dark:border-gray-700/30">
                            <div class="text-base md:text-2xl font-black text-gray-900 dark:text-white leading-none">
                                {{ $metrics['total_posts'] + $metrics['total_reels'] + $metrics['total_boosts'] }}
                            </div>
                            <div class="text-[10px] md:text-xs text-gray-400 dark:text-gray-500 mt-1 uppercase font-bold tracking-tighter">Done</div>
                        </div>
                        <div class="text-center p-2 md:p-3 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-100 dark:border-gray-700/30">
                            <div class="text-base md:text-2xl font-black text-gray-900 dark:text-white leading-none">
                                {{ $metrics['total_left'] ?? 0 }}
                            </div>
                            <div class="text-[10px] md:text-xs text-gray-400 dark:text-gray-500 mt-1 uppercase font-bold tracking-tighter">Left</div>
                        </div>
                        <div class="text-center p-2 md:p-3 bg-primary-50 dark:bg-primary-900/10 rounded-xl border border-primary-100/50 dark:border-primary-900/20">
                            <div class="text-base md:text-2xl font-black text-primary-600 dark:text-primary-400 leading-none">
                                ${{ number_format($metrics['total_boost_amount'], 0) }}
                            </div>
                            <div class="text-[10px] md:text-xs text-primary-500/70 dark:text-primary-400/50 mt-1 uppercase font-bold tracking-tighter">Boost</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Monthly Targets -->
    @include('partials.monthly-targets')
    
    <!-- Content & Boosts Section -->
    <div class="mt-8" x-data="{ activeTab: 'content' }">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-6 gap-4">
            <div class="flex items-center space-x-1 p-1 bg-gray-100 dark:bg-gray-700/50 rounded-xl w-full lg:w-auto">
                <button @click="activeTab = 'content'" 
                        :class="activeTab === 'content' ? 'bg-white dark:bg-gray-800 shadow-md text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                        class="flex-1 lg:flex-none px-6 py-2.5 text-sm font-bold rounded-lg transition-all duration-200 uppercase tracking-tight">
                    Social Content
                </button>
                <button @click="activeTab = 'boosts'" 
                        :class="activeTab === 'boosts' ? 'bg-white dark:bg-gray-800 shadow-md text-primary-600 dark:text-primary-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                        class="flex-1 lg:flex-none px-6 py-2.5 text-sm font-bold rounded-lg transition-all duration-200 uppercase tracking-tight">
                    Boost Tracking
                </button>
            </div>
            
            <div class="flex items-center space-x-3 w-full lg:w-auto">
                <button x-show="activeTab === 'content'" @click="openModal('add-content-modal')" class="flex-1 lg:flex-none px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-primary-600/20 transition-all active:scale-95 flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i>
                    Add Content
                </button>
                <button x-show="activeTab === 'boosts'" @click="openModal('add-boost-modal')" class="flex-1 lg:flex-none px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-600/20 transition-all active:scale-95 flex items-center justify-center">
                    <i class="fas fa-rocket mr-2"></i>
                    Add Boost Record
                </button>
            </div>
        </div>
        
        <div x-show="activeTab === 'content'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            @include('components.data-table', ['contentData' => $contentData, 'bsMonth' => $bsMonth, 'bsYear' => $bsYear])
        </div>

        <div x-show="activeTab === 'boosts'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            @include('components.boosts-table', ['boostData' => $boostData, 'bsMonth' => $bsMonth, 'bsYear' => $bsYear])
        </div>
    </div>
</div>

<script>
    window.dashboardChartData = @json($charts);
</script>
@endsection