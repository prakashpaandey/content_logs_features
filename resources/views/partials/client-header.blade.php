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
                @can('clients.delete')
                @if(isset($selectedClient))
                <button onclick="openSecureDeleteModal('{{ route('clients.destroy', $selectedClient->id) }}', '{{ $selectedClient->name }}')" 
                        class="px-3 py-2 border border-red-200 dark:border-red-900/30 text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/10 hover:bg-red-100 dark:hover:bg-red-900/20 rounded-lg transition-colors flex items-center justify-center shrink-0"
                        title="Delete Client">
                    <i class="fas fa-trash-alt"></i>
                </button>
                @endif
                @endcan

                @can('clients.update')
                <button onclick="openModal('edit-client-modal')" class="flex-1 md:flex-none px-3 md:px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors flex items-center justify-center text-xs md:text-sm font-medium">
                    <i class="fas fa-edit mr-1.5 md:mr-2"></i>
                    <span class="whitespace-nowrap">Edit</span>
                </button>
                @endcan
            </div>

            @can('contents.create')
            <button onclick="openModal('add-content-modal')" class="col-span-1 md:flex-none px-3 md:px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all shadow-md active:scale-95 flex items-center justify-center text-xs md:text-sm font-medium">
                <i class="fas fa-plus mr-1.5 md:mr-2"></i>
                <span class="whitespace-nowrap">Add Content</span>
            </button>
            @endcan
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
                        <div class="w-full sm:w-[220px]">
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
    <div class="modal-overlay absolute inset-0 bg-gray-950/40 backdrop-blur-sm transition-opacity" onclick="closeModal('edit-client-modal')"></div>
    <div class="relative min-h-screen flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="relative bg-white dark:bg-gray-900 rounded-t-[32px] sm:rounded-[32px] shadow-2xl max-w-md w-full mx-auto overflow-hidden transform transition-all animate-modal-pop border border-gray-100 dark:border-gray-800 pb-8 sm:pb-0">
            <!-- Modal Header -->
            <div class="relative px-6 sm:px-8 py-5 sm:py-6 bg-gradient-to-br from-indigo-600 to-purple-700">
                <div class="absolute top-0 right-0 p-4">
                    <button onclick="closeModal('edit-client-modal')" class="text-white/70 hover:text-white transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-white shadow-inner">
                        <i class="fas fa-edit text-lg sm:text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-white">Edit Client</h3>
                        <p class="text-white/70 text-xs sm:text-sm">Update profile for {{ $selectedClient->name }}</p>
                    </div>
                </div>
            </div>
            
            <div class="px-6 sm:px-8 py-6 sm:py-8">
                <form id="edit-client-form" action="{{ route('clients.update', $selectedClient->id) }}" method="POST" onsubmit="event.preventDefault(); submitFormAjax('edit-client-form', 'edit-client-modal')" class="space-y-5 sm:space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <!-- Client Name -->
                    <div class="space-y-2 group">
                        <label class="block text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-300 ml-1">
                            Client Name <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                                <i class="fas fa-user text-sm"></i>
                            </div>
                            <input type="text" name="name" value="{{ $selectedClient->name }}" required
                                   class="block w-full pl-11 pr-4 py-3 sm:py-3.5 bg-gray-50 dark:bg-gray-800/50 border border-transparent dark:border-gray-700 rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:bg-white dark:focus:bg-gray-800 focus:border-indigo-500/30 transition-all font-sans text-sm sm:text-base"
                                   >
                        </div>
                    </div>
                    
                    <!-- Business Name -->
                    <div class="space-y-2 group">
                        <label class="block text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-300 ml-1">
                            Business Name <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-500 transition-colors">
                                <i class="fas fa-briefcase text-sm"></i>
                            </div>
                            <input type="text" name="business_name" value="{{ $selectedClient->business_name }}" required
                                   class="block w-full pl-11 pr-4 py-3 sm:py-3.5 bg-gray-50 dark:bg-gray-800/50 border border-transparent dark:border-gray-700 rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:bg-white dark:focus:bg-gray-800 focus:border-indigo-500/30 transition-all font-sans text-sm sm:text-base"
                                  >
                        </div>
                    </div>
                    
                    <!-- Status Selection -->
                    <div class="space-y-3">
                        <label class="block text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-300 ml-1">Account Status</label>
                        <div class="grid grid-cols-2 gap-3 sm:gap-4" x-data="{ selected: '{{ $selectedClient->status }}' }">
                            <label class="relative flex items-center p-3 sm:p-4 cursor-pointer rounded-2xl border transition-all"
                                   :class="selected === 'active' ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-500 shadow-sm' : 'bg-gray-50 dark:bg-gray-800/50 border-transparent hover:border-gray-200 dark:hover:border-gray-700'">
                                <input type="radio" name="status" value="active" class="hidden" @change="selected = 'active'" {{ $selectedClient->status == 'active' ? 'checked' : '' }}>
                                <div class="flex items-center space-x-2 sm:space-x-3">
                                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center"
                                         :class="selected === 'active' ? 'bg-emerald-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500'">
                                        <i class="fas fa-check text-[10px] sm:text-xs"></i>
                                    </div>
                                    <span class="text-xs sm:text-sm font-bold" :class="selected === 'active' ? 'text-emerald-700 dark:text-emerald-400' : 'text-gray-500'">Active</span>
                                </div>
                            </label>

                            <label class="relative flex items-center p-3 sm:p-4 cursor-pointer rounded-2xl border transition-all"
                                   :class="selected === 'inactive' ? 'bg-gray-100 dark:bg-gray-700 border-gray-400 dark:border-gray-500' : 'bg-gray-50 dark:bg-gray-800/50 border-transparent hover:border-gray-200 dark:hover:border-gray-700'">
                                <input type="radio" name="status" value="inactive" class="hidden" @change="selected = 'inactive'" {{ $selectedClient->status == 'inactive' ? 'checked' : '' }}>
                                <div class="flex items-center space-x-2 sm:space-x-3">
                                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center"
                                         :class="selected === 'inactive' ? 'bg-gray-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500'">
                                        <i class="fas fa-pause text-[10px] sm:text-xs"></i>
                                    </div>
                                    <span class="text-xs sm:text-sm font-bold" :class="selected === 'inactive' ? 'text-gray-900 dark:text-white' : 'text-gray-500'">Inactive</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Modal Footer -->
            <div class="px-6 sm:px-8 py-4 sm:py-6 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800 flex flex-col sm:flex-row items-center justify-end gap-3">
                <button type="submit" form="edit-client-form"
                        class="w-full sm:w-auto order-1 sm:order-2 px-8 py-3.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-sm font-bold rounded-2xl shadow-lg shadow-indigo-500/25 transform transition-all active:scale-[0.98] outline-none">
                    Update Client
                </button>
                <button type="button" onclick="closeModal('edit-client-modal')"
                        class="w-full sm:w-auto order-2 sm:order-1 px-6 py-3 text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
@endif