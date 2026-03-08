@php
    $platformColors = [
        'Instagram' => 'bg-pink-100 text-pink-800 dark:bg-pink-900/30 dark:text-pink-300',
        'Facebook' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
        'TikTok' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    ];
    
    $typeColors = [
        'Post' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
        'Reel' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
        'Boost' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
    ];
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <!-- Table -->
    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Platform</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Title</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">URL</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($contentData as $content)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" title="Added by: {{ $content->user->name ?? 'Unknown' }}">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            @php 
                                $contentDate = \Carbon\Carbon::parse($content->date);
                                $bsDate = $dateHelpers->adToBs($contentDate);
                            @endphp
                            {{ $nepaliTranslate($bsDate['month'], 'month') }} {{ $bsDate['day'] }}, {{ $bsDate['year'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $typeColors[$content->type] ?? 'bg-gray-100' }}">
                                {{ ucfirst($content->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-wrap gap-1">
                                @foreach((array)$content->platform as $plt)
                                    <span class="px-2 py-1 text-[10px] font-bold rounded-full {{ $platformColors[$plt] ?? 'bg-gray-100' }}">
                                        {{ $plt }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white truncate max-w-xs">
                            {{ $content->title }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @php
                                $urls = [];
                                $platformIcons = [
                                    'Facebook' => 'fab fa-facebook-f',
                                    'Instagram' => 'fab fa-instagram',
                                    'TikTok' => 'fab fa-tiktok',
                                ];
                                $platformColors = [
                                    'Facebook' => 'text-blue-600 hover:text-blue-800',
                                    'Instagram' => 'text-pink-600 hover:text-pink-800',
                                    'TikTok' => 'text-gray-900 hover:text-black dark:text-white dark:hover:text-gray-300',
                                ];
                                
                                // Handle JSON format (new) and string format (legacy)
                                if ($content->url) {
                                    if (is_array($content->url)) {
                                        $urls = $content->url;
                                    } else {
                                        // Try to decode if it's a JSON string
                                        $decoded = json_decode($content->url, true);
                                        if (is_array($decoded)) {
                                            $urls = $decoded;
                                        } else if (is_string($content->url)) {
                                            // Legacy single URL format
                                            $urls = ['Link' => $content->url];
                                        }
                                    }
                                }
                            @endphp
                            
                            @if(!empty($urls))
                                <div class="flex items-center gap-3">
                                    @foreach($urls as $platform => $url)
                                        @if($url)
                                            <a href="{{ $url }}" target="_blank" 
                                               class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 {{ $platformColors[$platform] ?? 'text-gray-600 hover:text-gray-800' }} transition-colors"
                                               title="{{ $platform }}">
                                                <i class="{{ $platformIcons[$platform] ?? 'fas fa-link' }} text-sm"></i>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right space-x-2">
                            @php $bsDateStr = $bsDate['year'] . '-' . sprintf('%02d', $bsDate['month']) . '-' . sprintf('%02d', $bsDate['day']); @endphp
                            
                            @php $isOwnerOrAdmin = auth()->user()->isAdmin() || $content->user_id === auth()->id(); @endphp

                            @if($isOwnerOrAdmin)
                                @can('contents.update')
                                <button onclick='openEditContentModal(@json($content), "{{ $bsDateStr }}")' class="text-primary-600 hover:text-primary-900 dark:text-primary-400"><i class="fas fa-edit"></i></button>
                                @endcan

                                @can('contents.delete')
                                <button onclick="openDeleteModal('{{ route('contents.destroy', $content->id) }}', '{{ addslashes($content->title) }}')" class="text-red-600 hover:text-red-900 dark:text-red-400"><i class="fas fa-trash"></i></button>
                                @endcan
                            @else
                                <span class="text-gray-400 italic text-xs">ReadOnly</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No content found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden divide-y divide-gray-200 dark:divide-gray-700">
        @forelse($contentData as $content)
            <div class="p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex flex-wrap gap-1">
                            @foreach((array)$content->platform as $plt)
                                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $platformColors[$plt] ?? 'bg-gray-100' }}">
                                    {{ strtoupper($plt) }}
                                </span>
                            @endforeach
                        </div>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full {{ $typeColors[$content->type] ?? 'bg-gray-100' }}">
                            {{ strtoupper($content->type) }}
                        </span>
                    </div>
                    <div class="text-[10px] text-gray-400 font-medium">
                        @php 
                            $contentDate = \Carbon\Carbon::parse($content->date);
                            $bsDate = $dateHelpers->adToBs($contentDate);
                        @endphp
                        {{ $nepaliTranslate($bsDate['month'], 'month') }} {{ $bsDate['day'] }}
                    </div>
                </div>
                <div class="text-sm font-bold text-gray-900 dark:text-white leading-tight">
                    {{ $content->title }}
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700/50">
                    <div class="flex items-center gap-3">
                        @php
                            $urls = [];
                            $platformIcons = [
                                'Facebook' => 'fab fa-facebook-f',
                                'Instagram' => 'fab fa-instagram',
                                'TikTok' => 'fab fa-tiktok',
                            ];
                            $platformColors = [
                                'Facebook' => 'text-blue-600 hover:text-blue-800',
                                'Instagram' => 'text-pink-600 hover:text-pink-800',
                                'TikTok' => 'text-gray-900 hover:text-black dark:text-white dark:hover:text-gray-300',
                            ];
                            
                            // Handle JSON format (new) and string format (legacy)
                            if ($content->url) {
                                if (is_array($content->url)) {
                                    $urls = $content->url;
                                } else {
                                    // Try to decode if it's a JSON string
                                    $decoded = json_decode($content->url, true);
                                    if (is_array($decoded)) {
                                        $urls = $decoded;
                                    } else if (is_string($content->url)) {
                                        // Legacy single URL format
                                        $urls = ['Link' => $content->url];
                                    }
                                }
                            }
                        @endphp
                        
                        @if(!empty($urls))
                            @foreach($urls as $platform => $url)
                                @if($url)
                                    <a href="{{ $url }}" target="_blank" 
                                       class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-700 {{ $platformColors[$platform] ?? 'text-gray-600 hover:text-gray-800' }} transition-colors text-xs"
                                       title="{{ $platform }}">
                                        <i class="{{ $platformIcons[$platform] ?? 'fas fa-link' }}"></i>
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        @php $bsDateStr = $bsDate['year'] . '-' . sprintf('%02d', $bsDate['month']) . '-' . sprintf('%02d', $bsDate['day']); @endphp
                        @php $isOwnerOrAdmin = auth()->user()->isAdmin() || $content->user_id === auth()->id(); @endphp

                        @if($isOwnerOrAdmin)
                            @can('contents.update')
                            <button onclick='openEditContentModal(@json($content), "{{ $bsDateStr }}")' class="p-2 text-primary-600 dark:text-primary-400"><i class="fas fa-edit"></i></button>
                            @endcan

                            @can('contents.delete')
                            <button onclick="openDeleteModal('{{ route('contents.destroy', $content->id) }}', '{{ addslashes($content->title) }}')" class="p-2 text-red-500"><i class="fas fa-trash"></i></button>
                            @endcan
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-6 text-center text-gray-500 dark:text-gray-400 text-sm">No content found.</div>
        @endforelse
    </div>

    
    <!-- Empty State (Only show if truly empty and no filter? Logic handled by controller, here shows empty row above) -->
    
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        {{ $contentData->links() }}
    </div>
</div>

<!-- Add Content Modal -->
<div id="add-content-modal" class="modal hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full mx-auto">
             <!-- Modal Header -->
             <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Add New Content</h3>
                    <button onclick="closeModal('add-content-modal')" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-4">
                <form id="add-content-form" action="{{ route('contents.store') }}" method="POST" onsubmit="event.preventDefault(); submitFormAjax('add-content-form', 'add-content-modal')">
                    @csrf
                    @if(isset($selectedClient))
                        <input type="hidden" name="client_id" value="{{ $selectedClient->id }}">
                    @endif
                    <input type="hidden" name="context_bs_month" value="{{ $bsMonth }}">
                    <input type="hidden" name="context_bs_year" value="{{ $bsYear }}">
                    <input type="hidden" name="manual_bs_date" id="add-content-manual-bs-date">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title *</label>
                            <input type="text" name="title" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Platforms *</label>
                                <div class="flex flex-wrap gap-4 mt-2">
                                    <label class="flex items-center space-x-2 cursor-pointer group">
                                        <input type="checkbox" name="platform[]" value="Facebook" class="add-platform-checkbox w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 bg-white dark:bg-gray-700 dark:border-gray-600" onchange="updateUrlFields('add-content')">
                                        <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-primary-600 transition-colors">Facebook</span>
                                    </label>
                                    <label class="flex items-center space-x-2 cursor-pointer group">
                                        <input type="checkbox" name="platform[]" value="Instagram" class="add-platform-checkbox w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 bg-white dark:bg-gray-700 dark:border-gray-600" onchange="updateUrlFields('add-content')">
                                        <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-primary-600 transition-colors">Instagram</span>
                                    </label>
                                    <label class="flex items-center space-x-2 cursor-pointer group">
                                        <input type="checkbox" name="platform[]" value="TikTok" class="add-platform-checkbox w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 bg-white dark:bg-gray-700 dark:border-gray-600" onchange="updateUrlFields('add-content')">
                                        <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-primary-600 transition-colors">TikTok</span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type *</label>
                                <select name="type" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="Post">Post</option>
                                    <option value="Reel">Reel</option>
                                </select>
                            </div>
                        </div>

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
                            <input type="text" id="add-content-bs-date" name="bs_date" class="nepali-datepicker w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" 
                                   placeholder="Select Nepali Date"
                                   data-ad-id="add-content-ad-date" 
                                   data-nepali-format="YYYY-MM-DD"
                                   value="{{ $defaultBsDate }}"
                                   required>
                            <input type="hidden" name="date" id="add-content-ad-date" value="{{ $defaultAdDate }}">
                        </div>

                        <div id="add-content-urls-container">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">URLs</label>
                            <div id="add-content-url-fields" class="space-y-2">
                                <!-- Dynamic URL fields will be inserted here -->
                            </div>
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
                <button type="button" onclick="closeModal('add-content-modal')" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                <button type="submit" form="add-content-form" onclick="document.getElementById('add-content-manual-bs-date').value = document.getElementById('add-content-bs-date').value" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg">Add Content</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Content Modal -->
<div id="edit-content-modal" class="modal hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="modal-overlay absolute inset-0 bg-black opacity-50"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full mx-auto">
             <!-- Modal Header -->
             <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Content</h3>
                    <button onclick="closeModal('edit-content-modal')" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-4">
                <form id="edit-content-form" method="POST" onsubmit="event.preventDefault(); submitFormAjax('edit-content-form', 'edit-content-modal')">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="manual_bs_date" id="edit-content-manual-bs-date">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title *</label>
                            <input type="text" id="edit-content-title" name="title" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Platforms *</label>
                                <div class="flex flex-wrap gap-4 mt-2">
                                    <label class="flex items-center space-x-2 cursor-pointer group">
                                        <input type="checkbox" name="platform[]" value="Facebook" class="edit-platform-checkbox w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 bg-white dark:bg-gray-700 dark:border-gray-600" onchange="updateUrlFields('edit-content')">
                                        <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-primary-600 transition-colors">Facebook</span>
                                    </label>
                                    <label class="flex items-center space-x-2 cursor-pointer group">
                                        <input type="checkbox" name="platform[]" value="Instagram" class="edit-platform-checkbox w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 bg-white dark:bg-gray-700 dark:border-gray-600" onchange="updateUrlFields('edit-content')">
                                        <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-primary-600 transition-colors">Instagram</span>
                                    </label>
                                    <label class="flex items-center space-x-2 cursor-pointer group">
                                        <input type="checkbox" name="platform[]" value="TikTok" class="edit-platform-checkbox w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 bg-white dark:bg-gray-700 dark:border-gray-600" onchange="updateUrlFields('edit-content')">
                                        <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-primary-600 transition-colors">TikTok</span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type *</label>
                                <select id="edit-content-type" name="type" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="Post">Post</option>
                                    <option value="Reel">Reel</option>
                                    <option value="Boost">Boost</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date *</label>
                            <input type="text" id="edit-content-bs-date" name="bs_date" class="nepali-datepicker w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" 
                                   placeholder="Select Nepali Date"
                                   data-nepali-format="YYYY-MM-DD"
                                   data-ad-id="edit-content-ad-date" required>
                            <input type="hidden" name="date" id="edit-content-ad-date">
                        </div>

                        <div id="edit-content-urls-container">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">URLs</label>
                            <div id="edit-content-url-fields" class="space-y-2">
                                <!-- Dynamic URL fields will be inserted here -->
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                            <textarea id="edit-content-remarks" name="remarks" rows="2" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            
             <!-- Modal Footer -->
             <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                <button type="button" onclick="closeModal('edit-content-modal')" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                <button type="submit" form="edit-content-form" onclick="document.getElementById('edit-content-manual-bs-date').value = document.getElementById('edit-content-bs-date').value" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg">Update Content</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openEditContentModal(content, bsDateStr) {
        console.log('Opening Edit Modal for Content:', content);
        try {
            document.getElementById('edit-content-title').value = content.title;
            
            // Handle Platform Checkboxes
            const platforms = Array.isArray(content.platform) ? content.platform : [content.platform];
            document.querySelectorAll('.edit-platform-checkbox').forEach(cb => {
                cb.checked = platforms.includes(cb.value);
            });
            
            // Trigger URL field update for edit modal
            updateUrlFields('edit-content');
            
            // Populate platform URLs after fields are created
            setTimeout(() => {
                let urlObj = null;
                
                // Handle different URL formats
                if (content.url) {
                    if (typeof content.url === 'object') {
                        urlObj = content.url;
                    } else if (typeof content.url === 'string') {
                        try {
                            urlObj = JSON.parse(content.url);
                        } catch (e) {
                            // If it's not JSON, skip parsing
                            urlObj = null;
                        }
                    }
                }
                
                if (urlObj && typeof urlObj === 'object') {
                    Object.entries(urlObj).forEach(([platform, url]) => {
                        const urlInput = document.querySelector(`#edit-content-url-fields input[name="platform_urls[${platform}]"]`);
                        if (urlInput && url) {
                            urlInput.value = url;
                        }
                    });
                }
            }, 50);
            
            // Handle Date Conversion for Display
            const adDateStr = content.date.split('T')[0];
            document.getElementById('edit-content-ad-date').value = adDateStr;
            
            // Use provided BS Date string
            document.getElementById('edit-content-bs-date').value = bsDateStr;
            
            document.getElementById('edit-content-type').value = content.type;
            document.getElementById('edit-content-remarks').value = content.remarks || '';
            
            // Update form action
            document.getElementById('edit-content-form').action = `/contents/${content.id}`;
            
            openModal('edit-content-modal');
            if (typeof initializeNepaliDatePicker === 'function') initializeNepaliDatePicker();
        } catch (e) {
            console.error('Error opening content edit modal:', e);
        }
    }

    // Dynamic URL Fields Handler
    function updateUrlFields(formType) {
        const containerSelector = `#${formType}-urls-container`;
        const fieldsSelector = `#${formType}-url-fields`;
        const container = document.querySelector(containerSelector);
        const fieldsContainer = document.querySelector(fieldsSelector);
        
        if (!container || !fieldsContainer) {
            console.error(`Container or fieldsContainer not found for ${formType}`);
            return;
        }

        // Get selected platforms
        const platformCheckboxes = document.querySelectorAll(`#${formType}-form input[name="platform[]"]`);
        const selectedPlatforms = Array.from(platformCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        console.log(`Selected platforms for ${formType}:`, selectedPlatforms);

        // Clear existing URL fields
        fieldsContainer.innerHTML = '';

        if (selectedPlatforms.length === 0) {
            container.style.display = 'none';
            return;
        }

        container.style.display = 'block';

        // Create URL field for each selected platform
        selectedPlatforms.forEach(platform => {
            const fieldName = `platform_urls[${platform}]`;
            const urlField = document.createElement('div');
            urlField.className = 'flex items-center gap-2';
            urlField.innerHTML = `
                <label class="text-xs font-medium text-gray-600 dark:text-gray-400 min-w-20">${platform}:</label>
                <input 
                    type="url" 
                    name="${fieldName}" 
                    placeholder="https://..." 
                    class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm"
                >
            `;
            fieldsContainer.appendChild(urlField);
        });
    }

</script>