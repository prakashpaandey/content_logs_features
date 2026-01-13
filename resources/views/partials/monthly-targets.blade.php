<div class="mt-8">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 gap-4 sm:gap-0">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Monthly Targets</h2>
        <div class="flex items-center space-x-2 w-full sm:w-auto">
            @if(!isset($selectedClient))
            <button onclick="openModal('bulk-target-modal')" 
                    class="flex-1 sm:flex-none justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center">
                <i class="fas fa-users-cog mr-2"></i>
                Set for All Clients
            </button>
            @endif
            <button onclick="openModal('history-modal')" 
                    class="flex-1 sm:flex-none justify-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center">
                <i class="fas fa-history mr-2"></i>
                View History
            </button>
            <button onclick="openModal('create-target-modal')" 
                    class="flex-1 sm:flex-none justify-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Create Monthly Target
            </button>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Month</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Target Posts</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Target Reels</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Boost Budget</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Created Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="targets-table-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @php
                        $statusColors = [
                            'active' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                            'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                            'archived' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                        ];
                        
                        $activeTargets = $displayedTargets->filter(fn($t) => !in_array($t->status, ['completed', 'archived']));
                    @endphp
                    
                    @forelse($activeTargets as $target)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                @php 
                                    $m = $target->bs_month ?? $dateHelpers->representativeAdToBs($target->month)['month'];
                                    $y = $target->bs_year ?? $dateHelpers->representativeAdToBs($target->month)['year'];
                                @endphp
                                {{ $nepaliTranslate($m, 'month') }} {{ $y }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $target->target_posts }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $target->target_reels }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">$ {{ number_format($target->target_boost_budget, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                @php $createdBs = $dateHelpers->adToBs($target->created_at); @endphp
                                {{ $nepaliTranslate($createdBs['month'], 'month') }} {{ $createdBs['year'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$target->status] }}">
                                    {{ ucfirst($target->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right space-x-2">
                                @php
                                    $target->actual_posts = $target->getActualPosts();
                                    $target->actual_reels = $target->getActualReels();
                                    $target->actual_boost_amount = $target->getActualBoostAmount();
                                @endphp
                                <button type="button" class="btn-view-target text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300" data-target='@json($target)'>
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn-edit-target text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300" data-target='@json($target)'>
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No active targets found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($activeTargets as $target)
                <div class="p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-bold text-gray-900 dark:text-white">
                            @php 
                                $m = $target->bs_month ?? $dateHelpers->representativeAdToBs($target->month)['month'];
                                $y = $target->bs_year ?? $dateHelpers->representativeAdToBs($target->month)['year'];
                            @endphp
                            {{ $nepaliTranslate($m, 'month') }} {{ $y }}
                        </div>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $statusColors[$target->status] }}">
                            {{ strtoupper($target->status) }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-2">
                        <div class="bg-gray-50 dark:bg-gray-700/30 p-2 rounded-lg text-center">
                            <div class="text-xs text-gray-400 uppercase font-black tracking-tighter mb-1">Posts</div>
                            <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $target->target_posts }}</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/30 p-2 rounded-lg text-center">
                            <div class="text-xs text-gray-400 uppercase font-black tracking-tighter mb-1">Reels</div>
                            <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $target->target_reels }}</div>
                        </div>
                        <div class="bg-primary-50 dark:bg-primary-900/10 p-2 rounded-lg text-center">
                            <div class="text-xs text-primary-400 uppercase font-black tracking-tighter mb-1">Budget</div>
                            <div class="text-sm font-bold text-primary-600 dark:text-primary-400">${{ number_format($target->target_boost_budget, 0) }}</div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700/50">
                        <div class="text-[10px] text-gray-400">
                            Created: {{ $nepaliTranslate($createdBs['month'], 'month') }} {{ $createdBs['year'] }}
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="button" class="btn-view-target p-2 text-primary-600 dark:text-primary-400" data-target='@json($target)'>
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                            <button type="button" class="btn-edit-target p-2 text-yellow-500" data-target='@json($target)'>
                                <i class="fas fa-edit text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-gray-500 dark:text-gray-400 text-sm">No active targets found.</div>
            @endforelse
        </div>
    </div>

</div>

