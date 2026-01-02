@if(isset($selectedClient))
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
    <div class="flex flex-col md:flex-row md:items-center justify-between">
        <div class="flex items-center">
            <div class="w-16 h-16 rounded-full bg-gradient-to-r from-{{ $selectedClient->status == 'active' ? 'primary' : 'gray' }}-500 to-{{ $selectedClient->status == 'active' ? 'purple' : 'slate' }}-500 flex items-center justify-center text-white font-bold text-xl mr-4">
                {{ $selectedClient->initials }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $selectedClient->name }}</h1>
                <div class="flex items-center mt-1">
                    <p class="text-gray-600 dark:text-gray-400">{{ $selectedClient->business_name }}</p>
                    <span class="mx-3 text-gray-300">•</span>
                    <span class="px-3 py-1 {{ $selectedClient->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} text-xs font-medium rounded-full">
                        <i class="fas fa-circle text-xs mr-1"></i>
                        {{ ucfirst($selectedClient->status) }}
                    </span>
                    <span class="ml-3 text-gray-600 text-sm">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        @php $joinedBs = $dateHelpers->adToBs($selectedClient->created_at); @endphp
                        Joined {{ $nepaliTranslate($joinedBs['month'], 'month') }} {{ $joinedBs['year'] }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="flex space-x-3 mt-4 md:mt-0">
            @if(isset($selectedClient))
            <button onclick="openSecureDeleteModal('{{ route('clients.destroy', $selectedClient->id) }}', '{{ $selectedClient->name }}')" 
                    class="px-4 py-2 border border-red-200 dark:border-red-900/30 text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/10 hover:bg-red-100 dark:hover:bg-red-900/20 rounded-lg transition-colors flex items-center"
                    title="Delete Client">
                <i class="fas fa-trash-alt"></i>
            </button>
            @endif
            <button onclick="openModal('edit-client-modal')" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors flex items-center">
                <i class="fas fa-edit mr-2"></i>
                Edit Client
            </button>
            <button onclick="openModal('add-content-modal')" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Add Content
            </button>
        </div>
    </div>
    
    <!-- Month Navigation -->
    @php
        $currentBs = ['month' => $bsMonth, 'year' => $bsYear];
        
        // Calculate Prev BS Month
        $prevBsMonth = $bsMonth - 1;
        $prevBsYear = $bsYear;
        if ($prevBsMonth < 1) { $prevBsMonth = 12; $prevBsYear--; }
        
        // Calculate Next BS Month
        $nextBsMonth = $bsMonth + 1;
        $nextBsYear = $bsYear;
        if ($nextBsMonth > 12) { $nextBsMonth = 1; $nextBsYear++; }
    @endphp
    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700/50 flex items-center justify-between">
        <div class="flex items-center space-x-2">
            <a href="{{ route('dashboard.index', ['client_id' => $selectedClient->id, 'month' => $prevBsMonth, 'year' => $prevBsYear]) }}" 
               @click.stop
               class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 transition-colors"
               title="Previous Month">
                <i class="fas fa-chevron-left text-xs"></i>
            </a>
            
            <div class="flex items-center space-x-2 min-w-[200px]">
                <x-nepali-month-picker 
                    id="dashboard-month-nav" 
                    value="{{ $bsYear . '-' . str_pad($bsMonth, 2, '0', STR_PAD_LEFT) }}"
                    placeholder="Select Month"
                    redirectPattern="{{ route('dashboard.index', ['client_id' => $selectedClient->id]) }}&month=:month&year=:year" 
                />
            </div>

            <a href="{{ route('dashboard.index', ['client_id' => $selectedClient->id, 'month' => $nextBsMonth, 'year' => $nextBsYear]) }}" 
               @click.stop
               class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 transition-colors"
               title="Next Month">
                <i class="fas fa-chevron-right text-xs"></i>
            </a>
            
            @php
                $todayBs = \App\Helpers\NepaliDateHelper::adToBs(now());
                $isCurrentMonth = ($bsMonth == $todayBs['month'] && $bsYear == $todayBs['year']);
            @endphp
            
            @if(!$isCurrentMonth)
                <a href="{{ route('dashboard.index', ['client_id' => $selectedClient->id]) }}" 
                   class="ml-2 text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 text-xs font-bold uppercase tracking-wider transition-colors">
                    Reset
                </a>
            @endif
        </div>
        

    </div>
    

</div>

<!-- Edit Client Modal -->
<div id="edit-client-modal" class="modal hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-auto">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Client</h3>
                    <button onclick="closeModal('edit-client-modal')" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-4">
                <form id="edit-client-form" action="{{ route('clients.update', $selectedClient->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Client Name *
                            </label>
                            <input type="text" name="name" value="{{ $selectedClient->name }}" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Business Name *
                            </label>
                            <input type="text" name="business_name" value="{{ $selectedClient->business_name }}" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Status
                            </label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="active" {{ $selectedClient->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $selectedClient->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button type="button" onclick="closeModal('edit-client-modal')"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" form="edit-client-form"
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Update Client
                </button>
            </div>
        </div>
    </div>
</div>
@endif