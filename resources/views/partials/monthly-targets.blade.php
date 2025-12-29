<div class="mt-8">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Monthly Targets</h2>
        <button onclick="openModal('create-target-modal')" 
                class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Create Monthly Target
        </button>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Month
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Target Posts
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Target Reels
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Created Date
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody id="targets-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @php
                        $targets = [
                            ['month' => 'March 2024', 'posts' => 100, 'reels' => 20, 'created' => '2024-02-28', 'status' => 'active'],
                            ['month' => 'February 2024', 'posts' => 90, 'reels' => 18, 'created' => '2024-01-31', 'status' => 'completed'],
                            ['month' => 'January 2024', 'posts' => 80, 'reels' => 15, 'created' => '2023-12-30', 'status' => 'completed'],
                            ['month' => 'December 2023', 'posts' => 70, 'reels' => 12, 'created' => '2023-11-30', 'status' => 'archived'],
                            ['month' => 'November 2023', 'posts' => 65, 'reels' => 10, 'created' => '2023-10-31', 'status' => 'archived'],
                        ];
                        
                        $statusColors = [
                            'active' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                            'completed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                            'archived' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                        ];
                    @endphp
                    
                    @foreach($targets as $target)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $target['month'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                {{ $target['posts'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                {{ $target['reels'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                {{ \Carbon\Carbon::parse($target['created'])->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$target['status']] }}">
                                    {{ ucfirst($target['status']) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right space-x-2">
                                <button onclick="viewTargetDetails('{{ $target['month'] }}')" class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="editTarget('{{ $target['month'] }}')" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="viewTargetHistory('{{ $target['month'] }}')" class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300">
                                    <i class="fas fa-history"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Empty State -->
        <div id="empty-targets-state" class="hidden p-8 text-center">
            <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                <i class="fas fa-bullseye text-gray-400 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No monthly targets yet</h3>
            <p class="text-gray-500 text-sm mb-4">Create your first monthly target to track progress</p>
            <button onclick="openModal('create-target-modal')" 
                    class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Create Target
            </button>
        </div>
    </div>
</div>

<!-- Create Target Modal -->
<div id="create-target-modal" class="modal hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full mx-auto">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Create Monthly Target</h3>
                    <button onclick="closeModal('create-target-modal')" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Set targets for the upcoming month</p>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-4">
                <form id="create-target-form" onsubmit="addMonthlyTarget(event)">
                    <div class="space-y-4">
                        <div>
                            <label for="target-month" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Month & Year *
                            </label>
                            <input type="month" id="target-month" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                   min="{{ date('Y-m') }}">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="target-posts" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Target Posts *
                                </label>
                                <input type="number" id="target-posts" required min="0"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                       placeholder="e.g., 100">
                            </div>
                            
                            <div>
                                <label for="target-reels" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Target Reels *
                                </label>
                                <input type="number" id="target-reels" required min="0"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                       placeholder="e.g., 20">
                            </div>
                        </div>
                        
                        <div>
                            <label for="target-notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Notes
                            </label>
                            <textarea id="target-notes" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                      placeholder="Add any additional notes or goals..."></textarea>
                        </div>
                        
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-900/50 rounded-lg p-3">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-yellow-500 dark:text-yellow-400 mr-2"></i>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                    Targets can be edited later. Historical changes will be tracked in the target history.
                                </p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button onclick="closeModal('create-target-modal')"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" form="create-target-form"
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Create Target
                </button>
            </div>
        </div>
    </div>
</div>