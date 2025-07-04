<?php

namespace App\Http\Controllers;

use App\Models\MagazineUser;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MagazineUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $magazineUsers = MagazineUser::paginate(10);
        
        return Inertia::render('MagazineUsers/Index', [
            'magazineUsers' => $magazineUsers
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('MagazineUsers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        MagazineUser::create($validated);

        return redirect()->route('magazine-users.index')
            ->with('success', 'Magazine user created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MagazineUser $magazineUser)
    {
        return Inertia::render('MagazineUsers/Show', [
            'magazineUser' => $magazineUser
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MagazineUser $magazineUser)
    {
        return Inertia::render('MagazineUsers/Edit', [
            'magazineUser' => $magazineUser
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MagazineUser $magazineUser)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $magazineUser->update($validated);

        return redirect()->route('magazine-users.index')
            ->with('success', 'Magazine user updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MagazineUser $magazineUser)
    {
        $magazineUser->delete();

        return redirect()->route('magazine-users.index')
            ->with('success', 'Magazine user deleted successfully.');
    }
}
