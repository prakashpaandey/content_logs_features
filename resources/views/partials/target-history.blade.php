<div class="mt-8 hidden" id="target-history-section">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Target History</h2>
        <button onclick="document.getElementById('target-history-section').classList.add('hidden')"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            Close History
        </button>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <!-- Timeline -->
        <div class="relative">
            <!-- Timeline line -->
            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>
            
            <!-- Timeline items -->
            <div class="space-y-8">
                <!-- Item 1 -->
                <div class="relative pl-12">
                    <div class="absolute left-0 w-8 h-8 rounded-full bg-primary-500 flex items-center justify-center">
                        <i class="fas fa-edit text-white text-sm"></i>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-gray-900 dark:text-white">Target Updated</h4>
                            <span class="text-sm text-gray-500 dark:text-gray-400">March 15, 2024</span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">Posts target increased from 80 to 100</p>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="bg-white dark:bg-gray-800 p-2 rounded border">
                                <span class="text-gray-500 dark:text-gray-400">Before:</span>
                                <span class="font-medium ml-2">80 Posts, 15 Reels</span>
                            </div>
                            <div class="bg-white dark:bg-gray-800 p-2 rounded border border-primary-200 dark:border-primary-800">
                                <span class="text-primary-600 dark:text-primary-400">After:</span>
                                <span class="font-medium ml-2">100 Posts, 20 Reels</span>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-user mr-1"></i>
                            Updated by John Doe
                        </div>
                    </div>
                </div>
                
                <!-- Item 2 -->
                <div class="relative pl-12">
                    <div class="absolute left-0 w-8 h-8 rounded-full bg-green-500 flex items-center justify-center">
                        <i class="fas fa-check text-white text-sm"></i>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-gray-900 dark:text-white">Target Completed</h4>
                            <span class="text-sm text-gray-500 dark:text-gray-400">February 28, 2024</span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">Monthly target achieved successfully</p>
                        <div class="flex items-center text-sm">
                            <div class="mr-4">
                                <span class="text-gray-500 dark:text-gray-400">Actual Posts:</span>
                                <span class="font-medium ml-2 text-green-600 dark:text-green-400">92/90</span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400">Actual Reels:</span>
                                <span class="font-medium ml-2 text-green-600 dark:text-green-400">20/18</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Item 3 -->
                <div class="relative pl-12">
                    <div class="absolute left-0 w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center">
                        <i class="fas fa-plus text-white text-sm"></i>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-gray-900 dark:text-white">Target Created</h4>
                            <span class="text-sm text-gray-500 dark:text-gray-400">January 31, 2024</span>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">Initial target set for February 2024</p>
                        <div class="text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Target:</span>
                            <span class="font-medium ml-2">90 Posts, 18 Reels</span>
                        </div>
                        <div class="mt-2 text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Notes:</span>
                            <span class="ml-2">Focus on product launch campaign</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Empty State -->
        <div id="empty-history-state" class="hidden p-8 text-center">
            <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                <i class="fas fa-history text-gray-400 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No history available</h3>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Target history will appear here when changes are made</p>
        </div>
    </div>
</div>