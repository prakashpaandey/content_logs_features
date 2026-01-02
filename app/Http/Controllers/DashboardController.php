<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Content;
use App\Models\MonthlyTarget;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $clients = $user->clients()->orderBy('updated_at', 'desc')->get();
        
        $selectedClientId = $request->query('client_id');
        $selectedClient = $clients->where('id', $selectedClientId)->first() ?? $clients->first();
        
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
        $charts = [
            'totalBoostAmount' => 0,
            'boostAmountGrowth' => 0,
            'contentDistribution' => [
                'labels' => ['Posts', 'Reels', 'Boosts'],
                'series' => [0, 0, 0]
            ],
            'monthlyProgression' => [
                'categories' => [],
                'posts' => [],
                'reels' => [],
                'boosts' => []
            ],
            'targetVsActual' => [
                'categories' => [],
                'targetPosts' => [],
                'actualPosts' => [],
                'targetReels' => [],
                'actualReels' => [],
                'targetBoosts' => [],
                'actualBoosts' => [],
                'target_month_name' => ''
            ]
        ];

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
            
            // Check if previous month has any data (targets, content or boosts in range)
            $hasPreviousData = $selectedClient->monthlyTargets()
                ->whereYear('month', $lastMonth->year)
                ->whereMonth('month', $lastMonth->month)
                ->exists() 
                || 
                $selectedClient->contents()
                ->whereBetween('date', [$prevStartDate, $prevEndDate])
                ->exists()
                ||
                $selectedClient->boosts()
                ->whereBetween('date', [$prevStartDate, $prevEndDate])
                ->exists();
            
            $currentMonthContents = $selectedClient->contents()
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            $currentMonthBoosts = $selectedClient->boosts()
                ->whereBetween('date', [$startDate, $endDate])
                ->get();
                
            $lastMonthContents = $selectedClient->contents()
                ->whereBetween('date', [$prevStartDate, $prevEndDate])
                ->get();

            $lastMonthBoosts = $selectedClient->boosts()
                ->whereBetween('date', [$prevStartDate, $prevEndDate])
                ->get();
            
            $metrics['total_posts'] = $currentMonthContents->where('type', 'Post')->count();
            $metrics['total_reels'] = $currentMonthContents->where('type', 'Reel')->count();
            $metrics['total_boosts'] = $currentMonthBoosts->count();
            $metrics['total_boost_amount'] = $currentMonthBoosts->sum('amount');
            
            $lastPosts = $lastMonthContents->where('type', 'Post')->count();
            $lastReels = $lastMonthContents->where('type', 'Reel')->count();
            $lastBoosts = $lastMonthBoosts->count();
            $lastBoostAmount = $lastMonthBoosts->sum('amount');
            
            $metrics['posts_growth'] = $lastPosts > 0 ? round((($metrics['total_posts'] - $lastPosts) / $lastPosts) * 100) : 0;
            $metrics['reels_growth'] = $lastReels > 0 ? round((($metrics['total_reels'] - $lastReels) / $lastReels) * 100) : 0;
            $metrics['boosts_growth'] = $lastBoosts > 0 ? round((($metrics['total_boosts'] - $lastBoosts) / $lastBoosts) * 100) : 0;
            $metrics['boost_amount_growth'] = $lastBoostAmount > 0 ? round((($metrics['total_boost_amount'] - $lastBoostAmount) / $lastBoostAmount) * 100) : 0;
            
            // Monthly Target (Still keyed by representative AD month start)
            $currentTarget = $selectedClient->monthlyTargets()
                ->whereYear('month', $now->year)
                ->whereMonth('month', $now->month)
                ->first();
                
            if ($currentTarget) {
                // Better Logic: Sum of capped actuals to prevent over-performance in one area masking failure in another
                $cappedPosts = min($metrics['total_posts'], $currentTarget->target_posts);
                $cappedReels = min($metrics['total_reels'], $currentTarget->target_reels);
                $cappedBoosts = min($metrics['total_boosts'], $currentTarget->target_boosts);
                
                $totalTarget = $currentTarget->target_posts + $currentTarget->target_reels + $currentTarget->target_boosts;
                $totalCappedActual = $cappedPosts + $cappedReels + $cappedBoosts;
                
                $metrics['target_completion'] = $totalTarget > 0 ? round(($totalCappedActual / $totalTarget) * 100) : 0;
                $metrics['variance'] = ($metrics['total_posts'] + $metrics['total_reels'] + $metrics['total_boosts']) - $totalTarget; 
                
                // Add robust "Left" calculation
                $leftPosts = max(0, $currentTarget->target_posts - $metrics['total_posts']);
                $leftReels = max(0, $currentTarget->target_reels - $metrics['total_reels']);
                $leftBoosts = max(0, $currentTarget->target_boosts - $metrics['total_boosts']);
                $metrics['total_left'] = $leftPosts + $leftReels + $leftBoosts;
                $metrics['total_target'] = $totalTarget;

                // On-the-fly status sync fallback
                if ($metrics['target_completion'] >= 100 && $currentTarget->status !== 'completed') {
                    $currentTarget->update(['status' => 'completed']);
                } elseif ($metrics['target_completion'] < 100 && $currentTarget->status === 'completed') {
                    $currentTarget->update(['status' => 'active']);
                }
            } else {
                $metrics['target_completion'] = 0;
                $metrics['variance'] = 0;
                $metrics['total_left'] = 0;
                $metrics['total_target'] = 0;
            }

            // Previous Month Metrics for Growth Comp (Target & Variance)
            $lastTarget = $selectedClient->monthlyTargets()
                ->whereYear('month', $lastMonth->year)
                ->whereMonth('month', $lastMonth->month)
                ->first();

            if ($lastTarget) {
                $lastTotalTarget = $lastTarget->target_posts + $lastTarget->target_reels + $lastTarget->target_boosts;
                $lastTotalActual = $lastPosts + $lastReels + $lastBoosts;
                
                $lastCompletion = $lastTotalTarget > 0 ? round(($lastTotalActual / $lastTotalTarget) * 100) : 0;
                $lastVariance = $lastTotalActual - $lastTotalTarget;

                $metrics['target_growth'] = $lastCompletion > 0 ? round((($metrics['target_completion'] - $lastCompletion) / $lastCompletion) * 100) : 0;
                $metrics['variance_growth'] = $lastVariance != 0 ? round((($metrics['variance'] - $lastVariance) / abs($lastVariance)) * 100) : 0;
            }

            // Tables Data (Strictly filtered by the active dashboard month context)
            $contentData = $selectedClient->contents()
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->paginate(10, ['*'], 'content_page')
                ->withQueryString();

            $boostData = $selectedClient->boosts()
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->paginate(10, ['*'], 'boost_page')
                ->withQueryString();

            $allTargets = $selectedClient->monthlyTargets()->orderBy('month', 'desc')->get();
            
            // Filter targets for the main display (Current Month Context ONLY)
            $displayedTargets = $selectedClient->monthlyTargets()
                ->whereYear('month', $now->year)
                ->whereMonth('month', $now->month)
                ->orderBy('month', 'desc')
                ->get();
            
            // Charts Data
            // 1. Content Type Distribution (Overall for the selected month?)
            // Pie chart usually shows "Distribution". If dashboard is time-scoped, it should be for that month.
            // But code originally used `->count()` on ALL contents (lines 109-110).
            // Let's scope it to the view context (Month) to be consistent with "Overview Metrics".
             $charts['totalBoostAmount'] = $metrics['total_boost_amount'];
             $charts['boostAmountGrowth'] = $metrics['boost_amount_growth'];

            $charts['contentDistribution'] = [
                'labels' => ['Posts', 'Reels', 'Boosts'],
                'series' => [
                    $metrics['total_posts'],
                    $metrics['total_reels'],
                    $metrics['total_boosts']
                ]
            ];
            
            // 2. Monthly Progression (Last 12 BS Months for Year View)
            $months = [];
            $postsData = [];
            $reelsData = [];
            $boostsData = [];
            
            for ($i = 11; $i >= 0; $i--) {
                $m = $bsMonth - $i;
                $y = $bsYear;
                while ($m < 1) { 
                    $m += 12; 
                    $y--; 
                }
                
                [$s, $e] = \App\Helpers\NepaliDateHelper::getBsMonthRange($m, $y);
                
                $months[] = $this->nepaliMonthName($m);
                $postsData[] = $selectedClient->contents()->whereBetween('date', [$s, $e])->where('type', 'Post')->count();
                $reelsData[] = $selectedClient->contents()->whereBetween('date', [$s, $e])->where('type', 'Reel')->count();
                $boostsData[] = $selectedClient->boosts()->whereBetween('date', [$s, $e])->count();
            }
            
            $charts['monthlyProgression'] = [
                'categories' => $months,
                'posts' => $postsData,
                'reels' => $reelsData,
                'boosts' => $boostsData,
            ];
            
            // 3. Target vs Actual (Weekly breakdown for current BS month)
            $weeklyTargetPosts = [];
            $weeklyActualPosts = [];
            $weeklyTargetReels = [];
            $weeklyActualReels = [];
            $weeklyTargetBoosts = [];
            $weeklyActualBoosts = [];
            
            $totalTargetPosts = $currentTarget ? $currentTarget->target_posts : 0;
            $totalTargetReels = $currentTarget ? $currentTarget->target_reels : 0;
            $totalTargetBoosts = $currentTarget ? $currentTarget->target_boosts : 0;
            $weeksCount = 4;
            
            $distributeTarget = function($total, $parts) {
                $base = floor($total / $parts);
                $remainder = $total % $parts;
                $distribution = [];
                for ($i = 0; $i < $parts; $i++) {
                    $distribution[] = $i < $remainder ? $base + 1 : $base;
                }
                return $distribution;
            };
            
            $distributedPosts = $distributeTarget($totalTargetPosts, $weeksCount);
            $distributedReels = $distributeTarget($totalTargetReels, $weeksCount);
            $distributedBoosts = $distributeTarget($totalTargetBoosts, $weeksCount);
            
            // Divide BS month into 4 weeks
            $totalDays = $startDate->diffInDays($endDate) + 1;
            $daysPerWeek = floor($totalDays / 4);
            
            for ($i = 0; $i < 4; $i++) {
                $wStart = $startDate->copy()->addDays($i * $daysPerWeek);
                if ($i == 3) {
                    $wEnd = $endDate->copy();
                } else {
                    $wEnd = $wStart->copy()->addDays($daysPerWeek - 1)->endOfDay();
                }
                
                $weeklyActualPosts[] = $selectedClient->contents()
                    ->whereBetween('date', [$wStart, $wEnd])
                    ->where('type', 'Post')
                    ->count();
                    
                $weeklyActualReels[] = $selectedClient->contents()
                    ->whereBetween('date', [$wStart, $wEnd])
                    ->where('type', 'Reel')
                    ->count();

                $weeklyActualBoosts[] = $selectedClient->boosts()
                    ->whereBetween('date', [$wStart, $wEnd])
                    ->count();
                
                $weeklyTargetPosts[] = $distributedPosts[$i];
                $weeklyTargetReels[] = $distributedReels[$i];
                $weeklyTargetBoosts[] = $distributedBoosts[$i];
            }
            
            $charts['targetVsActual'] = [
                'categories' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                'targetPosts' => $weeklyTargetPosts,
                'actualPosts' => $weeklyActualPosts,
                'targetReels' => $weeklyTargetReels,
                'actualReels' => $weeklyActualReels,
                'targetBoosts' => $weeklyTargetBoosts,
                'actualBoosts' => $weeklyActualBoosts,
                'target_month_name' => $now->format('F Y')
            ];
        }

        return view('dashboard.index', compact('clients', 'selectedClient', 'metrics', 'contentData', 'boostData', 'displayedTargets', 'allTargets', 'charts', 'currentTarget', 'previousMonth', 'hasPreviousData', 'dateContext', 'bsMonth', 'bsYear'));
    }
    public function overview(Request $request)
    {
        $user = auth()->user();
        $clients = $user->clients()->orderBy('updated_at', 'desc')->get();
        
        $bsMonth = (int)$request->query('month');
        $bsYear = (int)$request->query('year');

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

        $clientsData = [];
        $totalAgencyMetrics = [
            'posts' => 0,
            'reels' => 0,
            'boosts' => 0,
            'boost_amount' => 0,
            'target_posts' => 0,
            'target_reels' => 0,
            'target_boosts' => 0,
        ];

        foreach ($clients as $client) {
            $target = $client->monthlyTargets()
                ->whereYear('month', $now->year)
                ->whereMonth('month', $now->month)
                ->first();

            $actualPosts = $client->contents()
                ->whereBetween('date', [$startDate, $endDate])
                ->where('type', 'Post')->count();

            $actualReels = $client->contents()
                ->whereBetween('date', [$startDate, $endDate])
                ->where('type', 'Reel')->count();

            $boostRecords = $client->boosts()
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            $actualBoosts = $boostRecords->count();
            $boostAmount = $boostRecords->sum('amount');

            $targetPosts = $target ? $target->target_posts : 0;
            $targetReels = $target ? $target->target_reels : 0;
            $targetBoosts = $target ? $target->target_boosts : 0;

            $totalTarget = $targetPosts + $targetReels + $targetBoosts;
            
            // Capped calculation for honest progress
            $cappedPosts = min($actualPosts, $targetPosts);
            $cappedReels = min($actualReels, $targetReels);
            $cappedBoosts = min($actualBoosts, $targetBoosts);
            $totalCappedActual = $cappedPosts + $cappedReels + $cappedBoosts;

            $completion = $totalTarget > 0 ? round(($totalCappedActual / $totalTarget) * 100) : 0;

            // On-the-fly status sync fallback for overview
            if ($target) {
                if ($completion >= 100 && $target->status !== 'completed') {
                    $target->update(['status' => 'completed']);
                } elseif ($completion < 100 && $target->status === 'completed') {
                    $target->update(['status' => 'active']);
                }
            }

            $clientsData[] = [
                'client' => $client,
                'target' => $target,
                'actual_posts' => $actualPosts,
                'actual_reels' => $actualReels,
                'actual_boosts' => $actualBoosts,
                'boost_amount' => $boostAmount,
                'target_posts' => $targetPosts,
                'target_reels' => $targetReels,
                'target_boosts' => $targetBoosts,
                'completion' => $completion,
                'total_actual' => $actualPosts + $actualReels + $actualBoosts,
                'total_target' => $totalTarget,
                'total_left' => (max(0, $targetPosts - $actualPosts) + max(0, $targetReels - $actualReels) + max(0, $targetBoosts - $actualBoosts)),
            ];

            // Agency Totals
            $totalAgencyMetrics['posts'] += $actualPosts;
            $totalAgencyMetrics['reels'] += $actualReels;
            $totalAgencyMetrics['boosts'] += $actualBoosts;
            $totalAgencyMetrics['boost_amount'] += $boostAmount;
            $totalAgencyMetrics['target_posts'] += $targetPosts;
            $totalAgencyMetrics['target_reels'] += $targetReels;
            $totalAgencyMetrics['target_boosts'] += $targetBoosts;
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
