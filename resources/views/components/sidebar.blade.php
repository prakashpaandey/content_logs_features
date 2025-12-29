<aside class="hidden md:flex md:flex-shrink-0">
    <div class="flex flex-col w-64 border-r border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
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
        
        <!-- Client List -->
        <div class="flex-1 overflow-y-auto custom-scrollbar py-2">
            <!-- Loading State -->
            <div id="loading-state" class="hidden px-4 py-3">
                <div class="animate-pulse space-y-3">
                    <div class="h-12 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                    <div class="h-12 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                    <div class="h-12 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                </div>
            </div>
            
            <!-- Empty State -->
            <div id="empty-state" class="hidden px-4 py-8 text-center">
                <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                    <i class="fas fa-users text-gray-400 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No clients yet</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">Add your first client to get started</p>
                <button onclick="openModal('create-client-modal')" 
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Add Client
                </button>
            </div>
            
            <!-- Client Items -->
            <div id="client-list" class="px-2 space-y-1">
                @php
                    $clients = [
                        ['id' => 1, 'name' => 'John Smith', 'business' => 'TechCorp Inc.', 'status' => 'active', 'initials' => 'JS'],
                        ['id' => 2, 'name' => 'Sarah Johnson', 'business' => 'Marketing Pro', 'status' => 'active', 'initials' => 'SJ'],
                        ['id' => 3, 'name' => 'Mike Wilson', 'business' => 'Retail Solutions', 'status' => 'active', 'initials' => 'MW'],
                        ['id' => 4, 'name' => 'Emma Davis', 'business' => 'Creative Studio', 'status' => 'inactive', 'initials' => 'ED'],
                        ['id' => 5, 'name' => 'Robert Brown', 'business' => 'Consulting Firm', 'status' => 'active', 'initials' => 'RB'],
                        ['id' => 6, 'name' => 'Lisa Anderson', 'business' => 'Fashion Brand', 'status' => 'active', 'initials' => 'LA'],
                        ['id' => 7, 'name' => 'David Lee', 'business' => 'Food Services', 'status' => 'inactive', 'initials' => 'DL'],
                        ['id' => 8, 'name' => 'Jessica Taylor', 'business' => 'Health & Wellness', 'status' => 'active', 'initials' => 'JT'],
                    ];
                @endphp
                
                @foreach($clients as $client)
                    <div class="client-item cursor-pointer p-3 rounded-lg border border-transparent hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200 {{ $client['id'] == 1 ? 'bg-primary-50 dark:bg-primary-900 border-primary-500' : '' }}"
                         data-client-id="{{ $client['id'] }}"
                         onclick="selectClient({{ $client['id'] }})">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-{{ $client['status'] == 'active' ? 'green' : 'gray' }}-500 to-{{ $client['status'] == 'active' ? 'emerald' : 'slate' }}-500 flex items-center justify-center text-white font-semibold text-sm">
                                    {{ $client['initials'] }}
                                </div>
                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                <p class="client-name text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $client['name'] }}
                                </p>
                                <p class="client-business text-xs text-gray-500 dark:text-gray-400 truncate">
                                    {{ $client['business'] }}
                                </p>
                            </div>
                            <div class="ml-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $client['status'] == 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ ucfirst($client['status']) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Sidebar Footer -->
        <div class="border-t border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    <p>{{ count($clients) }} clients</p>
                </div>
                <button class="text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300 text-sm font-medium">
                    <i class="fas fa-cog mr-1"></i>
                    Settings
                </button>
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
                    <button onclick="closeModal('create-client-modal')" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-4">
                <form id="create-client-form">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Client Name *
                            </label>
                            <input type="text" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Business Name *
                            </label>
                            <input type="text" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Email Address
                            </label>
                            <input type="email"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Phone Number
                            </label>
                            <input type="tel"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Status
                            </label>
                            <select class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Notes
                            </label>
                            <textarea rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button onclick="closeModal('create-client-modal')"
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