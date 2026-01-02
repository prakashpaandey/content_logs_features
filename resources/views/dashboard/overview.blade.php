@extends('layouts.app')

@section('content')
<div class="animate-fade-in">
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
            
            <div class="flex items-center gap-3">
                
                <div class="flex items-center space-x-2 min-w-[220px]">
                    <x-nepali-month-picker 
                        id="overview-month-nav" 
                        value="{{ $bsYear . '-' . str_pad($bsMonth, 2, '0', STR_PAD_LEFT) }}"
                        placeholder="Select Month"
                        redirectPattern="{{ route('clients.overview') }}?month=:month&year=:year" 
                    />
                </div>
            </div>
        </div>

        <!-- Portfolio Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
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
                    <span class="text-sm text-gray-500">/ {{ $totalAgencyMetrics['target_boosts'] }}</span>
                </div>
            </div>

            <div class="bg-yellow-50 dark:bg-yellow-900/10 p-4 rounded-xl border border-yellow-100 dark:border-yellow-900/20">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-yellow-600 dark:text-yellow-400 uppercase tracking-wider">Boost Amount</span>
                    <i class="fas fa-hand-holding-usd text-yellow-500"></i>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl font-black text-gray-900 dark:text-white">Rs. {{ number_format($totalAgencyMetrics['boost_amount'], 0) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Client Name</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Progress</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Posts</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reels</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Boosts</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Boost Amt</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($clientsData as $data)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mr-3">
                                        <i class="fas fa-user-tie text-primary-600 dark:text-primary-400"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $data['client']->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $data['client']->business_name ?? 'Client' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap min-w-[200px]">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-primary-500 to-purple-500 transition-all duration-500" 
                                             style="width: {{ $data['completion'] }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300">{{ $data['completion'] }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $postProgress = $data['target_posts'] > 0 ? min(100, round(($data['actual_posts'] / $data['target_posts']) * 100)) : 0;
                                @endphp
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-primary-500 transition-all duration-500" 
                                             style="width: {{ $postProgress }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 min-w-[35px] text-right">{{ $postProgress }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $reelProgress = $data['target_reels'] > 0 ? min(100, round(($data['actual_reels'] / $data['target_reels']) * 100)) : 0;
                                @endphp
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-green-500 transition-all duration-500" 
                                             style="width: {{ $reelProgress }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 min-w-[35px] text-right">{{ $reelProgress }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $boostProgress = $data['target_boosts'] > 0 ? min(100, round(($data['actual_boosts'] / $data['target_boosts']) * 100)) : 0;
                                @endphp
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-blue-500 transition-all duration-500" 
                                             style="width: {{ $boostProgress }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 min-w-[35px] text-right">{{ $boostProgress }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">Rs. {{ number_format($data['boost_amount'], 0) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($data['target'])
                                    <span class="px-3 py-1 text-[10px] font-bold rounded-full uppercase tracking-widest
                                        @if($data['target']->status === 'completed') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                        @else bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 @endif">
                                        {{ $data['target']->status }}
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-[10px] font-bold rounded-full bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 uppercase tracking-widest">
                                        No Target
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('dashboard.index', ['client_id' => $data['client']->id]) }}" 
                                   class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors">
                                    <i class="fas fa-external-link-alt mr-2"></i>
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
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
</script>
@endsection
