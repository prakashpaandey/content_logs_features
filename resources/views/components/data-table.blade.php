@php
    $contentData = [
        [
            'date' => '2024-03-15',
            'type' => 'post',
            'platform' => 'Instagram',
            'title' => 'Product Launch Announcement',
            'url' => 'https://instagram.com/p/xyz123',
            'remarks' => 'High engagement, 500+ likes'
        ],
        [
            'date' => '2024-03-14',
            'type' => 'reel',
            'platform' => 'Instagram',
            'title' => 'Behind the Scenes',
            'url' => 'https://instagram.com/reel/abc456',
            'remarks' => 'Viral, 10K+ views'
        ],
        [
            'date' => '2024-03-13',
            'type' => 'post',
            'platform' => 'Facebook',
            'title' => 'Weekly Update',
            'url' => 'https://facebook.com/post/def789',
            'remarks' => 'Good discussion in comments'
        ],
        [
            'date' => '2024-03-12',
            'type' => 'reel',
            'platform' => 'TikTok',
            'title' => 'Tutorial Video',
            'url' => 'https://tiktok.com/@user/video/ghi012',
            'remarks' => '1M+ views, trending'
        ],
        [
            'date' => '2024-03-11',
            'type' => 'post',
            'platform' => 'LinkedIn',
            'title' => 'Industry Insights',
            'url' => 'https://linkedin.com/post/jkl345',
            'remarks' => '200+ professional reactions'
        ],
        [
            'date' => '2024-03-10',
            'type' => 'post',
            'platform' => 'Twitter',
            'title' => 'News Update',
            'url' => 'https://twitter.com/tweet/mno678',
            'remarks' => '150 retweets'
        ],
        [
            'date' => '2024-03-09',
            'type' => 'reel',
            'platform' => 'Instagram',
            'title' => 'Customer Testimonial',
            'url' => 'https://instagram.com/reel/pqr901',
            'remarks' => 'Great conversion rate'
        ],
        [
            'date' => '2024-03-08',
            'type' => 'post',
            'platform' => 'Facebook',
            'title' => 'Event Promotion',
            'url' => 'https://facebook.com/post/stu234',
            'remarks' => '200+ event responses'
        ],
    ];
    
    $platformColors = [
        'Instagram' => 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200',
        'Facebook' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'TikTok' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
        'LinkedIn' => 'bg-blue-50 text-blue-700 dark:bg-blue-800 dark:text-blue-100',
        'Twitter' => 'bg-sky-100 text-sky-800 dark:bg-sky-900 dark:text-sky-200',
    ];
    
    $typeColors = [
        'post' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
        'reel' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    ];
@endphp

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
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
                        Remarks
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($contentData as $content)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            {{ \Carbon\Carbon::parse($content['date'])->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $typeColors[$content['type']] }}">
                                {{ ucfirst($content['type']) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $platformColors[$content['platform']] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                {{ $content['platform'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ $content['title'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ $content['url'] }}" target="_blank" 
                               class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300 flex items-center">
                                <i class="fas fa-external-link-alt mr-1 text-xs"></i>
                                View
                            </a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                            {{ $content['remarks'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right space-x-2">
                            <button class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Empty State -->
    <div id="empty-table-state" class="hidden p-8 text-center">
        <div class="mx-auto w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
            <i class="fas fa-file-alt text-gray-400 text-xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No content yet</h3>
        <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">Add your first social media content</p>
        <button class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Add Content
        </button>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700 dark:text-gray-300">
                Showing <span class="font-medium">1</span> to <span class="font-medium">8</span> of <span class="font-medium">50</span> entries
            </div>
            <div class="flex space-x-2">
                <button onclick="goToPage(1)" 
                        class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fas fa-chevron-left"></i>
                </button>
                
                @for($i = 1; $i <= 5; $i++)
                    <button onclick="goToPage({{ $i }})" 
                            class="page-button px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg {{ $i === 1 ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300' }} hover:bg-gray-50 dark:hover:bg-gray-700">
                        {{ $i }}
                    </button>
                @endfor
                
                <button onclick="goToPage(2)" 
                        class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>