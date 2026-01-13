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
                <button onclick="openModal('create-client-modal')" 
                        class="px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center">
                    <i class="fas fa-plus mr-1.5"></i>
                    Add Client
                </button>
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
        <div class="border-t border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    <p>{{ count($clients) }} clients</p>
                </div>

            </div>
        </div>
    </div>
</aside>

<!-- Create Client Modal -->
<div id="create-client-modal" class="modal hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-auto">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Add New Client</h3>
                    <button onclick="closeModal('create-client-modal')" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-4">
                <form id="create-client-form" action="{{ route('clients.store') }}" method="POST" onsubmit="event.preventDefault(); submitFormAjax('create-client-form', 'create-client-modal')">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Client Name *
                            </label>
                            <input type="text" name="name" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Business Name *
                            </label>
                            <input type="text" name="business_name" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Status
                            </label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button type="button" onclick="closeModal('create-client-modal')"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" form="create-client-form"
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Create Client
                </button>
            </div>
        </div>
    </div>
</div>