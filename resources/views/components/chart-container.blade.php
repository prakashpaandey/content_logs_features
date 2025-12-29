@props([
    'title' => '',
    'type' => 'line',
    'height' => 350,
    'loading' => false,
])

<div class="chart-container bg-white rounded-xl border border-gray-200 p-5">
    @if($title)
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
            <div class="flex space-x-2">
                <button class="p-2 text-gray-500 hover:text-gray-700">
                    <i class="fas fa-download"></i>
                </button>
                <button class="p-2 text-gray-500 hover:text-gray-700">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
        </div>
    @endif
    
    @if($loading)
        <div class="flex items-center justify-center" style="height: {{ $height }}px">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-500"></div>
        </div>
    @else
        <div class="chart-placeholder" style="height: {{ $height }}px">
            {{ $slot }}
        </div>
    @endif
    
    <!-- Chart Legend -->
    @if(isset($legend) && $legend)
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex flex-wrap items-center justify-center space-x-4">
                @foreach($legend as $item)
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $item['color'] }}"></div>
                        <span class="text-sm text-gray-600">{{ $item['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>