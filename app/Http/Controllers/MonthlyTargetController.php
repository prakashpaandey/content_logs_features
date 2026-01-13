<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\MonthlyTarget;

class MonthlyTargetController extends Controller
{

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'month' => 'required|date_format:Y-m',
            'bs_month' => 'nullable|integer',
            'bs_year' => 'nullable|integer',
            'target_posts' => 'required|integer|min:0',
            'target_reels' => 'required|integer|min:0',
            'target_boost_budget' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Append day to make it a valid date
        $validated['month'] = $validated['month'] . '-01';

        // Ensure BS dates are populated
        if (empty($validated['bs_month']) || empty($validated['bs_year'])) {
            $bsDate = \App\Helpers\NepaliDateHelper::representativeAdToBs($validated['month']);
            $validated['bs_month'] = $bsDate['month'];
            $validated['bs_year'] = $bsDate['year'];
        }

        // Check for duplicates
        if (\App\Models\MonthlyTarget::where('client_id', $validated['client_id'])
            ->where('bs_month', $validated['bs_month'])
            ->where('bs_year', $validated['bs_year'])
            ->exists()) {
            return redirect()->back()->with('error', 'A target for this month already exists!');
        }

        MonthlyTarget::create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Target set successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Target set successfully.');
    }





    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MonthlyTarget $monthlyTarget)
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
            'bs_month' => 'nullable|integer',
            'bs_year' => 'nullable|integer',
            'target_posts' => 'required|integer|min:0',
            'target_reels' => 'required|integer|min:0',
            'target_boost_budget' => 'required|numeric|min:0',
            'status' => 'required|in:active,completed,archived',
            'notes' => 'nullable|string',
        ]);

        $validated['month'] = $validated['month'] . '-01';

        if ($request->input('status') === 'completed') {
            $actualPosts = $monthlyTarget->getActualPosts();
            $actualReels = $monthlyTarget->getActualReels();
            $actualBoostAmount = $monthlyTarget->getActualBoostAmount();
                
            $newTargetPosts = $validated['target_posts'];
            $newTargetReels = $validated['target_reels'];
            $newTargetBoostBudget = $validated['target_boost_budget'];

            if ($actualPosts < $newTargetPosts || $actualReels < $newTargetReels || $actualBoostAmount < $newTargetBoostBudget) {
                return redirect()->back()->withErrors(['status' => 'Cannot mark as completed. Actual content counts must meet the targets.']);
            }
        }

        $monthlyTarget->update($validated);
        
        // Re-check completion status in case targets were LOWERED to meet actuals
        $monthlyTarget->checkCompletionStatus();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Target updated successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Target updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MonthlyTarget $monthlyTarget)
    {
        $monthlyTarget->delete();
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Target deleted successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Target deleted successfully.');
    }
    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
            'bs_month' => 'nullable|integer',
            'bs_year' => 'nullable|integer',
            'target_posts' => 'required|integer|min:0',
            'target_reels' => 'required|integer|min:0',
            'target_boost_budget' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $month = $validated['month'] . '-01';
        $user = auth()->user();
        
        // Fetch ALL active clients globally instead of just user's clients
        $clients = \App\Models\Client::where('status', 'active')->get();

        foreach ($clients as $client) {
            $client->monthlyTargets()->updateOrCreate(
                [
                    'bs_month' => $validated['bs_month'] ?? \App\Helpers\NepaliDateHelper::representativeAdToBs($month)['month'],
                    'bs_year' => $validated['bs_year'] ?? \App\Helpers\NepaliDateHelper::representativeAdToBs($month)['year']
                ],
                [
                    'month' => $month,
                    'user_id' => $user->id,
                    'target_posts' => $validated['target_posts'],
                    'target_reels' => $validated['target_reels'],
                    'target_boost_budget' => $validated['target_boost_budget'],
                    'status' => 'active',
                    'notes' => $validated['notes'],
                ]
            );
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Targets updated for all clients.'
            ]);
        }

        return redirect()->back()->with('success', 'Targets updated for all clients.');
    }
}
