<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Models\Content;

class ContentController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:contents.create')->only(['store']);
        $this->middleware('can:contents.update')->only(['update']);
        $this->middleware('can:contents.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'platform' => 'required|array|min:1',
            'platform.*' => 'required|in:Instagram,TikTok,Facebook',
            'type' => 'required|in:Post,Reel',
            'date' => 'required|date',
            'platform_urls' => 'nullable|array',
            'platform_urls.*' => 'nullable|url',
            'remarks' => 'nullable|string',
        ]);

        // Handle platform URLs - store as JSON
        $platformUrls = [];
        if ($request->has('platform_urls')) {
            foreach ($request->platform_urls as $platform => $url) {
                if (!empty($url)) {
                    $platformUrls[$platform] = $url;
                }
            }
        }
        $validated['url'] = !empty($platformUrls) ? json_encode($platformUrls) : null;

        // Check if bs_date is provided (Server-side date conversion fallback)
        $bsDateInput = $request->bs_date ?? $request->manual_bs_date;
        if ($bsDateInput) {
            try {
                $bsDateStr = $bsDateInput;
                $parts = explode('-', $bsDateStr);
                if (count($parts) === 3) {
                    $bsYear = (int)$parts[0];
                    $bsMonth = (int)$parts[1];
                    $bsDay = (int)$parts[2];
                    
                    $adDate = \App\Helpers\NepaliDateHelper::bsToAd($bsMonth, $bsYear, $bsDay);
                    $validated['date'] = $adDate['year'] . '-' . str_pad($adDate['month'], 2, '0', STR_PAD_LEFT) . '-' . str_pad($adDate['day'], 2, '0', STR_PAD_LEFT);
                }
            } catch (\Exception $e) {
               
            }
        }

        $date = \Carbon\Carbon::parse($validated['date']);
        $now = \Carbon\Carbon::now();
        $contentBs = \App\Helpers\NepaliDateHelper::adToBs($date);
        $nowBs = \App\Helpers\NepaliDateHelper::adToBs($now);

        /* 
        // Prevent future dates
        if ($date->startOfDay()->gt(\Carbon\Carbon::today())) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot create content for upcoming dates!'
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', 'Cannot create content for upcoming dates!');
        }
        */

        /*
        //Prevent creating content for months ahead of the current real Nepali month
        if ($contentBs['year'] > $nowBs['year'] || ($contentBs['year'] == $nowBs['year'] && $contentBs['month'] > $nowBs['month'])) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot create content for future months.'
                ], 422);
            }
            return redirect()->back()->withInput()->with('error', 'Cannot create content for future months.');
        }
        */

        /*
        //Context-based validation: Date must match the dashboard context
        if ($request->has('context_bs_month') && $request->has('context_bs_year')) {
            $contextBsMonth = (int) $request->context_bs_month;
            $contextBsYear = (int) $request->context_bs_year;
            
            if ($contentBs['month'] !== $contextBsMonth || $contentBs['year'] !== $contextBsYear) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected date must match the dashboard month'
                    ], 422);
                }
                return redirect()->back()->withInput()->with('error', 'Selected date must match the dashboard month');
            }
        }
        */

        $bsData = [];
        if ($request->has('context_bs_month') && $request->has('context_bs_year')) {
            $bsData['bs_month'] = (int) $request->context_bs_month;
            $bsData['bs_year'] = (int) $request->context_bs_year;
            $bsData['bs_day'] = $contentBs['day'];
        } else {
            $bsData['bs_month'] = $contentBs['month'];
            $bsData['bs_year'] = $contentBs['year'];
            $bsData['bs_day'] = $contentBs['day'];
        }

        Content::create([
            ...$validated,
            ...$bsData,
            'user_id' => auth()->id(),
        ]);

        //Check Monthly Target for completion
        try {
            $repAd = \App\Helpers\NepaliDateHelper::bsToAd($bsData['bs_month'], $bsData['bs_year']);
            $target = \App\Models\MonthlyTarget::where('client_id', $validated['client_id'])
                ->whereYear('month', $repAd['year'])
                ->whereMonth('month', $repAd['month'])
                ->first();

            if ($target) {
                $target->checkCompletionStatus();
            }
        } catch (\Exception $e) {
           
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Content added successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Content added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Content $content)
    {

        if (!auth()->user()->isAdmin() && $content->user_id !== auth()->id()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to edit this content.'
                ], 403);
            }
            return redirect()->back()->with('error', 'You are not authorized to edit this content.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'platform' => 'required|array|min:1',
            'platform.*' => 'required|in:Instagram,TikTok,Facebook',
            'type' => 'required|in:Post,Reel',
            'date' => 'required|date',
            'platform_urls' => 'nullable|array',
            'platform_urls.*' => 'nullable|url',
            'remarks' => 'nullable|string',
        ]);

        // Handle platform URLs - store as JSON
        $platformUrls = [];
        if ($request->has('platform_urls')) {
            foreach ($request->platform_urls as $platform => $url) {
                if (!empty($url)) {
                    $platformUrls[$platform] = $url;
                }
            }
        }
        $validated['url'] = !empty($platformUrls) ? json_encode($platformUrls) : null;

        // Check if bs_date is provided (Server-side date conversion fallback)
        $bsDateInput = $request->bs_date ?? $request->manual_bs_date;
        if ($bsDateInput) {
            try {
                $bsDateStr = $bsDateInput;
                $parts = explode('-', $bsDateStr);
                if (count($parts) === 3) {
                    $bsYear = (int)$parts[0];
                    $bsMonth = (int)$parts[1];
                    $bsDay = (int)$parts[2];
                    $adDate = \App\Helpers\NepaliDateHelper::bsToAd($bsMonth, $bsYear, $bsDay);
                    $validated['date'] = $adDate['year'] . '-' . str_pad($adDate['month'], 2, '0', STR_PAD_LEFT) . '-' . str_pad($adDate['day'], 2, '0', STR_PAD_LEFT);
                }
            } catch (\Exception $e) {}
        }

        $date = \Carbon\Carbon::parse($validated['date']);
        $now = \Carbon\Carbon::now();

        /*
        //Prevent future dates
        if ($date->startOfDay()->gt($now->startOfDay())) {
            return redirect()->back()->withInput()->with('error', 'Cannot update content to upcoming dates.');
        }
        */

        /*
        //Prevent updating content to future months
        if ($date->year > $now->year || ($date->year == $now->year && $date->month > $now->month)) {
            return redirect()->back()->withInput()->with('error', 'Cannot update content to future months.');
        }
        */

        $bsData = [];
        if ($request->has('context_bs_month') && $request->has('context_bs_year')) {
            $bsData['bs_month'] = (int) $request->context_bs_month;
            $bsData['bs_year'] = (int) $request->context_bs_year;
        } else {
            $bsData['bs_month'] = $contentBs['month'] ?? \App\Helpers\NepaliDateHelper::adToBs($date)['month'];
            $bsData['bs_year'] = $contentBs['year'] ?? \App\Helpers\NepaliDateHelper::adToBs($date)['year'];
        }
        $bsData['bs_day'] = $contentBs['day'] ?? \App\Helpers\NepaliDateHelper::adToBs($date)['day'];

        $content->update([
            ...$validated,
            ...$bsData
        ]);

        // Check Monthly Target for completion
        try {
            $repAd = \App\Helpers\NepaliDateHelper::bsToAd($bsData['bs_month'], $bsData['bs_year']);
            
            $target = \App\Models\MonthlyTarget::where('client_id', $content->client_id)
                ->whereYear('month', $repAd['year'])
                ->whereMonth('month', $repAd['month'])
                ->first();

            if ($target) {
                $target->checkCompletionStatus();
            }
        } catch (\Exception $e) {
            
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Content updated successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Content updated successfully.');
    }

    public function destroy(Content $content)
    {

        if (!auth()->user()->isAdmin() && $content->user_id !== auth()->id()) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to delete this content.'
                ], 403);
            }
            return redirect()->back()->with('error', 'You are not authorized to delete this content.');
        }

        $clientId = $content->client_id;
        $date = \Carbon\Carbon::parse($content->date);

        $content->delete();

        // Check Monthly Target for completion (revert if needed)
        try {
            $contentBs = \App\Helpers\NepaliDateHelper::adToBs($date);
            $repAd = \App\Helpers\NepaliDateHelper::bsToAd($contentBs['month'], $contentBs['year']);
            
            $target = \App\Models\MonthlyTarget::where('client_id', $clientId)
                ->whereYear('month', $repAd['year'])
                ->whereMonth('month', $repAd['month'])
                ->first();

            if ($target) {
                $target->checkCompletionStatus();
            }
        } catch (\Exception $e) {
            
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Content deleted successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Content deleted successfully.');
    }
}
