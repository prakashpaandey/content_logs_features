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
            'variance_growth' => 0
        ];
        
        $contentData = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        $targets = collect([]);
        $charts = [
            'monthly_progress' => ['posts' => [], 'reels' => [], 'labels' => []],
            'type_distribution' => [0, 0], 
            'target_vs_actual' => ['target_posts' => [], 'actual_posts' => [], 'target_reels' => [], 'actual_reels' => []]
        ];

        $currentTarget = null;

        $previousMonth = null;
        $hasPreviousData = false;
        $dateContext = Carbon::now();
        $allTargets = collect([]);
        $displayedTargets = collect([]);

        if ($selectedClient) {
            // Metrics (Current Month or Selected Month)
            $yearInput = $request->input('year');
            $monthInput = $request->input('month');

            if ($yearInput && $yearInput > 2050) {
                // Convert BS Input to AD for querying
                $bsMonth = (int)$monthInput ?: 1;
                $bsYear = (int)$yearInput;
                
                if ($bsMonth <= 9) {
                    $adMonth = $bsMonth + 3;
                    $adYear = $bsYear - 57;
                } else {
                    $adMonth = $bsMonth - 9;
                    $adYear = $bsYear - 56;
                }
                $now = Carbon::createFromDate($adYear, $adMonth, 1);
            } else {
                // Use AD input or default to now
                $adYear = $yearInput ?: Carbon::now()->year;
                $adMonth = $monthInput ?: Carbon::now()->month;
                $now = Carbon::createFromDate($adYear, $adMonth, 1);
            }
            
            $dateContext = $now;
            
            // Previous Month Calculation
            $lastMonth = $now->copy()->subMonth();
            $previousMonth = $lastMonth;
            
            // Check if previous month has any data (targets or content)
            $hasPreviousData = $selectedClient->monthlyTargets()
                ->whereYear('month', $lastMonth->year)
                ->whereMonth('month', $lastMonth->month)
                ->exists() 
                || 
                $selectedClient->contents()
                ->whereYear('date', $lastMonth->year)
                ->whereMonth('date', $lastMonth->month)
                ->exists();
            
            $currentMonthContents = $selectedClient->contents()
                ->whereMonth('date', $now->month)
                ->whereYear('date', $now->year)
                ->get();
                
            $lastMonthContents = $selectedClient->contents()
                ->whereMonth('date', $lastMonth->month)
                ->whereYear('date', $lastMonth->year)
                ->get();
            
            $metrics['total_posts'] = $currentMonthContents->where('type', 'Post')->count();
            $metrics['total_reels'] = $currentMonthContents->where('type', 'Reel')->count();
            $metrics['total_boosts'] = $currentMonthContents->where('type', 'Boost')->count();
            
            $lastPosts = $lastMonthContents->where('type', 'Post')->count();
            $lastReels = $lastMonthContents->where('type', 'Reel')->count();
            $lastBoosts = $lastMonthContents->where('type', 'Boost')->count();
            
            $metrics['posts_growth'] = $lastPosts > 0 ? round((($metrics['total_posts'] - $lastPosts) / $lastPosts) * 100) : 0;
            $metrics['reels_growth'] = $lastReels > 0 ? round((($metrics['total_reels'] - $lastReels) / $lastReels) * 100) : 0;
            $metrics['boosts_growth'] = $lastBoosts > 0 ? round((($metrics['total_boosts'] - $lastBoosts) / $lastBoosts) * 100) : 0;
            
            // Monthly Target
            $currentTarget = $selectedClient->monthlyTargets()
                ->whereYear('month', $now->year)
                ->whereMonth('month', $now->month)
                ->first();
                
            if ($currentTarget) {
                $totalTarget = $currentTarget->target_posts + $currentTarget->target_reels + $currentTarget->target_boosts;
                $totalActual = $metrics['total_posts'] + $metrics['total_reels'] + $metrics['total_boosts'];
                
                $metrics['target_completion'] = $totalTarget > 0 ? round(($totalActual / $totalTarget) * 100) : 0;
                $metrics['variance'] = $totalActual - $totalTarget; // Simple variance count
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

                // Calculate Growth
                $metrics['target_growth'] = $lastCompletion > 0 ? round((($metrics['target_completion'] - $lastCompletion) / $lastCompletion) * 100) : 0;
                
                // For variance, since it can be negative, standard percentage growth might be misleading or tricky (e.g. -5 to -2 is improvement).
                // Let's settle for simple difference percentage if last variance wasn't 0, or just absolute difference?
                // Standard growth formula: ((New - Old) / |Old|) * 100
                $metrics['variance_growth'] = $lastVariance != 0 ? round((($metrics['variance'] - $lastVariance) / abs($lastVariance)) * 100) : 0;
            }

            // Tables Data (Strictly filtered by the active dashboard month context)
            $contentData = $selectedClient->contents()
                ->whereYear('date', $now->year)
                ->whereMonth('date', $now->month)
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->paginate(10)
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
             $charts['contentDistribution'] = [
                'labels' => ['Posts', 'Reels'],
                'series' => [
                    $selectedClient->contents()
                        ->whereYear('date', $now->year)
                        ->whereMonth('date', $now->month)
                        ->where('type', 'Post')->count(),
                    $selectedClient->contents()
                         ->whereYear('date', $now->year)
                        ->whereMonth('date', $now->month)
                        ->where('type', 'Reel')->count(),
                ]
            ];
            
            // 2. Monthly Progression (posts/reels count per month for "Real Today" current year)
            // User requested this be fixed to the current calendar year regardless of dashboard view month.
            $currentRealYear = Carbon::now()->year;
            $months = [];
            $postsData = [];
            $reelsData = [];
            
            for ($i = 1; $i <= 12; $i++) {
                $months[] = Carbon::createFromDate($currentRealYear, $i, 1)->format('M');
                $postsData[] = $selectedClient->contents()
                    ->whereYear('date', $currentRealYear)
                    ->whereMonth('date', $i)
                    ->where('type', 'Post')
                    ->count();
                $reelsData[] = $selectedClient->contents()
                    ->whereYear('date', $currentRealYear)
                    ->whereMonth('date', $i)
                    ->where('type', 'Reel')
                    ->count();
            }
            
            $charts['monthlyProgression'] = [
                'categories' => $months,
                'posts' => $postsData,
                'reels' => $reelsData,
            ];
            
            // 3. Target vs Actual (weekly breakdown for current month)
            $weeklyTargetPosts = [];
            $weeklyActualPosts = [];
            $weeklyTargetReels = [];
            $weeklyActualReels = [];
            
            // Calculate distributed weekly targets (handling odd numbers)
            $totalTargetPosts = $currentTarget ? $currentTarget->target_posts : 0;
            $totalTargetReels = $currentTarget ? $currentTarget->target_reels : 0;
            $weeksCount = 4;
            
            // Helper function to distribute targets
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
            
            // Get current month boundaries
            // Use $now which is already set
            
            // Define 4 weeks for the current month
            $weekRanges = [
                ['start' => Carbon::create($now->year, $now->month, 1), 'end' => Carbon::create($now->year, $now->month, 7)],
                ['start' => Carbon::create($now->year, $now->month, 8), 'end' => Carbon::create($now->year, $now->month, 14)],
                ['start' => Carbon::create($now->year, $now->month, 15), 'end' => Carbon::create($now->year, $now->month, 21)],
                ['start' => Carbon::create($now->year, $now->month, 22), 'end' => Carbon::create($now->year, $now->month)->endOfMonth()],
            ];
            
            foreach ($weekRanges as $index => $range) {
                $weekStart = $range['start']->format('Y-m-d');
                $weekEnd = $range['end']->format('Y-m-d');
                
                // Count actual posts for this week
                $weeklyActualPosts[] = $selectedClient->contents()
                    ->whereDate('date', '>=', $weekStart)
                    ->whereDate('date', '<=', $weekEnd)
                    ->where('type', 'Post')
                    ->count();
                    
                // Count actual reels for this week
                $weeklyActualReels[] = $selectedClient->contents()
                    ->whereDate('date', '>=', $weekStart)
                    ->whereDate('date', '<=', $weekEnd)
                    ->where('type', 'Reel')
                    ->count();
                
                // Set weekly targets from properly distributed array
                $weeklyTargetPosts[] = $distributedPosts[$index];
                $weeklyTargetReels[] = $distributedReels[$index];
            }
            
            $charts['targetVsActual'] = [
                'categories' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                'targetPosts' => $weeklyTargetPosts,
                'actualPosts' => $weeklyActualPosts,
                'targetReels' => $weeklyTargetReels,
                'actualReels' => $weeklyActualReels,
                'target_month_name' => $now->format('F Y')
            ];
        }

        return view('dashboard.index', compact('clients', 'selectedClient', 'metrics', 'contentData', 'displayedTargets', 'allTargets', 'charts', 'currentTarget', 'previousMonth', 'hasPreviousData', 'dateContext'));
    }
    public function overview(Request $request)
    {
        $user = auth()->user();
        $clients = $user->clients()->orderBy('updated_at', 'desc')->get();
        
        $yearInput = $request->input('year');
        $monthInput = $request->input('month');

        if ($yearInput && $yearInput > 2050) {
            $bsMonth = (int)$monthInput ?: 1;
            $bsYear = (int)$yearInput;
            
            if ($bsMonth <= 9) {
                $adMonth = $bsMonth + 3;
                $adYear = $bsYear - 57;
            } else {
                $adMonth = $bsMonth - 9;
                $adYear = $bsYear - 56;
            }
            $now = Carbon::createFromDate($adYear, $adMonth, 1);
        } else {
            $adYear = $yearInput ?: Carbon::now()->year;
            $adMonth = $monthInput ?: Carbon::now()->month;
            $now = Carbon::createFromDate($adYear, $adMonth, 1);
        }
        $dateContext = $now;

        $clientsData = [];
        $totalAgencyMetrics = [
            'posts' => 0,
            'reels' => 0,
            'boosts' => 0,
            'target_posts' => 0,
            'target_reels' => 0,
            'target_boosts' => 0,
        ];

        foreach ($clients as $client) {
            $target = $client->monthlyTargets()
                ->whereYear('month', $now->year)
                ->whereMonth('month', $now->month)
                ->first();

            $contents = $client->contents()
                ->whereYear('date', $now->year)
                ->whereMonth('date', $now->month)
                ->get();

            $actualPosts = $contents->where('type', 'Post')->count();
            $actualReels = $contents->where('type', 'Reel')->count();
            $actualBoosts = $contents->where('type', 'Boost')->count();

            $targetPosts = $target ? $target->target_posts : 0;
            $targetReels = $target ? $target->target_reels : 0;
            $targetBoosts = $target ? $target->target_boosts : 0;

            $totalTarget = $targetPosts + $targetReels + $targetBoosts;
            $totalActual = $actualPosts + $actualReels + $actualBoosts;
            $completion = $totalTarget > 0 ? min(100, round(($totalActual / $totalTarget) * 100)) : 0;

            $clientsData[] = [
                'client' => $client,
                'target' => $target,
                'actual_posts' => $actualPosts,
                'actual_reels' => $actualReels,
                'actual_boosts' => $actualBoosts,
                'target_posts' => $targetPosts,
                'target_reels' => $targetReels,
                'target_boosts' => $targetBoosts,
                'completion' => $completion,
                'total_actual' => $totalActual,
                'total_target' => $totalTarget,
            ];

            // Agency Totals
            $totalAgencyMetrics['posts'] += $actualPosts;
            $totalAgencyMetrics['reels'] += $actualReels;
            $totalAgencyMetrics['boosts'] += $actualBoosts;
            $totalAgencyMetrics['target_posts'] += $targetPosts;
            $totalAgencyMetrics['target_reels'] += $targetReels;
            $totalAgencyMetrics['target_boosts'] += $targetBoosts;
        }

        return view('dashboard.overview', compact('clientsData', 'totalAgencyMetrics', 'dateContext', 'clients'));
    }
}
