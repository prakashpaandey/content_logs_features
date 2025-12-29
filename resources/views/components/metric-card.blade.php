@props([
    'title' => '',
    'value' => '0',
    'change' => null,
    'icon' => 'fas fa-chart-bar',
    'color' => 'primary',
    'loading' => false,
])

@php
    $colorClasses = [
        'primary' => 'bg-primary-100 text-primary-600 dark:bg-primary-900/30 dark:text-primary-400',
        'green' => 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400',
        'yellow' => 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400',
        'red' => 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
        'purple' => 'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
        'blue' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
    ];
@endphp

<div class="metric-card bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ $title }}</p>
            @if($loading)
                <div class="h-8 w-20 shimmer rounded mb-2"></div>
            @else
                <p class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $value }}</p>
            @endif
            
            @if($change !== null && !$loading)
                <div class="flex items-center">
                    @if($change >= 0)
                        <span class="text-green-600 text-sm font-medium flex items-center">
                            <i class="fas fa-arrow-up mr-1 text-xs"></i>
                            {{ $change }}%
                        </span>
                    @else
                        <span class="text-red-600 text-sm font-medium flex items-center">
                            <i class="fas fa-arrow-down mr-1 text-xs"></i>
                            {{ abs($change) }}%
                        </span>
                    @endif
                    <span class="text-gray-500 text-sm ml-2">from last month</span>
                </div>
            @endif
        </div>
        
        <div class="{{ $colorClasses[$color] ?? $colorClasses['primary'] }} p-3 rounded-lg">
            <i class="{{ $icon }} text-lg"></i>
        </div>
    </div>
    
    @if(!$loading)
        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500 dark:text-gray-400">Progress</span>
                <span class="font-medium text-gray-900 dark:text-white">
                    @if(isset($progress))
                        {{ $progress }}%
                    @endif
                </span>
            </div>
            @if(isset($progress))
                <div class="mt-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="progress-bar h-2 rounded-full {{ $colorClasses[$color] ?? $colorClasses['primary'] }}" 
                         data-value="{{ $progress }}"></div>
                </div>
            @endif
        </div>
    @endif
</div>