<!-- Bulk Target Modal -->
<div id="bulk-target-modal" class="modal hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full mx-auto">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Set Target for All Clients</h3>
                    <button onclick="closeModal('bulk-target-modal')" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">This will update or create targets for all your clients.</p>
            </div>
            
            <div class="px-6 py-4">
                <form id="bulk-target-form" action="{{ route('monthly-targets.bulk') }}" method="POST" onsubmit="event.preventDefault(); submitFormAjax('bulk-target-form', 'bulk-target-modal')">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Month & Year *</label>
                            <x-nepali-month-picker 
                                id="bulk-target-picker" 
                                adInputId="bulk-target-ad-month" 
                                bsMonthInputId="bulk-target-bs-month" 
                                bsYearInputId="bulk-target-bs-year" 
                                value="{{ $bsYear . '-' . str_pad($bsMonth, 2, '0', STR_PAD_LEFT) }}"
                                placeholder="Select Month" />
                            <input type="hidden" name="month" id="bulk-target-ad-month" required>
                            <input type="hidden" name="bs_month" id="bulk-target-bs-month">
                            <input type="hidden" name="bs_year" id="bulk-target-bs-year">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Target Posts *</label>
                                <input type="number" name="target_posts" required min="0" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Target Reels *</label>
                                <input type="number" name="target_reels" required min="0" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Boost Budget ($) *</label>
                                <input type="number" name="target_boost_budget" required min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                            <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button type="button" onclick="closeModal('bulk-target-modal')" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Cancel</button>
                <button type="submit" form="bulk-target-form" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">Set All Targets</button>
            </div>
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
                <form id="create-target-form" action="{{ route('monthly-targets.store') }}" method="POST" onsubmit="event.preventDefault(); submitFormAjax('create-target-form', 'create-target-modal')">
                    @csrf
                    @if(isset($selectedClient))
                        <input type="hidden" name="client_id" value="{{ $selectedClient->id }}">
                    @endif
                    
                    <div class="space-y-4">
                        <div>
                            <label for="target-month" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Month & Year *
                            </label>
                            <x-nepali-month-picker id="create-target-picker" 
                                adInputId="create-target-ad-month" 
                                bsMonthInputId="create-target-bs-month" 
                                bsYearInputId="create-target-bs-year" 
                                value="{{ $bsYear . '-' . str_pad($bsMonth, 2, '0', STR_PAD_LEFT) }}"
                                placeholder="Select Month" />
                            <input type="hidden" name="month" id="create-target-ad-month" required>
                            <input type="hidden" name="bs_month" id="create-target-bs-month">
                            <input type="hidden" name="bs_year" id="create-target-bs-year">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="target-posts" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Target Posts *
                                </label>
                                <input type="number" name="target_posts" id="target-posts" required min="0"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                       placeholder="">
                            </div>
                            
                            <div>
                                <label for="target-reels" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Target Reels *
                                </label>
                                <input type="number" name="target_reels" id="target_reels" required min="0"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                       placeholder="">
                            </div>

                            <div>
                                <label for="target-boosts" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Boost Budget ($) *
                                </label>
                                <input type="number" name="target_boost_budget" id="target-boost-budget" required min="0" step="0.01"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                       placeholder="e.g. 50">
                            </div>
                        </div>
                        
                        <div>
                            <label for="target-notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Notes
                            </label>
                            <textarea name="notes" id="target-notes" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                                      placeholder="Add any additional notes or goals..."></textarea>
                        </div>
                        
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-900/50 rounded-lg p-3">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-yellow-500 dark:text-yellow-400 mr-2"></i>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                    Targets can be edited later.
                                </p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button type="button" onclick="closeModal('create-target-modal')"
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

