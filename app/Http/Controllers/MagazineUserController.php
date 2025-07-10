<?php

namespace App\Http\Controllers;

use App\Models\MagazineUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class MagazineUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $magazineUsers = MagazineUser::latest()->paginate(20);

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
            // Validate referrer domain
            if (!$this->validateReferrer($request)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied: Invalid referrer domain'
                ], 403);
            }

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
        // Validate referrer domain
        if (!$this->validateReferrer($request)) {
            abort(403, 'Access denied: Invalid referrer domain');
        }

        return view('embed.iframe', [
            'apiUrl' => $request->getSchemeAndHttpHost() . '/api/magazine-users'
        ]);
    }

    /**
     * Serve the JavaScript embed widget.
     */
    public function embedJavaScript(Request $request)
    {
        // Validate referrer domain
        if (!$this->validateReferrer($request)) {
            // Log the blocked embed attempt
            Log::warning('Embed JavaScript blocked', [
                'type' => 'javascript_widget',
                'referrer' => $request->header('Referer') ?? $request->header('Referrer'),
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'requested_url' => $request->fullUrl(),
                'timestamp' => now()->toISOString(),
            ]);

            // Return JavaScript that shows an error message
            $errorJs = "
                (function() {
                    var container = document.getElementById('magazine-subscription-form');
                    if (container) {
                        container.innerHTML = '<div style=\"padding: 20px; background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; color: #dc2626; text-align: center; font-family: system-ui, -apple-system, sans-serif;\"><h4 style=\"margin: 0 0 8px 0; font-size: 16px;\">Access Denied</h4><p style=\"margin: 0; font-size: 14px;\">This embed widget is not authorized for this domain. Please contact the site administrator.</p></div>';
                    }
                })();
            ";
            return response($errorJs, 403)
                ->header('Content-Type', 'application/javascript');
        }

        $apiUrl = $request->getSchemeAndHttpHost() . '/api/magazine-users';
        
        $js = view('embed.magazine-embed', compact('apiUrl'))->render();
        
        return response($js)
            ->header('Content-Type', 'application/javascript')
            ->header('Cache-Control', 'public, max-age=3600')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET')
            ->header('Access-Control-Allow-Headers', 'Content-Type');
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

    /**
     * Validate if the request comes from an allowed referrer domain.
     */
    private function validateReferrer(Request $request): bool
    {
        $allowedReferers = config('embed.allowed_referers', []);

        // If no allowed referers are configured, allow all requests
        if (empty($allowedReferers)) {
            return true;
        }

        // Bypass validation in development mode if configured
        if (config('embed.bypass_in_development', false)) {
            return true;
        }

        // Get referrer from request headers
        $referrer = $request->header('Referer') ?? $request->header('Referrer');

        if (!$referrer) {
            // Log missing referrer if logging is enabled
            if (config('embed.log_violations', false)) {
                Log::warning('Embed request blocked: Missing referrer header', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'url' => $request->fullUrl(),
                ]);
            }
            return false;
        }

        // Parse the referrer URL to get the domain
        $parsedUrl = parse_url($referrer);
        $referrerDomain = $parsedUrl['host'] ?? null;

        if (!$referrerDomain) {
            // Log invalid referrer if logging is enabled
            if (config('embed.log_violations', false)) {
                Log::warning('Embed request blocked: Invalid referrer format', [
                    'referrer' => $referrer,
                    'ip' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'url' => $request->fullUrl(),
                ]);
            }
            return false;
        }

        // Check if the referrer domain is in the allowed list
        foreach ($allowedReferers as $allowedDomain) {
            if ($this->domainMatches($referrerDomain, $allowedDomain)) {
                return true;
            }
        }

        // Log security violation if logging is enabled
        if (config('embed.log_violations', false)) {
            Log::warning('Embed request blocked: Referrer domain not allowed', [
                'referrer_domain' => $referrerDomain,
                'referrer' => $referrer,
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'url' => $request->fullUrl(),
                'allowed_referers' => $allowedReferers,
            ]);
        }

        return false;
    }

    /**
     * Check if a domain matches the allowed pattern (supports wildcards).
     */
    private function domainMatches(string $domain, string $pattern): bool
    {
        // Convert both to lowercase for case-insensitive comparison
        $domain = strtolower($domain);
        $pattern = strtolower($pattern);

        // Exact match
        if ($domain === $pattern) {
            return true;
        }

        // Wildcard match (e.g., *.example.com matches subdomain.example.com)
        if (str_starts_with($pattern, '*.')) {
            $baseDomain = substr($pattern, 2);

            // Check if domain ends with the base domain
            if (str_ends_with($domain, '.' . $baseDomain)) {
                return true;
            }

            // Check if domain exactly matches the base domain (for *.example.com matching example.com)
            if ($domain === $baseDomain) {
                return true;
            }
        }

        return false;
    }
}
