<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleOAuthService;

class GoogleOAuthController extends Controller
{
    private $googleOAuthService;

    public function __construct(GoogleOAuthService $googleOAuthService)
    {
        $this->googleOAuthService = $googleOAuthService;
    }

    public function redirect()
    {
        $authUrl = $this->googleOAuthService->getAuthUrl();
        return redirect($authUrl);
    }

    public function callback(Request $request)
    {
        $this->googleOAuthService->handleCallback($request->get('code'));
        // classes.create_individual
        return redirect()->route('classes.create_individual');
        return redirect('/dashboard'); // or wherever you want to redirect after auth
    }
}
