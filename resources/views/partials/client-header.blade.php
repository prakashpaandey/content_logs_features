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
                        Joined {{ $selectedClient->created_at->format('M Y') }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="flex space-x-3 mt-4 md:mt-0">
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
    
    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
        <div class="text-center">
            <div class="text-2xl font-bold text-primary-600">{{ $targets->where('status', 'active')->count() }}</div>
            <div class="text-sm text-gray-500">Active Targets</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-green-600">{{ $metrics['total_posts'] + $metrics['total_reels'] }}</div>
            <div class="text-sm text-gray-500">Total Content</div>
        </div>
        <div class="text-center">
            @php
                 $latestContent = $contentData->first();
                 $lastActive = $latestContent ? \Carbon\Carbon::parse($latestContent->date)->diffForHumans() : 'N/A';
            @endphp
            <div class="text-2xl font-bold text-yellow-600">{{ $contentData->count() }}</div>
            <div class="text-sm text-gray-500">Total Activities</div>
        </div>
        <div class="text-center">
            <div class="text-2xl font-bold text-purple-600">{{ \Carbon\Carbon::now()->subMonth()->format('M') }}</div>
            <div class="text-sm text-gray-500">Last Report</div>
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