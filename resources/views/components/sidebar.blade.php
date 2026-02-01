<!-- Mobile Overlay -->
<div x-show="sidebarOpen"
     x-cloak
     @click="sidebarOpen = false"
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm md:hidden"
     style="display: none;"></div>

<aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:flex md:flex-shrink-0 border-r border-gray-200 dark:border-gray-700 shadow-xl md:shadow-none overflow-x-hidden"
       :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
    <div class="flex flex-col h-full w-full">
        <!-- Client Panel Header -->
        <div class="px-4 py-5 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Clients</h2>
                @can('clients.create')
                <button onclick="openModal('create-client-modal')" 
                        class="px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center">
                    <i class="fas fa-plus mr-1.5"></i>
                    Add Client
                </button>
                @endcan
            </div>
            
            <!-- Search -->
            <div class="mt-4 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" 
                       id="client-search"
                       onkeyup="searchClients()"
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent text-sm"
                       placeholder="Search clients...">
            </div>
        </div>
        
        <!-- Portfolio Overview Link -->
        <div class="px-3 py-4 border-b border-gray-100 dark:border-gray-700/50">
            <a href="{{ route('clients.overview') }}" 
               class="flex items-center px-4 py-3 rounded-2xl {{ request()->routeIs('clients.overview') ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' : 'bg-gray-50 dark:bg-gray-700/30 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-all group">
                <i class="fas fa-chart-pie mr-3 {{ request()->routeIs('clients.overview') ? 'text-white' : 'text-primary-500' }}"></i>
                <span class="text-sm font-bold truncate">All Client Overview</span>
                @if(!request()->routeIs('clients.overview'))
                    <i class="fas fa-chevron-right ml-auto text-[10px] opacity-0 group-hover:opacity-100 transition-opacity"></i>
                @endif
            </a>
        </div>

        @if(auth()->user()->isAdmin())
        <!-- Admin Section -->
        <div class="px-3 py-4 border-b border-gray-100 dark:border-gray-700/50">
            <p class="px-4 mb-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Admin Panel</p>
            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center px-4 py-3 rounded-2xl {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/30' : 'text-gray-700 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20' }} transition-all group">
                <i class="fas fa-users-cog mr-3 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-indigo-500' }}"></i>
                <span class="text-sm font-bold truncate">Manage Users</span>
            </a>
        </div>
        @endif
        
        <!-- Client List -->
        <div class="flex-1 overflow-y-auto custom-scrollbar py-2">
            <!-- Loading State -->
            <div id="loading-state" class="hidden px-4 py-3">
                <div class="animate-pulse space-y-3">
                    <div class="h-12 bg-gray-200 rounded-lg"></div>
                    <div class="h-12 bg-gray-200 rounded-lg"></div>
                    <div class="h-12 bg-gray-200 rounded-lg"></div>
                </div>
            </div>
            
            <!-- Empty State -->
            <div id="empty-state" class="hidden px-4 py-8 text-center">
                <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                    <i class="fas fa-users text-gray-400 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No clients yet</h3>
                <p class="text-gray-500 text-sm mb-4">Add your first client to get started</p>
                <button onclick="openModal('create-client-modal')" 
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Add Client
                </button>
            </div>
            
            <!-- Client Items -->
            <div id="client-list" class="px-2 space-y-1">
                @foreach($clients as $client)
                    <a href="{{ route('dashboard.index', ['client_id' => $client->id]) }}" 
                       class="client-item block cursor-pointer p-3 rounded-lg border border-transparent hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 {{ isset($selectedClient) && $selectedClient->id == $client->id ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-500' : '' }} overflow-hidden">
                        <div class="flex items-center w-full">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-{{ $client->status == 'active' ? 'green' : 'gray' }}-500 to-{{ $client->status == 'active' ? 'emerald' : 'slate' }}-500 flex items-center justify-center text-white font-semibold text-sm">
                                    {{ $client->initials }}
                                </div>
                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                <p class="client-name text-sm font-medium text-gray-900 dark:text-white truncate" title="{{ $client->name }}">
                                    {{ $client->name }}
                                </p>
                                <p class="client-business text-xs text-gray-500 dark:text-gray-400 truncate" title="{{ $client->business_name }}">
                                    {{ $client->business_name }}
                                </p>
                            </div>
                            <div class="ml-2 flex-shrink-0">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $client->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($client->status) }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        
        <!-- Sidebar Footer -->
        <div id="sidebar-footer" class="border-t border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    <p>{{ count($clients) }} clients</p>
                </div>

            </div>
        </div>
    </div>
