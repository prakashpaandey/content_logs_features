@extends('layouts.app')

@section('content')
<div class="animate-fade-in">
    <!-- Client Header -->
    @include('partials.client-header')
    
    <!-- Overview Metrics -->
    <div class="mt-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Overview Metrics</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @include('components.metric-card', [
                'title' => 'Total Posts',
                'value' => '1,247',
                'change' => 12,
                'icon' => 'fas fa-newspaper',
                'color' => 'primary',
                'progress' => 85
            ])
            
            @include('components.metric-card', [
                'title' => 'Total Reels',
                'value' => '456',
                'change' => 24,
                'icon' => 'fas fa-film',
                'color' => 'green',
                'progress' => 65
            ])
            
            @include('components.metric-card', [
                'title' => 'Monthly Target',
                'value' => '78%',
                'change' => 8,
                'icon' => 'fas fa-bullseye',
                'color' => 'yellow',
                'progress' => 78
            ])
            
            @include('components.metric-card', [
                'title' => 'Variance',
                'value' => '-12%',
                'change' => -5,
                'icon' => 'fas fa-chart-line',
                'color' => 'red',
                'progress' => 88
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
                    <!-- Overall Progress -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Overall Completion</span>
                            <span class="text-lg font-bold text-primary-600 dark:text-primary-400">78%</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                            <div class="bg-gradient-to-r from-primary-500 to-purple-500 h-3 rounded-full" style="width: 78%"></div>
                        </div>
                    </div>
                    
                    <!-- Posts Progress -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-primary-500 mr-2"></div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Posts</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">85% (85/100)</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                            <div class="bg-primary-500 h-2.5 rounded-full" style="width: 85%"></div>
                        </div>
                    </div>
                    
                    <!-- Reels Progress -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Reels</span>
                            </div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">65% (13/20)</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                            <div class="bg-green-500 h-2.5 rounded-full" style="width: 65%"></div>
                        </div>
                    </div>
                    
                    <!-- Stats Summary -->
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">85</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Posts Done</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">15</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Posts Left</div>
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
            <button class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Add Content
            </button>
        </div>
        
        @include('components.data-table')
    </div>
</div>
@endsection