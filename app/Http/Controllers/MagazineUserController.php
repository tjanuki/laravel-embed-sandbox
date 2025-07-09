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

    /**
     * Store a newly created resource via public API.
     */
    public function apiStore(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:magazine_users,email',
                'source_data' => 'nullable|array',
            ]);

            // Extract source tracking data from request
            $sourceData = $this->extractSourceData($request);
            
            // Merge with any source data provided in the request
            if (isset($validated['source_data'])) {
                $sourceData = array_merge($sourceData, $validated['source_data']);
            }

            MagazineUser::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'source_data' => $sourceData,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Successfully subscribed to magazine!'
            ])->header('Access-Control-Allow-Origin', '*')
              ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
              ->header('Access-Control-Allow-Headers', 'Content-Type, Accept');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422)->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
                    ->header('Access-Control-Allow-Headers', 'Content-Type, Accept');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request'
            ], 500)->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Methods', 'POST, OPTIONS')
                    ->header('Access-Control-Allow-Headers', 'Content-Type, Accept');
        }
    }

    /**
     * Show the iframe embed form.
     */
    public function embedIframe(Request $request)
    {
        return view('embed.iframe', [
            'apiUrl' => $request->getSchemeAndHttpHost() . '/api/magazine-users'
        ]);
    }

    /**
     * Extract source tracking data from the request.
     */
    private function extractSourceData(Request $request): array
    {
        $sourceData = [];

        // Extract referrer information
        $referrer = $request->header('Referer') ?? $request->header('Referrer');
        if ($referrer) {
            $parsedUrl = parse_url($referrer);
            $sourceData['website'] = $parsedUrl['host'] ?? null;
            $sourceData['url'] = $referrer;
        }

        // Extract user agent
        $userAgent = $request->header('User-Agent');
        if ($userAgent) {
            $sourceData['user_agent'] = $userAgent;
        }

        // Extract IP address
        $ipAddress = $request->ip();
        if ($ipAddress) {
            $sourceData['ip_address'] = $ipAddress;
        }

        // Add timestamp
        $sourceData['timestamp'] = now()->toISOString();

        return $sourceData;
    }
}
