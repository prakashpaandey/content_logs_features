<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Content;

class ContentController extends Controller
{
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
            'platform' => 'required|in:Instagram,TikTok,Facebook',
            'type' => 'required|in:Post,Reel',
            'date' => 'required|date',
            'url' => 'nullable|url',
            'remarks' => 'nullable|string',
        ]);

        $date = \Carbon\Carbon::parse($validated['date']);
        $now = \Carbon\Carbon::now();
        $contentBs = \App\Helpers\NepaliDateHelper::adToBs($date);
        $nowBs = \App\Helpers\NepaliDateHelper::adToBs($now);

        // 1. Prevent future dates
        if ($date->isFuture()) {
            return redirect()->back()->withInput()->with('error', 'Cannot create content for upcoming dates.');
        }

        // 2. Prevent creating content for months ahead of the current real Nepali month
        if ($contentBs['year'] > $nowBs['year'] || ($contentBs['year'] == $nowBs['year'] && $contentBs['month'] > $nowBs['month'])) {
            return redirect()->back()->withInput()->with('error', 'Cannot create content for future Nepali months.');
        }

        // 3. Context-based validation: Date must match the dashboard context
        if ($request->has('context_bs_month') && $request->has('context_bs_year')) {
            $contextBsMonth = (int) $request->context_bs_month;
            $contextBsYear = (int) $request->context_bs_year;
            
            if ($contentBs['month'] !== $contextBsMonth || $contentBs['year'] !== $contextBsYear) {
                return redirect()->back()->withInput()->with('error', 'Selected date must match the dashboard month context (Nepali Calendar).');
            }
        }

        Content::create([
            ...$validated,
            'user_id' => auth()->id(), // Keep for audit trail
        ]);

        // Check Monthly Target for completion
        try {
            $repAd = \App\Helpers\NepaliDateHelper::bsToAd($contentBs['month'], $contentBs['year']);
            $target = \App\Models\MonthlyTarget::where('client_id', $validated['client_id'])
                ->whereYear('month', $repAd['year'])
                ->whereMonth('month', $repAd['month'])
                ->first();

            if ($target) {
                $target->checkCompletionStatus();
            }
        } catch (\Exception $e) {
            // silent fail
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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'platform' => 'required|in:Instagram,TikTok,Facebook',
            'type' => 'required|in:Post,Reel',
            'date' => 'required|date',
            'url' => 'nullable|url',
            'remarks' => 'nullable|string',
        ]);

        $date = \Carbon\Carbon::parse($validated['date']);
        $now = \Carbon\Carbon::now();

        // 1. Prevent future dates
        if ($date->isFuture()) {
            return redirect()->back()->withInput()->with('error', 'Cannot update content to upcoming dates or months.');
        }

        // 2. Prevent updating content to future months
        if ($date->year > $now->year || ($date->year == $now->year && $date->month > $now->month)) {
            return redirect()->back()->withInput()->with('error', 'Cannot update content to future months.');
        }

        $content->update($validated);

        // Check Monthly Target for completion
        try {
            $contentBs = \App\Helpers\NepaliDateHelper::adToBs($date);
            $repAd = \App\Helpers\NepaliDateHelper::bsToAd($contentBs['month'], $contentBs['year']);
            
            $target = \App\Models\MonthlyTarget::where('client_id', $content->client_id)
                ->whereYear('month', $repAd['year'])
                ->whereMonth('month', $repAd['month'])
                ->first();

            if ($target) {
                $target->checkCompletionStatus();
            }
        } catch (\Exception $e) {
            // silent fail
        }

        return redirect()->back()->with('success', 'Content updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $content)
    {
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
            // silent fail
        }

        return redirect()->back()->with('success', 'Content deleted successfully.');
    }
}
