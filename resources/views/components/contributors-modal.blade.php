<div x-data="contributorsModal"
     x-show="showModal"
     style="display: none;"
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true"
     x-on:open-contributors-modal.window="open($event.detail)">
    
    <!-- Overlay -->
    <div x-show="showModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-filter backdrop-blur-sm" 
         @click="close"></div>

    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="showModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full">
            
            <!-- Modal Header -->
            <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white" id="modal-title">
                        <span x-text="activeType + ' Contributors'"></span>
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400" x-text="'For ' + clientName"></p>
                </div>
                <button @click="close" type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <span class="sr-only">Close</span>
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-4 max-h-[60vh] overflow-y-auto">
                <template x-if="filteredItems.length === 0">
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        No records found for this category.
                    </div>
                </template>

                <template x-if="filteredItems.length > 0">
                    <div class="overflow-hidden ring-1 ring-black ring-opacity-5 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Date</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Title</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Amount</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Username</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                <template x-for="item in filteredItems" :key="item.title + item.date">
                                    <tr>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 dark:text-gray-400" x-text="item.date"></td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-gray-900 dark:text-white" x-text="item.title"></td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                            <span x-show="item.amount" class="text-green-600 dark:text-green-400" x-text="item.amount"></span>
                                            <span x-show="!item.amount" class="text-gray-400">-</span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                                            <div class="flex items-center">
                                                <div class="h-6 w-6 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-xs font-bold text-primary-600 dark:text-primary-400 mr-2" x-text="item.avatar"></div>
                                                <span x-text="item.user"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>
            
            <div class="bg-gray-50 dark:bg-gray-700/30 px-6 py-4 flex justify-end">
                <button type="button" @click="close" class="w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('contributorsModal', () => ({
                showModal: false,
                activeType: 'all',
                clientName: '',
                items: [],
                
                open(detail) {
                    const clientId = detail.clientId;
                    const type = detail.type;

                    try {
                        const dataScript = document.getElementById('client-data-' + clientId);
                        if (!dataScript) {
                            alert('Error: Data script not found for client ' + clientId);
                            return;
                        }
                    
                        const data = JSON.parse(dataScript.textContent);
                        
                        // Normalize contents
                        const rawContents = Array.isArray(data.contents) ? data.contents : Object.values(data.contents || {});
                        let contentItems = rawContents.map(c => ({
                            date: c.date ? new Date(c.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) : 'N/A',
                            type: c.type || 'Content',
                            title: c.title || 'Untitled',
                            amount: null,
                            user: c.user ? c.user.name : (c.user_id ? 'User ID: ' + c.user_id : 'Not Recorded'),
                            avatar: c.user ? c.user.name.substring(0, 2).toUpperCase() : '??'
                        }));
                        
                        // Normalize boosts
                        const rawBoosts = Array.isArray(data.boosts) ? data.boosts : Object.values(data.boosts || {});
                        let boostItems = rawBoosts.map(b => ({
                            date: b.date ? new Date(b.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) : 'N/A',
                            type: 'Boost (' + (b.boosted_content_type || 'General') + ')',
                            title: b.title || 'Untitled Boost',
                            amount: b.amount ? '$ ' + parseFloat(b.amount).toLocaleString() : '$ 0',
                            user: b.user ? b.user.name : (b.user_id ? 'User ID: ' + b.user_id : 'Not Recorded'),
                            avatar: b.user ? b.user.name.substring(0, 2).toUpperCase() : '??'
                        }));

                        this.items = [...contentItems, ...boostItems];
                        this.activeType = type;
                        this.clientName = data.clientName;
                        this.showModal = true;
                    } catch (e) {
                        console.error(e);
                        alert('Error loading data: ' + e.message);
                    }
                },
                
                get filteredItems() {
                    if (this.activeType === 'all') return this.items;
                    if (this.activeType === 'Boost') return this.items.filter(i => i.type.startsWith('Boost'));
                    return this.items.filter(i => i.type === this.activeType);
                },

                close() {
                    this.showModal = false;
                }
            }));
        });
    </script>
</div>
