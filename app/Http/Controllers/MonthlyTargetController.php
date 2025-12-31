<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\MonthlyTarget;

class MonthlyTargetController extends Controller
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
            'month' => 'required|date_format:Y-m',
            'target_posts' => 'required|integer|min:0',
            'target_reels' => 'required|integer|min:0',
            'target_boosts' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        // Append day to make it a valid date
        $validated['month'] = $validated['month'] . '-01';

        // Check for duplicates
        if (\App\Models\MonthlyTarget::where('client_id', $validated['client_id'])
            ->where('month', $validated['month'])
            ->exists()) {
            return redirect()->back()->with('error', 'A target for this month already exists!');
        }

        auth()->user()->monthlyTargets()->create($validated);

        return redirect()->back()->with('success', 'Target set successfully.');
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
    public function update(Request $request, MonthlyTarget $monthlyTarget)
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
            'target_posts' => 'required|integer|min:0',
            'target_reels' => 'required|integer|min:0',
            'target_boosts' => 'required|integer|min:0',
            'status' => 'required|in:active,completed,archived', // Allow status update here
            'notes' => 'nullable|string',
        ]);

        $validated['month'] = $validated['month'] . '-01';

        // Prevent setting status to 'completed' if targets are not met
        if ($request->input('status') === 'completed') {
            $actualPosts = $monthlyTarget->client->contents()
                ->whereYear('date', \Carbon\Carbon::parse($validated['month'])->year)
                ->whereMonth('date', \Carbon\Carbon::parse($validated['month'])->month)
                ->where('type', 'Post')
                ->count();

            $actualReels = $monthlyTarget->getActualReels();
            $actualBoosts = $monthlyTarget->getActualBoosts();
                
            $newTargetPosts = $validated['target_posts'];
            $newTargetReels = $validated['target_reels'];
            $newTargetBoosts = $validated['target_boosts'];

            if ($actualPosts < $newTargetPosts || $actualReels < $newTargetReels || $actualBoosts < $newTargetBoosts) {
                return redirect()->back()->withErrors(['status' => 'Cannot mark as completed. Actual content counts must meet the targets.']);
            }
        }

        $monthlyTarget->update($validated);
        
        // Re-check completion status in case targets were LOWERED to meet actuals
        $monthlyTarget->checkCompletionStatus();

        return redirect()->back()->with('success', 'Target updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MonthlyTarget $monthlyTarget)
    {
        $monthlyTarget->delete();
        return redirect()->back()->with('success', 'Target deleted successfully.');
    }
    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
            'target_posts' => 'required|integer|min:0',
            'target_reels' => 'required|integer|min:0',
            'target_boosts' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $month = $validated['month'] . '-01';
        $user = auth()->user();
        $clients = $user->clients;

        foreach ($clients as $client) {
            $client->monthlyTargets()->updateOrCreate(
                ['month' => $month],
                [
                    'user_id' => $user->id,
                    'target_posts' => $validated['target_posts'],
                    'target_reels' => $validated['target_reels'],
                    'target_boosts' => $validated['target_boosts'],
                    'status' => 'active',
                    'notes' => $validated['notes'],
                ]
            );
        }

        return redirect()->back()->with('success', 'Targets updated for all clients.');
    }
}
