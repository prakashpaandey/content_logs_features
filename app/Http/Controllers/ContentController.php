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

        auth()->user()->contents()->create($validated);

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

        $content->update($validated);

        return redirect()->back()->with('success', 'Content updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $content)
    {
        $content->delete();
        return redirect()->back()->with('success', 'Content deleted successfully.');
    }
}
