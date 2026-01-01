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
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Type
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Platform
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Title
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
                @if($contentData->count() > 0)
                    @foreach($contentData as $content)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                @php 
                                    $contentDate = \Carbon\Carbon::parse($content->date);
                                    $bsDate = $dateHelpers->adToBs($contentDate);
                                @endphp
                                {{ $nepaliTranslate($bsDate['month'], 'month') }} {{ $contentDate->format('d') }}, {{ $bsDate['year'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $typeColors[$content->type] ?? 'bg-gray-100' }}">
                                    {{ ucfirst($content->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $platformColors[$content->platform] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ $content->platform }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $content->title }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($content->url)
                                    <a href="{{ $content->url }}" target="_blank" 
                                       class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 flex items-center">
                                        <i class="fas fa-external-link-alt mr-1 text-xs"></i>
                                        View
                                    </a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right space-x-2">
                                <button onclick='openEditContentModal(@json($content))' class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="openDeleteModal('{{ route('contents.destroy', $content->id) }}')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            No content found. Add some content to get started.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
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
                <form id="add-content-form" action="{{ route('contents.store') }}" method="POST">
                    @csrf
                    @if(isset($selectedClient))
                        <input type="hidden" name="client_id" value="{{ $selectedClient->id }}">
                    @endif
                    
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
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type *</label>
                                <select name="type" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="Post">Post</option>
                                    <option value="Reel">Reel</option>
                                    <option value="Boost">Boost</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date *</label>
                            <input type="text" id="add-content-bs-date" class="nepali-datepicker w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" 
                                   placeholder="Select Nepali Date"
                                   data-ad-id="add-content-ad-date" required>
                            <input type="hidden" name="date" id="add-content-ad-date" 
                                   value="{{ $dateContext->isCurrentMonth() ? date('Y-m-d') : $dateContext->format('Y-m-d') }}">
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
                <button type="button" onclick="closeModal('add-content-modal')" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                <button type="submit" form="add-content-form" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg">Add Content</button>
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
                <form id="edit-content-form" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title *</label>
                            <input type="text" id="edit-content-title" name="title" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Platform *</label>
                                <select id="edit-content-platform" name="platform" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="Instagram">Instagram</option>
                                    <option value="Facebook">Facebook</option>
                                    <option value="TikTok">TikTok</option>
                                </select>
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
                            <input type="text" id="edit-content-bs-date" class="nepali-datepicker w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white" 
                                   placeholder="Select Nepali Date"
                                   data-ad-id="edit-content-ad-date" required>
                            <input type="hidden" name="date" id="edit-content-ad-date">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL</label>
                            <input type="url" id="edit-content-url" name="url" placeholder="https://..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
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
                <button type="submit" form="edit-content-form" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg">Update Content</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openEditContentModal(content) {
        console.log('Opening Edit Modal for Content:', content);
        try {
            document.getElementById('edit-content-title').value = content.title;
            document.getElementById('edit-content-platform').value = content.platform;
            document.getElementById('edit-content-type').value = content.type;
            
            // Handle Date Conversion for Display
            const adDateStr = content.date.split('T')[0];
            document.getElementById('edit-content-ad-date').value = adDateStr;
            
            // Convert AD string to Nepali BS
            const adDateObj = NepaliFunctions.ConvertToDateObject(adDateStr, "YYYY-MM-DD");
            const bsDateObj = NepaliFunctions.AD2BS(adDateObj);
            document.getElementById('edit-content-bs-date').value = NepaliFunctions.ConvertDateFormat(bsDateObj, "YYYY-MM-DD");
            
            document.getElementById('edit-content-url').value = content.url || '';
            document.getElementById('edit-content-remarks').value = content.remarks || '';
            
            // Update form action
            document.getElementById('edit-content-form').action = `/contents/${content.id}`;
            
            openModal('edit-content-modal');
            if (typeof initializeNepaliDatePicker === 'function') initializeNepaliDatePicker();
        } catch (e) {
            console.error('Error opening content edit modal:', e);
        }
    }
</script>