</aside>

@can('clients.create')
<!-- Create Client Modal -->
<div id="create-client-modal" class="modal hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="modal-overlay absolute inset-0 bg-gray-950/40 backdrop-blur-sm transition-opacity" onclick="closeModal('create-client-modal')"></div>
    <div class="relative min-h-screen flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="relative bg-white dark:bg-gray-900 rounded-t-[32px] sm:rounded-[32px] shadow-2xl max-w-md w-full mx-auto overflow-hidden transform transition-all animate-modal-pop border border-gray-100 dark:border-gray-800 pb-8 sm:pb-0">
            <!-- Modal Header -->
            <div class="relative px-6 sm:px-8 py-5 sm:py-6 bg-gradient-to-br from-primary-600 to-indigo-700">
                <div class="absolute top-0 right-0 p-4">
                    <button onclick="closeModal('create-client-modal')" class="text-white/70 hover:text-white transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center text-white shadow-inner">
                        <i class="fas fa-user-plus text-lg sm:text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-white">Add New Client</h3>
                        <p class="text-white/70 text-xs sm:text-sm">Create a new profile for your client</p>
                    </div>
                </div>
            </div>
            
            <div class="px-6 sm:px-8 py-6 sm:py-8">
                <form id="create-client-form" action="{{ route('clients.store') }}" method="POST" onsubmit="event.preventDefault(); submitFormAjax('create-client-form', 'create-client-modal')" class="space-y-5 sm:space-y-6">
                    @csrf
                    
                    <!-- Client Name -->
                    <div class="space-y-2 group">
                        <label class="block text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-300 ml-1">
                            Client Name <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary-500 transition-colors">
                                <i class="fas fa-user text-sm"></i>
                            </div>
                            <input type="text" name="name" required
                                   class="block w-full pl-11 pr-4 py-3 sm:py-3.5 bg-gray-50 dark:bg-gray-800/50 border border-transparent dark:border-gray-700 rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:bg-white dark:focus:bg-gray-800 focus:border-primary-500/30 transition-all font-sans text-sm sm:text-base"
                                   placeholder="Ram Shrestha">
                        </div>
                    </div>
                    
                    <!-- Business Name -->
                    <div class="space-y-2 group">
                        <label class="block text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-300 ml-1">
                            Business Name <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary-500 transition-colors">
                                <i class="fas fa-briefcase text-sm"></i>
                            </div>
                            <input type="text" name="business_name" required
                                   class="block w-full pl-11 pr-4 py-3 sm:py-3.5 bg-gray-50 dark:bg-gray-800/50 border border-transparent dark:border-gray-700 rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:bg-white dark:focus:bg-gray-800 focus:border-primary-500/30 transition-all font-sans text-sm sm:text-base"
                                   placeholder="Bhatbhateni">
                        </div>
                    </div>
                    
                    <!-- Status Selection -->
                    <div class="space-y-3">
                        <label class="block text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-300 ml-1">Account Status</label>
                        <div class="grid grid-cols-2 gap-3 sm:gap-4" x-data="{ selected: 'active' }">
                            <label class="relative flex items-center p-3 sm:p-4 cursor-pointer rounded-2xl border transition-all"
                                   :class="selected === 'active' ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-500 shadow-sm' : 'bg-gray-50 dark:bg-gray-800/50 border-transparent hover:border-gray-200 dark:hover:border-gray-700'">
                                <input type="radio" name="status" value="active" class="hidden" @change="selected = 'active'" checked>
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
                                <input type="radio" name="status" value="inactive" class="hidden" @change="selected = 'inactive'">
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
                <button type="submit" form="create-client-form"
                        class="w-full sm:w-auto order-1 sm:order-2 px-8 py-3.5 bg-gradient-to-r from-primary-600 to-indigo-600 hover:from-primary-700 hover:to-indigo-700 text-white text-sm font-bold rounded-2xl shadow-lg shadow-primary-500/25 transform transition-all active:scale-[0.98] outline-none">
                    Create Client
                </button>
                <button type="button" onclick="closeModal('create-client-modal')"
                        class="w-full sm:w-auto order-2 sm:order-1 px-6 py-3 text-sm font-bold text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
@endcan