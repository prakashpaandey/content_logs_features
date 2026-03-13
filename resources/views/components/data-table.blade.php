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

@can('contents.update')
{{-- The edit content modal logic is now handled in partials/content-modals.blade.php --}}
@endcan