<!-- Edit Target Modal -->
<div id="edit-target-modal" class="modal hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full mx-auto">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Monthly Target</h3>
                    <button onclick="closeModal('edit-target-modal')" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-4">
                <form id="edit-target-form" method="POST" onsubmit="event.preventDefault(); submitFormAjax('edit-target-form', 'edit-target-modal')">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Month & Year *</label>
                            <x-nepali-month-picker id="edit-target-picker" 
                                adInputId="edit-target-ad-month" 
                                bsMonthInputId="edit-target-bs-month" 
                                bsYearInputId="edit-target-bs-year" 
                                placeholder="Select Month" />
                            <input type="hidden" name="month" id="edit-target-ad-month">
                            <input type="hidden" name="bs_month" id="edit-target-bs-month">
                            <input type="hidden" name="bs_year" id="edit-target-bs-year">
                        </div>
                        
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Target Posts *
                                </label>
                                <input type="number" name="target_posts" id="edit-target-posts" required min="0"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Target Reels *
                                </label>
                                <input type="number" name="target_reels" id="edit-target-reels" required min="0"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Boost Budget ($) *
                                </label>
                                <input type="number" name="target_boost_budget" id="edit-target-boost-budget" required min="0" step="0.01"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Status
                            </label>
                            <select name="status" id="edit-target-status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                <option value="active">Active</option>
                                <option value="completed">Completed</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Notes
                            </label>
                            <textarea name="notes" id="edit-target-notes" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button type="button" onclick="closeModal('edit-target-modal')"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </button>
                <button type="submit" form="edit-target-form"
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Update Target
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Target Modal -->
<div id="view-target-modal" class="modal hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full mx-auto">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">View Monthly Target</h3>
                    <button onclick="closeModal('view-target-modal')" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-4">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Month & Year
                        </label>
                        <input type="text" id="view-target-bs-month" readonly disabled
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white cursor-not-allowed">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Target Posts
                            </label>
                            <input type="number" id="view-target-posts" readonly disabled
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white cursor-not-allowed">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Target Reels
                            </label>
                            <input type="number" id="view-target-reels" readonly disabled
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Boost Budget
                            </label>
                            <input type="text" id="view-target-boost-budget" readonly disabled
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white cursor-not-allowed">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Status
                        </label>
                        <input type="text" id="view-target-status" readonly disabled
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white cursor-not-allowed capitalize">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Notes
                        </label>
                        <textarea id="view-target-notes" rows="3" readonly disabled
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white cursor-not-allowed"></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button type="button" onclick="closeModal('view-target-modal')"
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- History Modal -->
<div id="history-modal" class="modal hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full mx-auto">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Target History</h3>
                    <button onclick="closeModal('history-modal')" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-4">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Month</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Target (Posts/Reels/Budget)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actual (Posts/Reels/Spent)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Completion Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @php
                                $historyTargets = $allTargets->filter(function($target) {
                                    return in_array($target->status, ['completed', 'archived']);
                                });
                            @endphp
                            
                            @if($historyTargets->count() > 0)
                                @foreach($historyTargets as $target)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            @php 
                                                $m = $target->bs_month ?? $dateHelpers->representativeAdToBs($target->month)['month'];
                                                $y = $target->bs_year ?? $dateHelpers->representativeAdToBs($target->month)['year'];
                                            @endphp
                                            {{ $nepaliTranslate($m, 'month') }} {{ $y }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $target->target_posts }} / {{ $target->target_reels }} / $ {{ number_format($target->target_boost_budget ?? 0, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $target->getActualPosts() }} / {{ $target->getActualReels() }} / $ {{ number_format($target->getActualBoostAmount(), 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$target->status] }}">
                                                {{ ucfirst($target->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            @php $updatedBs = $dateHelpers->adToBs($target->updated_at); @endphp
                                            {{ $nepaliTranslate($updatedBs['month'], 'month') }} {{ $updatedBs['year'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @if($target->status === 'archived')
                                                <form id="restore-target-form-{{ $target->id }}" action="{{ route('monthly-targets.update', $target->id) }}" method="POST" class="inline-block" onsubmit="event.preventDefault(); submitFormAjax('restore-target-form-{{ $target->id }}', 'history-modal')">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="month" value="{{ date('Y-m', strtotime($target->month)) }}">
                                                    <input type="hidden" name="bs_month" value="{{ $target->bs_month }}">
                                                    <input type="hidden" name="bs_year" value="{{ $target->bs_year }}">
                                                    <input type="hidden" name="target_posts" value="{{ $target->target_posts }}">
                                                    <input type="hidden" name="target_reels" value="{{ $target->target_reels }}">
                                                    <input type="hidden" name="target_boost_budget" value="{{ $target->target_boost_budget }}">
                                                    <input type="hidden" name="status" value="active">
                                                    <button type="submit" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="Restore to Active">
                                                        <i class="fas fa-undo-alt mr-1"></i> Restore
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                        No completed or archived targets found.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                <button type="button" onclick="closeModal('history-modal')"
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const monthNames = [
        'Baisakh', 'Jestha', 'Asar', 'Shrawan', 'Bhadra', 'Ashwin',
        'Kartik', 'Mangsir', 'Poush', 'Magh', 'Falgun', 'Chaitra'
    ];

    // Event delegation for view and edit buttons
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Monthly targets: DOMContentLoaded - Setting up event listeners');
        
        // View target buttons
        document.addEventListener('click', function(e) {
            const viewBtn = e.target.closest('.btn-view-target');
            if (viewBtn) {
                console.log('View button clicked');
                e.preventDefault();
                const target = JSON.parse(viewBtn.getAttribute('data-target'));
                openViewTargetModal(target);
            }
            
            const editBtn = e.target.closest('.btn-edit-target');
            if (editBtn) {
                console.log('Edit button clicked!');
                e.preventDefault();
                try {
                    const targetData = editBtn.getAttribute('data-target');
                    console.log('Raw target data:', targetData);
                    const target = JSON.parse(targetData);
                    console.log('Parsed target:', target);
                    openEditTargetModal(target);
                } catch (e) {
                    console.error('Error parsing target data:', e);
                    alert('Error: Could not parse target data');
                }
            }
        });
        
        console.log('Monthly targets: Event listeners set up complete');
    });

    function openEditTargetModal(target) {
        try {
            // Handle Date Conversion for Display
            const adStr = target.month.substring(0, 7); // Format 2024-03
            document.getElementById('edit-target-ad-month').value = adStr;
            
            // Convert AD string to Nepali object for display
            if (typeof NepaliFunctions === 'undefined') {
                alert('Error: NepaliFunctions library not loaded. Please refresh the page.');
                return;
            }
            
            const adObj = NepaliFunctions.ConvertToDateObject(adStr + "-01", "YYYY-MM-DD");
            
            // Representative Conversion: 
            // Apr(4) to Dec(12) -> BS Month = Month - 3, BS Year = Year + 57
            // Jan(1) to Mar(3) -> BS Month = Month + 9, BS Year = Year + 56
            let bsMonth, bsYear;
            if (adObj.month >= 4) {
                bsMonth = adObj.month - 3;
                bsYear = adObj.year + 57;
            } else {
                bsMonth = adObj.month + 9;
                bsYear = adObj.year + 56;
            }
            
            // Dispatch event to the custom Alpine picker
            window.dispatchEvent(new CustomEvent('set-month', { 
                detail: { 
                    targetId: 'edit-target-picker', 
                    year: bsYear, 
                    month: bsMonth 
                } 
            }));

            document.getElementById('edit-target-posts').value = target.target_posts;
            document.getElementById('edit-target-reels').value = target.target_reels;
            document.getElementById('edit-target-boost-budget').value = target.target_boost_budget || 0;
            document.getElementById('edit-target-status').value = target.status;
            document.getElementById('edit-target-notes').value = target.notes || '';
            
            // Disable 'Completed' option if targets not met
            const statusSelect = document.getElementById('edit-target-status');
            const completedOption = statusSelect.querySelector('option[value="completed"]');
            
            if (completedOption) {
                if (target.actual_posts < target.target_posts || target.actual_reels < target.target_reels || (target.actual_boost_amount || 0) < target.target_boost_budget) {
                    completedOption.disabled = true;
                    completedOption.textContent = "Completed (Targets not met)";
                } else {
                    completedOption.disabled = false;
                    completedOption.textContent = "Completed";
                }
            }
            
            // Update form action
            document.getElementById('edit-target-form').action = `/monthly-targets/${target.id}`;
            
            if (typeof openModal === 'function') {
                openModal('edit-target-modal');
            } else {
                alert('Error: openModal function not found. Please refresh the page.');
            }
        } catch (e) {
            console.error('Error opening edit modal:', e);
            alert('Error opening edit modal: ' + e.message);
        }
    }
    
    function openViewTargetModal(target) {
        console.log('Opening View Modal for Target:', target);
        try {
            // Convert to BS for display
            const adDateStr = target.month.substring(0, 10);
            const adObj = NepaliFunctions.ConvertToDateObject(adDateStr, "YYYY-MM-DD");
            
            // Representative Conversion
            let bsMonth, bsYear;
            if (adObj.month >= 4) {
                bsMonth = adObj.month - 3;
                bsYear = adObj.year + 57;
            } else {
                bsMonth = adObj.month + 9;
                bsYear = adObj.year + 56;
            }
            
            const bsMonthName = monthNames[(target.bs_month || bsMonth) - 1] || '';
            const displayBsYear = target.bs_year || bsYear;
            
            document.getElementById('view-target-bs-month').value = bsMonthName + " " + displayBsYear;

            document.getElementById('view-target-posts').value = target.target_posts;
            document.getElementById('view-target-reels').value = target.target_reels;
            document.getElementById('view-target-posts').value = target.target_posts;
            document.getElementById('view-target-reels').value = target.target_reels;
            document.getElementById('view-target-boost-budget').value = "$ " + (target.target_boost_budget || 0);
            document.getElementById('view-target-status').value = target.status;
            document.getElementById('view-target-notes').value = target.notes || 'No notes available.';
            
            openModal('view-target-modal');
        } catch (e) {
            console.error('Error opening view modal:', e);
        }
    }
</script>