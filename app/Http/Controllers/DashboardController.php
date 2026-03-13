<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Content;
use App\Models\MonthlyTarget;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $hasPermission = Gate::allows('clients.view');
        $clients = $hasPermission ? Client::orderBy('updated_at', 'desc')->get() : collect();
        $user = auth()->user();
        
        $selectedClientId = $request->query('client_id');
        $selectedClient = $selectedClientId ? $clients->where('id', $selectedClientId)->first() : null;
        
        // If no client is selected, show the simplified Agency Home Dashboard
        if (!$selectedClient) {
            return view('dashboard.home', compact('clients'));
        }

        $metrics = [
            'total_posts' => 0,
            'total_reels' => 0,
            'total_boosts' => 0,
            'posts_growth' => 0,
            'reels_growth' => 0,
            'boosts_growth' => 0,
            'target_completion' => 0,
            'target_growth' => 0,
            'variance' => 0,
            'variance_growth' => 0,
            'total_boost_amount' => 0,
            'boost_amount_growth' => 0
        ];
        
        $contentData = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        $boostData = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        $targets = collect([]);
        $charts = [];

        $currentTarget = null;

        $previousMonth = null;
        $hasPreviousData = false;
        $dateContext = Carbon::now();
        $allTargets = collect([]);
        $displayedTargets = collect([]);

        $bsMonth = (int)$request->query('month');
        $bsYear = (int)$request->query('year');
        
        if (!$bsYear || $bsYear < 2050) {
            $todayBs = \App\Helpers\NepaliDateHelper::adToBs(now());
            $bsMonth = $bsMonth ?: $todayBs['month'];
            $bsYear = $bsYear ?: $todayBs['year'];
        }

        // Get the AD range for this BS month
        [$startDate, $endDate] = \App\Helpers\NepaliDateHelper::getBsMonthRange($bsMonth, $bsYear);
        
        // For context and navigation, we still need a single AD date that "represents" this BS month
        $repAd = \App\Helpers\NepaliDateHelper::bsToAd($bsMonth, $bsYear);
        $dateContext = Carbon::createFromDate($repAd['year'], $repAd['month'], 1);
        $now = $dateContext;

        if ($selectedClient) {
            // Metrics (Current Month or Selected Month)
            
            // Previous Month Range for Growth Calculation
            $prevBsMonth = $bsMonth - 1;
            $prevBsYear = $bsYear;
            if ($prevBsMonth < 1) {
                $prevBsMonth = 12;
                $prevBsYear--;
            }
            [$prevStartDate, $prevEndDate] = \App\Helpers\NepaliDateHelper::getBsMonthRange($prevBsMonth, $prevBsYear);

            // Previous Month Representation for Target Lookup
            $prevRepAd = \App\Helpers\NepaliDateHelper::bsToAd($prevBsMonth, $prevBsYear);
            $lastMonth = Carbon::createFromDate($prevRepAd['year'], $prevRepAd['month'], 1);
            $previousMonth = $lastMonth;
            
            // Permissions checks
            $canViewContent = Gate::allows('contents.view');
            $canViewBoosts = Gate::allows('boosts.view');
            $canViewTargets = Gate::allows('targets.view');

            if ($canViewContent) {
                $currentMonthContents = $selectedClient->contents()
                    ->with('user')
                    ->whereBetween('date', [$startDate, $endDate])
                    ->get();
                    
                $lastMonthContents = $selectedClient->contents()
                    ->whereBetween('date', [$prevStartDate, $prevEndDate])
                    ->get();

                $metrics['total_posts'] = $currentMonthContents->where('type', 'Post')->count();
                $metrics['total_reels'] = $currentMonthContents->where('type', 'Reel')->count();
                
                $lastPosts = $lastMonthContents->where('type', 'Post')->count();
                $lastReels = $lastMonthContents->where('type', 'Reel')->count();
                
                $metrics['posts_growth'] = $lastPosts > 0 ? round((($metrics['total_posts'] - $lastPosts) / $lastPosts) * 100) : 0;
                $metrics['reels_growth'] = $lastReels > 0 ? round((($metrics['total_reels'] - $lastReels) / $lastReels) * 100) : 0;

                $contentData = $selectedClient->contents()
                    ->whereBetween('date', [$startDate, $endDate])
                    ->orderBy('date', 'desc')
                    ->orderBy('id', 'desc')
                    ->paginate(10, ['*'], 'content_page')
                    ->withQueryString();
            }

            if ($canViewBoosts) {
                $currentMonthBoosts = $selectedClient->boosts()
                    ->with('user')
                    ->whereBetween('date', [$startDate, $endDate])
                    ->get();

                $lastMonthBoosts = $selectedClient->boosts()
                    ->whereBetween('date', [$prevStartDate, $prevEndDate])
                    ->get();

                $metrics['total_boosts'] = $currentMonthBoosts->count();
                $metrics['total_boost_amount'] = $currentMonthBoosts->sum('amount');
                
                $lastBoosts = $lastMonthBoosts->count();
                $lastBoostAmount = $lastMonthBoosts->sum('amount');
                
                $metrics['boosts_growth'] = $lastBoosts > 0 ? round((($metrics['total_boosts'] - $lastBoosts) / $lastBoosts) * 100) : 0;
                $metrics['boost_amount_growth'] = $lastBoostAmount > 0 ? round((($metrics['total_boost_amount'] - $lastBoostAmount) / $lastBoostAmount) * 100) : 0;

                $boostData = $selectedClient->boosts()
                    ->whereBetween('date', [$startDate, $endDate])
                    ->orderBy('date', 'desc')
                    ->orderBy('id', 'desc')
                    ->paginate(10, ['*'], 'boost_page')
                    ->withQueryString();
            }

            if ($canViewTargets) {
                $currentTarget = $selectedClient->monthlyTargets()
                    ->whereYear('month', $now->year)
                    ->whereMonth('month', $now->month)
                    ->first();
                    
                if ($currentTarget) {
                    $postsCompletion = $currentTarget->target_posts > 0 
                        ? min(100, round(($metrics['total_posts'] / $currentTarget->target_posts) * 100)) 
                        : 0;
                    $reelsCompletion = $currentTarget->target_reels > 0 
                        ? min(100, round(($metrics['total_reels'] / $currentTarget->target_reels) * 100)) 
                        : 0;
                    $boostCompletion = $currentTarget->target_boost_budget > 0 
                        ? min(100, round(($metrics['total_boost_amount'] / $currentTarget->target_boost_budget) * 100)) 
                        : 0;
                    
                    $metrics['target_completion'] = round(($postsCompletion + $reelsCompletion + $boostCompletion) / 3);
                    
                    $leftPosts = max(0, $currentTarget->target_posts - $metrics['total_posts']);
                    $leftReels = max(0, $currentTarget->target_reels - $metrics['total_reels']);
                    $leftBoostBudget = max(0, $currentTarget->target_boost_budget - $metrics['total_boost_amount']);
                    
                    $metrics['total_left'] = $leftPosts + $leftReels;
                    $metrics['left_boost_budget'] = $leftBoostBudget;
                    $metrics['total_target'] = $currentTarget->target_posts + $currentTarget->target_reels;
                    $metrics['target_boost_budget'] = $currentTarget->target_boost_budget;

                    if ($metrics['target_completion'] >= 100 && $currentTarget->status !== 'completed') {
                        $currentTarget->update(['status' => 'completed']);
                    } elseif ($metrics['target_completion'] < 100 && $currentTarget->status === 'completed') {
                        $currentTarget->update(['status' => 'active']);
                    }
                }

                $lastTarget = $selectedClient->monthlyTargets()
                    ->whereYear('month', $lastMonth->year)
                    ->whereMonth('month', $lastMonth->month)
                    ->first();

                if ($lastTarget) {
                    $lastPostsCompletion = $lastTarget->target_posts > 0 
                        ? min(100, round(($lastPosts / $lastTarget->target_posts) * 100)) 
                        : 0;
                    $lastReelsCompletion = $lastTarget->target_reels > 0 
                        ? min(100, round(($lastReels / $lastTarget->target_reels) * 100)) 
                        : 0;
                    $lastBoostCompletion = $lastTarget->target_boost_budget > 0 
                        ? min(100, round(($lastBoostAmount / $lastTarget->target_boost_budget) * 100)) 
                        : 0;
                    
                    $lastCompletion = round(($lastPostsCompletion + $lastReelsCompletion + $lastBoostCompletion) / 3);

                    $metrics['target_growth'] = $lastCompletion > 0 ? round((($metrics['target_completion'] - $lastCompletion) / $lastCompletion) * 100) : 0;
                }

                $allTargets = $selectedClient->monthlyTargets()->orderBy('month', 'desc')->get();
                $displayedTargets = $selectedClient->monthlyTargets()
                    ->whereYear('month', $now->year)
                    ->whereMonth('month', $now->month)
                    ->orderBy('month', 'desc')
                    ->get();
            }

            // Charts Data
             $charts = [
                'totalBoostAmount' => $metrics['total_boost_amount'],
                'boostAmountGrowth' => $metrics['boost_amount_growth'],
                'contentDistribution' => [
                    'labels' => ['Posts', 'Reels', 'Boosts'],
                    'series' => [$metrics['total_posts'], $metrics['total_reels'], $metrics['total_boosts']]
                ]
            ];
        }

        return view('dashboard.index', compact('clients', 'selectedClient', 'metrics', 'contentData', 'boostData', 'displayedTargets', 'allTargets', 'charts', 'currentTarget', 'previousMonth', 'hasPreviousData', 'dateContext', 'bsMonth', 'bsYear'));
    }

    public function overview(Request $request)
    {
        $hasPermission = Gate::allows('clients.view');
        
        $bsMonth = (int)$request->query('month');
        $bsYear = (int)$request->query('year');
        $statusFilter = $request->query('status', 'all');

        if (!$bsYear || $bsYear < 2050) {
            $todayBs = \App\Helpers\NepaliDateHelper::adToBs(now());
            $bsMonth = $bsMonth ?: $todayBs['month'];
            $bsYear = $bsYear ?: $todayBs['year'];
        }

        // Get the AD range for this BS month
        [$startDate, $endDate] = \App\Helpers\NepaliDateHelper::getBsMonthRange($bsMonth, $bsYear);
        
        // For context, we still need a representative AD date
        $repAd = \App\Helpers\NepaliDateHelper::bsToAd($bsMonth, $bsYear);
        $dateContext = Carbon::createFromDate($repAd['year'], $repAd['month'], 1);
        $now = $dateContext;

        $canViewContent = Gate::allows('contents.view');
        $canViewBoosts = Gate::allows('boosts.view');
        $canViewTargets = Gate::allows('targets.view');

        $clients = $hasPermission ? Client::orderBy('updated_at', 'desc')
            ->when($canViewTargets, function($query) use ($now) {
                $query->with(['monthlyTargets' => function($q) use ($now) {
                    $q->whereYear('month', $now->year)
                        ->whereMonth('month', $now->month);
                }]);
            })
            ->when($canViewContent, function($query) use ($startDate, $endDate) {
                $query->with(['contents' => function($q) use ($startDate, $endDate) {
                    $q->with('user')->whereBetween('date', [$startDate, $endDate]);
                }]);
            })
            ->when($canViewBoosts, function($query) use ($startDate, $endDate) {
                $query->with(['boosts' => function($q) use ($startDate, $endDate) {
                    $q->with('user')->whereBetween('date', [$startDate, $endDate]);
                }]);
            })
            ->get() : collect();

        $user = auth()->user();
        $clientsData = [];
        $totalAgencyMetrics = [
            'posts' => 0,
            'reels' => 0,
            'boosts' => 0,
            'boost_amount' => 0,
            'target_posts' => 0,
            'target_reels' => 0,
            'target_boost_budget' => 0,
        ];

        foreach ($clients as $client) {
            $target = $client->monthlyTargets->first();

            // Status Filtering Logic
            if ($statusFilter !== 'all') {
                if ($statusFilter === 'no-target' && $target) {
                    continue;
                }
                if ($statusFilter === 'active' && (!$target || $target->status !== 'active')) {
                    continue;
                }
                if ($statusFilter === 'completed' && (!$target || $target->status !== 'completed')) {
                    continue;
                }
            }

            $actualPosts = $canViewContent ? $client->contents->where('type', 'Post')->count() : 0;
            $actualReels = $canViewContent ? $client->contents->where('type', 'Reel')->count() : 0;

            $actualBoosts = $canViewBoosts ? $client->boosts->count() : 0;
            $boostAmount = $canViewBoosts ? $client->boosts->sum('amount') : 0;

            $targetPosts = ($canViewTargets && $target) ? $target->target_posts : 0;
            $targetReels = ($canViewTargets && $target) ? $target->target_reels : 0;
            $targetBoostBudget = ($canViewTargets && $target) ? $target->target_boost_budget : 0;
            
            // Calculate individual completion percentages (only if targets are visible)
            $postsCompletion = $targetPosts > 0 ? min(100, round(($actualPosts / $targetPosts) * 100)) : 0;
            $reelsCompletion = $targetReels > 0 ? min(100, round(($actualReels / $targetReels) * 100)) : 0;
            $boostCompletion = $targetBoostBudget > 0 ? min(100, round(($boostAmount / $targetBoostBudget) * 100)) : 0;
            
            $completion = round(($postsCompletion + $reelsCompletion + $boostCompletion) / 3);

            if ($canViewTargets && $target) {
                if ($completion >= 100 && $target->status !== 'completed') {
                    $target->update(['status' => 'completed']);
                } elseif ($completion < 100 && $target->status === 'completed') {
                    $target->update(['status' => 'active']);
                }
            }

            $clientsData[] = [
                'client' => $client,
                'target' => $canViewTargets ? $target : null,
                'actual_posts' => $actualPosts,
                'actual_reels' => $actualReels,
                'actual_boosts' => $actualBoosts,
                'boost_amount' => $boostAmount,
                'target_posts' => $targetPosts,
                'target_reels' => $targetReels,
                'target_boost_budget' => $targetBoostBudget,
                'completion' => $completion,
                'posts_completion' => $postsCompletion,
                'reels_completion' => $reelsCompletion,
                'boost_completion' => $boostCompletion,
                'total_actual' => $actualPosts + $actualReels + $actualBoosts,
                'total_target' => $targetPosts + $targetReels,
                'total_left' => (max(0, $targetPosts - $actualPosts) + max(0, $targetReels - $actualReels)),
                'contents' => $canViewContent ? $client->contents : collect(),
                'boosts' => $canViewBoosts ? $client->boosts : collect(),
            ];

            // Agency Totals
            $totalAgencyMetrics['posts'] += $actualPosts;
            $totalAgencyMetrics['reels'] += $actualReels;
            $totalAgencyMetrics['boosts'] += $actualBoosts;
            $totalAgencyMetrics['boost_amount'] += $boostAmount;
            $totalAgencyMetrics['target_posts'] += $targetPosts;
            $totalAgencyMetrics['target_reels'] += $targetReels;
            $totalAgencyMetrics['target_boost_budget'] += $targetBoostBudget;
        }

        return view('dashboard.overview', compact('clientsData', 'totalAgencyMetrics', 'dateContext', 'clients', 'bsMonth', 'bsYear'));
    }

    private function nepaliMonthName($month)
    {
        $months = [
            1 => 'Baisakh',
            2 => 'Jestha',
            3 => 'Ashadh',
            4 => 'Shrawan',
            5 => 'Bhadra',
            6 => 'Ashwin',
            7 => 'Kartik',
            8 => 'Mangsir',
            9 => 'Poush',
            10 => 'Magh',
            11 => 'Falgun',
            12 => 'Chaitra'
        ];
        return $months[$month] ?? '';
    }
}
