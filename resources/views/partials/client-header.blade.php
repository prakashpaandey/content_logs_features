@if(isset($selectedClient))
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 md:p-5">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center min-w-0">
            <div class="w-12 h-12 md:w-16 md:h-16 rounded-full bg-gradient-to-r from-{{ $selectedClient->status == 'active' ? 'primary' : 'gray' }}-500 to-{{ $selectedClient->status == 'active' ? 'purple' : 'slate' }}-500 flex items-center justify-center text-white font-bold text-lg md:text-xl mr-3 md:mr-4 shrink-0 shadow-lg">
                {{ $selectedClient->initials }}
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white truncate">{{ $selectedClient->name }}</h1>
                    <span class="px-2 py-0.5 {{ $selectedClient->status == 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700' }} text-[10px] font-bold rounded-full uppercase tracking-wider">
                        {{ ucfirst($selectedClient->status) }}
                    </span>
                </div>
                <div class="flex items-center mt-0.5 text-xs md:text-sm text-gray-500 dark:text-gray-400 truncate">
                    <p class="truncate max-w-[150px] md:max-w-md" title="{{ $selectedClient->business_name }}">{{ $selectedClient->business_name }}</p>
                    <span class="mx-2 hidden md:inline">•</span>
                    <span class="hidden md:inline-block">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        @php $joinedBs = $dateHelpers->adToBs($selectedClient->created_at); @endphp
                        Joined {{ $nepaliTranslate($joinedBs['month'], 'month') }} {{ $joinedBs['year'] }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-2 md:flex md:items-center gap-2 w-full md:w-auto">
            <div class="col-span-1 flex gap-2">
                @if(isset($selectedClient))
                <button onclick="openSecureDeleteModal('{{ route('clients.destroy', $selectedClient->id) }}', '{{ $selectedClient->name }}')" 
                        class="px-3 py-2 border border-red-200 dark:border-red-900/30 text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/10 hover:bg-red-100 dark:hover:bg-red-900/20 rounded-lg transition-colors flex items-center justify-center shrink-0"
                        title="Delete Client">
                    <i class="fas fa-trash-alt"></i>
                </button>
                @endif
                <button onclick="openModal('edit-client-modal')" class="flex-1 md:flex-none px-3 md:px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors flex items-center justify-center text-xs md:text-sm font-medium">
                    <i class="fas fa-edit mr-1.5 md:mr-2"></i>
                    <span class="whitespace-nowrap">Edit</span>
                </button>
            </div>
            <button onclick="openModal('add-content-modal')" class="col-span-1 md:flex-none px-3 md:px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all shadow-md active:scale-95 flex items-center justify-center text-xs md:text-sm font-medium">
                <i class="fas fa-plus mr-1.5 md:mr-2"></i>
                <span class="whitespace-nowrap">Add Content</span>
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
    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700/50">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center w-full sm:w-auto">
                <div class="flex items-center flex-1 sm:flex-none bg-gray-50 dark:bg-gray-700/30 rounded-xl p-1 gap-1">
                    <a href="{{ route('dashboard.index', ['client_id' => $selectedClient->id, 'month' => $prevBsMonth, 'year' => $prevBsYear]) }}" 
                       @click.stop
                       class="p-2 rounded-lg hover:bg-white dark:hover:bg-gray-600 text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-all active:scale-95"
                       title="Previous Month">
                        <i class="fas fa-chevron-left text-[10px]"></i>
                    </a>
                    
                    <div class="flex-1 sm:flex-none min-w-0">
                        <div class="w-full sm:w-[200px]">
                            <x-nepali-month-picker 
                                id="dashboard-month-nav" 
                                value="{{ $bsYear . '-' . str_pad($bsMonth, 2, '0', STR_PAD_LEFT) }}"
                                placeholder="Select Month"
                                redirectPattern="{{ route('dashboard.index', ['client_id' => $selectedClient->id]) }}&month=:month&year=:year" 
                            />
                        </div>
                    </div>

                    <a href="{{ route('dashboard.index', ['client_id' => $selectedClient->id, 'month' => $nextBsMonth, 'year' => $nextBsYear]) }}" 
                       @click.stop
                       class="p-2 rounded-lg hover:bg-white dark:hover:bg-gray-600 text-gray-500 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 transition-all active:scale-95"
                       title="Next Month">
                        <i class="fas fa-chevron-right text-[10px]"></i>
                    </a>
                </div>
                
                @php
                    $todayBs = \App\Helpers\NepaliDateHelper::adToBs(now());
                    $isCurrentMonth = ($bsMonth == $todayBs['month'] && $bsYear == $todayBs['year']);
                @endphp
                
                @if(!$isCurrentMonth)
                    <a href="{{ route('dashboard.index', ['client_id' => $selectedClient->id]) }}" 
                       class="ml-3 px-3 py-1.5 text-primary-600 dark:text-primary-400 font-bold text-[10px] uppercase tracking-wider hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-colors">
                        Reset
                    </a>
                @endif
            </div>
            

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