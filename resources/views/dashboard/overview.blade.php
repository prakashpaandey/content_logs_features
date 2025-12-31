@extends('layouts.app')

@section('content')
<div class="animate-fade-in">
    <!-- Agency Summary Header -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/50 p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">All Clients Overview</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Monitoring progress for all active clients in {{ $nepaliTranslate($dateContext->format('F'), 'month') }} {{ $nepaliTranslate($dateContext->format('Y'), 'year') }}
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <button onclick="openModal('bulk-target-modal')" 
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg shadow-primary-500/20 flex items-center">
                    <i class="fas fa-bullseye mr-2"></i>
                    Set Target For all Clients
                </button>
                
                <div class="flex items-center bg-gray-50 dark:bg-gray-700/50 p-1 rounded-xl border border-gray-200 dark:border-gray-600">
                    <a href="{{ route('clients.overview', ['month' => $dateContext->copy()->subMonth()->month, 'year' => $dateContext->copy()->subMonth()->year]) }}" 
                       class="p-2 hover:bg-white dark:hover:bg-gray-600 rounded-lg text-gray-600 dark:text-gray-400 transition-all">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </a>
                    <span class="px-4 text-sm font-bold text-gray-900 dark:text-white min-w-[120px] text-center">
                        {{ $nepaliTranslate($dateContext->format('F'), 'month') }}
                    </span>
                    <a href="{{ route('clients.overview', ['month' => $dateContext->copy()->addMonth()->month, 'year' => $dateContext->copy()->addMonth()->year]) }}" 
                       class="p-2 hover:bg-white dark:hover:bg-gray-600 rounded-lg text-gray-600 dark:text-gray-400 transition-all">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Portfolio Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
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
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-xs font-bold @if($data['actual_posts'] >= $data['target_posts'] && $data['target_posts'] > 0) text-green-600 @else text-gray-700 dark:text-gray-300 @endif">
                                    {{ $data['actual_posts'] }} / {{ $data['target_posts'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-xs font-bold @if($data['actual_reels'] >= $data['target_reels'] && $data['target_reels'] > 0) text-green-600 @else text-gray-700 dark:text-gray-300 @endif">
                                    {{ $data['actual_reels'] }} / {{ $data['target_reels'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="text-xs font-bold @if($data['actual_boosts'] >= $data['target_boosts'] && $data['target_boosts'] > 0) text-green-600 @else text-gray-700 dark:text-gray-300 @endif">
                                    {{ $data['actual_boosts'] }} / {{ $data['target_boosts'] }}
                                </span>
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

<!-- Bulk Target Modal -->
<div id="bulk-target-modal" class="modal hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="modal-overlay absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-lg w-full p-8 overflow-hidden">
            <div class="absolute top-0 right-0 p-6">
                <button onclick="closeModal('bulk-target-modal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="mb-8">
                <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-2xl flex items-center justify-center mb-4">
                    <i class="fas fa-bullseye text-primary-600 dark:text-primary-400 text-xl"></i>
                </div>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white">Set Target for all Clients</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Apply the same targets to all {{ $clients->count() }} clients simultaneously.
                </p>
            </div>

            <form action="{{ route('monthly-targets.bulk') }}" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Target Month</label>
                        <input type="text" id="bulk-target-bs-month" class="nepali-monthpicker w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all"
                               placeholder="Select Month"
                               data-ad-id="bulk-target-ad-month" required>
                        <input type="hidden" name="month" id="bulk-target-ad-month" value="{{ $dateContext->format('Y-m') }}">
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Posts</label>
                            <input type="number" name="target_posts" required min="0" placeholder="0"
                                   class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all text-center font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Reels</label>
                            <input type="number" name="target_reels" required min="0" placeholder="0"
                                   class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all text-center font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Boosts</label>
                            <input type="number" name="target_boosts" required min="0" placeholder="0"
                                   class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all text-center font-bold">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Notes (Optional)</label>
                        <textarea name="notes" rows="2" 
                                  class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 outline-none transition-all"
                                  placeholder="Standard target for all clients..."></textarea>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" 
                            class="w-full py-4 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-2xl shadow-xl shadow-primary-500/20 transition-all transform active:scale-[0.98]">
                        Apply to All Clients
                    </button>
                    <p class="text-[10px] text-center text-gray-400 mt-4 uppercase tracking-widest font-bold">
                        This will overwrite existing targets for the selected month.
                    </p>
                </div>
            </form>
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
