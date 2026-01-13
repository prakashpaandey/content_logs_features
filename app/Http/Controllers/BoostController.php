<?php

namespace App\Http\Controllers;

use App\Models\Boost;
use App\Models\Client;
use App\Models\MonthlyTarget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoostController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'platform' => 'required|in:Instagram,TikTok,Facebook',
            'boosted_content_type' => 'required|in:Post,Reel',
            'date' => 'required|date',
            'url' => 'nullable|url|max:500',
            'amount' => 'required|numeric|min:0',
            'remarks' => 'nullable|string|max:1000',
        ]);

        
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
        $contentBs = \App\Helpers\NepaliDateHelper::adToBs($date);
        $nowBs = \App\Helpers\NepaliDateHelper::adToBs($now);

        /*
        //Prevent future dates
        if ($date->startOfDay()->gt(\Carbon\Carbon::today())) {
            return redirect()->back()->withInput()->with('error', 'Cannot create boost records for upcoming dates.');
        }
        */

        

        /*
        //Prevent creating boosts for months ahead of the current month
        if ($contentBs['year'] > $nowBs['year'] || ($contentBs['year'] == $nowBs['year'] && $contentBs['month'] > $nowBs['month'])) {
            return redirect()->back()->withInput()->with('error', 'Cannot create boost records for future  months.');
        }
        */

        //Context-based validation: Date must match the dashboard context
        if ($request->has('context_bs_month') && $request->has('context_bs_year')) {
            $contextBsMonth = (int) $request->context_bs_month;
            $contextBsYear = (int) $request->context_bs_year;
            
            if ($contentBs['month'] !== $contextBsMonth || $contentBs['year'] !== $contextBsYear) {
                return redirect()->back()->withInput()->with('error', 'Selected date must match the dashboard month).');
            }
        }

        $boost = Boost::create([
            'user_id' => Auth::id(),
            'client_id' => $validated['client_id'],
            'title' => $validated['title'],
            'platform' => $validated['platform'],
            'boosted_content_type' => $validated['boosted_content_type'],
            'date' => $validated['date'],
            'url' => $validated['url'],
            'amount' => $validated['amount'],
            'remarks' => $validated['remarks'],
        ]);

        // Check and update target status
        $bsDate = \App\Helpers\NepaliDateHelper::adToBs($date);
        $repAd = \App\Helpers\NepaliDateHelper::bsToAd($bsDate['month'], $bsDate['year']);
        
        $target = MonthlyTarget::where('client_id', $validated['client_id'])
            ->whereYear('month', $repAd['year'])
            ->whereMonth('month', $repAd['month'])
            ->first();

        if ($target) {
            $target->checkCompletionStatus();
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Boost record added successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Boost record added successfully.');
    }

    public function update(Request $request, Boost $boost)
    {
        if ($boost->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You are not authorized to edit this boost record.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'platform' => 'required|in:Instagram,TikTok,Facebook',
            'boosted_content_type' => 'required|in:Post,Reel',
            'date' => 'required|date',
            'url' => 'nullable|url|max:500',
            'amount' => 'required|numeric|min:0',
            'remarks' => 'nullable|string|max:1000',
        ]);

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
        if ($date->startOfDay()->gt(\Carbon\Carbon::today())) {
            return redirect()->back()->withInput()->with('error', 'Cannot update boost records to upcoming dates.');
        }
        */

        /*
        //Prevent updating boosts to future months
        if ($date->year > $now->year || ($date->year == $now->year && $date->month > $now->month)) {
            return redirect()->back()->withInput()->with('error', 'Cannot update boost records to future months.');
        }
        */

        $boost->update($validated);

        // Check and update target status
        $bsDate = \App\Helpers\NepaliDateHelper::adToBs($date);
        $repAd = \App\Helpers\NepaliDateHelper::bsToAd($bsDate['month'], $bsDate['year']);
        
        $target = MonthlyTarget::where('client_id', $boost->client_id)
            ->whereYear('month', $repAd['year'])
            ->whereMonth('month', $repAd['month'])
            ->first();

        if ($target) {
            $target->checkCompletionStatus();
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Boost record updated successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Boost record updated successfully.');
    }

    public function destroy(Boost $boost)
    {
        if ($boost->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'You are not authorized to delete this boost record.');
        }

        $clientId = $boost->client_id;
        $date = $boost->date;
        
        $boost->delete();

        // Check and update target status
        $bsDate = \App\Helpers\NepaliDateHelper::adToBs($date);
        $repAd = \App\Helpers\NepaliDateHelper::bsToAd($bsDate['month'], $bsDate['year']);
        
        $target = MonthlyTarget::where('client_id', $clientId)
            ->whereYear('month', $repAd['year'])
            ->whereMonth('month', $repAd['month'])
            ->first();

        if ($target) {
            $target->checkCompletionStatus();
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Boost record deleted successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Boost record deleted successfully.');
    }
}
