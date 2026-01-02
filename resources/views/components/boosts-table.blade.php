@php
    $platformColors = [
        'Instagram' => 'bg-pink-100 text-pink-800 dark:bg-pink-900/30 dark:text-pink-300',
        'Facebook' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
        'TikTok' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    ];
    
    $typeColors = [
        'Post' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
        'Reel' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
    ];
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Content Type
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Platform
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Title
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Amount
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        URL
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @if($boostData->count() > 0)
                    @foreach($boostData as $boost)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                @php 
                                    $boostDate = \Carbon\Carbon::parse($boost->date);
                                    $bsDate = $dateHelpers->adToBs($boostDate);
                                @endphp
                                {{ $nepaliTranslate($bsDate['month'], 'month') }} {{ $bsDate['day'] }}, {{ $bsDate['year'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $typeColors[$boost->boosted_content_type] ?? 'bg-gray-100' }}">
                                    {{ ucfirst($boost->boosted_content_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $platformColors[$boost->platform] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ $boost->platform }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $boost->title }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-semibold">
                                Rs. {{ number_format($boost->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($boost->url)
                                    <a href="{{ $boost->url }}" target="_blank" 
                                       class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 flex items-center">
                                        <i class="fas fa-external-link-alt mr-1 text-xs"></i>
                                        View
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right space-x-2">
                                <button onclick='openEditBoostModal(@json($boost))' class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="openDeleteModal('{{ route('boosts.destroy', $boost->id) }}')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            No boost records found.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        {{ $boostData->links() }}
    </div>
</div>

<!-- Add Boost Modal -->
<div id="add-boost-modal" class="modal hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full mx-auto">
             <!-- Modal Header -->
             <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Add New Boost Record</h3>
                    <button onclick="closeModal('add-boost-modal')" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-4">
                <form id="add-boost-form" action="{{ route('boosts.store') }}" method="POST">
                    @csrf
                    @if(isset($selectedClient))
                        <input type="hidden" name="client_id" value="{{ $selectedClient->id }}">
                    @endif
                    <input type="hidden" name="context_bs_month" value="{{ $bsMonth }}">
                    <input type="hidden" name="context_bs_year" value="{{ $bsYear }}">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title *</label>
                            <input type="text" name="title" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Platform *</label>
                                <select name="platform" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="Instagram">Instagram</option>
                                    <option value="Facebook">Facebook</option>
                                    <option value="TikTok">TikTok</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content Type *</label>
                                <select name="boosted_content_type" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="Post">Post</option>
                                    <option value="Reel">Reel</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                @php
                                    $todayBs = \App\Helpers\NepaliDateHelper::adToBs(now());
                                    $isCurrentMonth = ($bsMonth == $todayBs['month'] && $bsYear == $todayBs['year']);
                                    
                                    // Default to today's day if we are in the current BS month context, otherwise day 1
                                    $defaultDay = $isCurrentMonth ? $todayBs['day'] : 1; 
                                    
                                    $defaultBsDate = $bsYear . '-' . str_pad($bsMonth, 2, '0', STR_PAD_LEFT) . '-' . str_pad($defaultDay, 2, '0', STR_PAD_LEFT);
                                    
                                    // Get the AD start date for this BS month
                                    [$contextStartDate, $contextEndDate] = \App\Helpers\NepaliDateHelper::getBsMonthRange($bsMonth, $bsYear);
                                    $defaultAdDate = $contextStartDate->copy()->addDays($defaultDay - 1)->format('Y-m-d');
                                @endphp
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date *</label>
                                <input type="text" id="add-boost-bs-date" class="nepali-datepicker w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" 
                                       placeholder="Select Nepali Date"
                                       data-ad-id="add-boost-ad-date" 
                                       data-nepali-format="YYYY-MM-DD"
                                       value="{{ $defaultBsDate }}"
                                       required>
                                <input type="hidden" name="date" id="add-boost-ad-date" value="{{ $defaultAdDate }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount Spent *</label>
                                <input type="number" name="amount" step="0.01" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" placeholder="0.00">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL</label>
                            <input type="url" name="url" placeholder="https://..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                            <textarea name="remarks" rows="2" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            
             <!-- Modal Footer -->
             <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button type="button" onclick="closeModal('add-boost-modal')" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                <button type="submit" form="add-boost-form" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg">Add Boost</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Boost Modal -->
<div id="edit-boost-modal" class="modal hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full mx-auto">
             <!-- Modal Header -->
             <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Boost Record</h3>
                    <button onclick="closeModal('edit-boost-modal')" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-4">
                <form id="edit-boost-form" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title *</label>
                            <input type="text" id="edit-boost-title" name="title" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Platform *</label>
                                <select id="edit-boost-platform" name="platform" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="Instagram">Instagram</option>
                                    <option value="Facebook">Facebook</option>
                                    <option value="TikTok">TikTok</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content Type *</label>
                                <select id="edit-boost-content-type" name="boosted_content_type" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="Post">Post</option>
                                    <option value="Reel">Reel</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date *</label>
                                <input type="text" id="edit-boost-bs-date" class="nepali-datepicker w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" 
                                       placeholder="Select Nepali Date"
                                       data-nepali-format="YYYY-MM-DD"
                                       data-ad-id="edit-boost-ad-date" required>
                                <input type="hidden" name="date" id="edit-boost-ad-date">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Amount Spent *</label>
                                <input type="number" id="edit-boost-amount" name="amount" step="0.01" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL</label>
                            <input type="url" id="edit-boost-url" name="url" placeholder="https://..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                            <textarea id="edit-boost-remarks" name="remarks" rows="2" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            
             <!-- Modal Footer -->
             <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button type="button" onclick="closeModal('edit-boost-modal')" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                <button type="submit" form="edit-boost-form" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg">Update Boost</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openEditBoostModal(boost) {
        console.log('Opening Edit Modal for Boost:', boost);
        try {
            document.getElementById('edit-boost-title').value = boost.title;
            document.getElementById('edit-boost-platform').value = boost.platform;
            document.getElementById('edit-boost-content-type').value = boost.boosted_content_type;
            document.getElementById('edit-boost-amount').value = boost.amount;
            
            // Handle Date Conversion for Display
            const adDateStr = boost.date.split('T')[0];
            document.getElementById('edit-boost-ad-date').value = adDateStr;
            
            // Convert AD string to Nepali BS
            const adDateObj = NepaliFunctions.ConvertToDateObject(adDateStr, "YYYY-MM-DD");
            const bsDateObj = NepaliFunctions.AD2BS(adDateObj);
            document.getElementById('edit-boost-bs-date').value = NepaliFunctions.ConvertDateFormat(bsDateObj, "YYYY-MM-DD");
            
            document.getElementById('edit-boost-url').value = boost.url || '';
            document.getElementById('edit-boost-remarks').value = boost.remarks || '';
            
            // Update form action
            document.getElementById('edit-boost-form').action = `/boosts/${boost.id}`;
            
            openModal('edit-boost-modal');
            if (typeof initializeNepaliDatePicker === 'function') initializeNepaliDatePicker();
        } catch (e) {
            console.error('Error opening boost edit modal:', e);
        }
    }
</script>
