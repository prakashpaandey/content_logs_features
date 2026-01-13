<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;

class ClientController extends Controller
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
            'name' => 'required|string|max:255',
            'business_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        // Generate initials from name
        $visuals = collect(explode(' ', $validated['name']))->map(function ($segment) {
            return strtoupper(substr($segment, 0, 1));
        })->take(2)->join('');

        Client::create([
            ...$validated,
            'initials' => $visuals,
            'user_id' => auth()->id(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Client created successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Client created successfully.');
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
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        // Generate initials from name
        $visuals = collect(explode(' ', $validated['name']))->map(function ($segment) {
            return strtoupper(substr($segment, 0, 1));
        })->take(2)->join('');

        $client->update([
            ...$validated,
            'initials' => $visuals,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Client updated successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Client updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Client deleted successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Client deleted successfully.');
    }
}
