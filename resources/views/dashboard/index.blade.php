@extends('layouts.app')

@section('content')
<div class="animate-fade-in">
    <!-- Client Header -->
    @include('partials.client-header')
    
    <!-- Overview Metrics -->
    <div class="mt-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Overview Metrics ({{ \Carbon\Carbon::now()->format('F Y') }})</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @include('components.metric-card', [
                'title' => 'Total Posts',
                'value' => $metrics['total_posts'],
                'change' => $metrics['posts_growth'],
                'icon' => 'fas fa-newspaper',
                'color' => 'primary',
                'progress' => $metrics['total_posts'] > 0 ? 100 : 0
            ])
            
            @include('components.metric-card', [
                'title' => 'Total Reels',
                'value' => $metrics['total_reels'],
                'change' => $metrics['reels_growth'],
                'icon' => 'fas fa-film',
                'color' => 'green',
                'progress' => $metrics['total_reels'] > 0 ? 100 : 0
            ])
            
            @include('components.metric-card', [
                'title' => 'Target Completion',
                'value' => $metrics['target_completion'] . '%',
                'change' => $metrics['target_growth'],
                'icon' => 'fas fa-bullseye',
                'color' => 'yellow',
                'progress' => $metrics['target_completion']
            ])
            
            @include('components.metric-card', [
                'title' => 'Variance',
                'value' => $metrics['variance'],
                'change' => $metrics['variance_growth'],
                'icon' => 'fas fa-chart-line',
                'color' => 'red',
                'progress' => 100
            ])
        </div>
    </div>
    
    <!-- Charts Section -->
    <div class="mt-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Content Analytics</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Line Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <div id="line-chart"></div>
            </div>
            
            <!-- Bar Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <div id="bar-chart"></div>
            </div>
            
            <!-- Pie Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <div id="pie-chart"></div>
            </div>
            
            <!-- Progress Visualization -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Monthly Progress</h3>
                <div class="space-y-6">
                    @php
                        // $currentTarget is passed from controller and represents the target for the current month
                        $postProgress = $currentTarget && $currentTarget->target_posts > 0 ? min(100, round(($metrics['total_posts'] / $currentTarget->target_posts) * 100)) : 0;
                        $reelProgress = $currentTarget && $currentTarget->target_reels > 0 ? min(100, round(($metrics['total_reels'] / $currentTarget->target_reels) * 100)) : 0;
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
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-primary-500 mr-2"></div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Posts</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $postProgress }}% ({{ $metrics['total_posts'] }}/{{ $currentTarget ? $currentTarget->target_posts : 0 }})
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                            <div class="bg-primary-500 h-2.5 rounded-full" style="width: {{ $postProgress }}%"></div>
                        </div>
                    </div>
                    
                    <!-- Reels Progress -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Reels</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $reelProgress }}% ({{ $metrics['total_reels'] }}/{{ $currentTarget ? $currentTarget->target_reels : 0 }})
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                            <div class="bg-green-500 h-2.5 rounded-full" style="width: {{ $reelProgress }}%"></div>
                        </div>
                    </div>
                    
                    <!-- Stats Summary -->
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $metrics['total_posts'] + $metrics['total_reels'] }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Total Done</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            @php
                                $totalTarget = $currentTarget ? ($currentTarget->target_posts + $currentTarget->target_reels) : 0;
                                $totalDone = $metrics['total_posts'] + $metrics['total_reels'];
                                $left = max(0, $totalTarget - $totalDone);
                            @endphp
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $left }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Total Left</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Monthly Targets -->
    @include('partials.monthly-targets')
    
    <!-- Content Table -->
    <div class="mt-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Social Content</h2>
            <button onclick="openModal('add-content-modal')" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Add Content
            </button>
        </div>
        
        @include('components.data-table')
    </div>
</div>

<script>
    window.dashboardChartData = @json($charts);
</script>
@endsection