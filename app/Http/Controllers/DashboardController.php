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
            'posts_growth' => 0,
            'reels_growth' => 0,
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

        if ($selectedClient) {
            // Metrics (Current Month)
            $now = Carbon::now();
            $lastMonth = Carbon::now()->subMonth();
            
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
            
            $lastPosts = $lastMonthContents->where('type', 'Post')->count();
            $lastReels = $lastMonthContents->where('type', 'Reel')->count();
            
            $metrics['posts_growth'] = $lastPosts > 0 ? round((($metrics['total_posts'] - $lastPosts) / $lastPosts) * 100) : 0;
            $metrics['reels_growth'] = $lastReels > 0 ? round((($metrics['total_reels'] - $lastReels) / $lastReels) * 100) : 0;
            
            // Monthly Target
            $currentTarget = $selectedClient->monthlyTargets()
                ->whereYear('month', $now->year)
                ->whereMonth('month', $now->month)
                ->first();
                
            if ($currentTarget) {
                $totalTarget = $currentTarget->target_posts + $currentTarget->target_reels;
                $totalActual = $metrics['total_posts'] + $metrics['total_reels'];
                
                $metrics['target_completion'] = $totalTarget > 0 ? round(($totalActual / $totalTarget) * 100) : 0;
                $metrics['variance'] = $totalActual - $totalTarget; // Simple variance count
            }

            // Previous Month Metrics for Growth Comp (Target & Variance)
            $lastTarget = $selectedClient->monthlyTargets()
                ->whereYear('month', $lastMonth->year)
                ->whereMonth('month', $lastMonth->month)
                ->first();

            if ($lastTarget) {
                $lastTotalTarget = $lastTarget->target_posts + $lastTarget->target_reels;
                $lastTotalActual = $lastPosts + $lastReels;
                
                $lastCompletion = $lastTotalTarget > 0 ? round(($lastTotalActual / $lastTotalTarget) * 100) : 0;
                $lastVariance = $lastTotalActual - $lastTotalTarget;

                // Calculate Growth
                $metrics['target_growth'] = $lastCompletion > 0 ? round((($metrics['target_completion'] - $lastCompletion) / $lastCompletion) * 100) : 0;
                
                // For variance, since it can be negative, standard percentage growth might be misleading or tricky (e.g. -5 to -2 is improvement).
                // Let's settle for simple difference percentage if last variance wasn't 0, or just absolute difference?
                // Standard growth formula: ((New - Old) / |Old|) * 100
                $metrics['variance_growth'] = $lastVariance != 0 ? round((($metrics['variance'] - $lastVariance) / abs($lastVariance)) * 100) : 0;
            }

            // Tables Data
            $contentData = $selectedClient->contents()->latest()->paginate(10);
            $targets = $selectedClient->monthlyTargets()->orderBy('month', 'desc')->get();
            
            // Charts Data
            // 1. Content Type Distribution (Overall)
            $charts['contentDistribution'] = [
                'labels' => ['Posts', 'Reels'],
                'series' => [
                    $selectedClient->contents()->where('type', 'Post')->count(),
                    $selectedClient->contents()->where('type', 'Reel')->count(),
                ]
            ];
            
            // 2. Monthly Progression (posts/reels count per month for current year)
            $months = [];
            $postsData = [];
            $reelsData = [];
            
            for ($i = 1; $i <= 12; $i++) {
                $months[] = Carbon::createFromDate($now->year, $i, 1)->format('M');
                $postsData[] = $selectedClient->contents()
                    ->whereYear('date', $now->year)
                    ->whereMonth('date', $i)
                    ->where('type', 'Post')
                    ->count();
                $reelsData[] = $selectedClient->contents()
                    ->whereYear('date', $now->year)
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
            $year = $now->year;
            $month = $now->month;
            
            // Define 4 weeks for the current month
            $weekRanges = [
                ['start' => Carbon::create($year, $month, 1), 'end' => Carbon::create($year, $month, 7)],
                ['start' => Carbon::create($year, $month, 8), 'end' => Carbon::create($year, $month, 14)],
                ['start' => Carbon::create($year, $month, 15), 'end' => Carbon::create($year, $month, 21)],
                ['start' => Carbon::create($year, $month, 22), 'end' => Carbon::create($year, $month)->endOfMonth()],
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
            ];
        }

        return view('dashboard.index', compact('clients', 'selectedClient', 'metrics', 'contentData', 'targets', 'charts', 'currentTarget'));
    }
}
