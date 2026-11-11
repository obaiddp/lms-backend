<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use App\Models\GoogleMeetToken;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Laravel\Socialite\Facades\Socialite;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
// use Auth;

class GoogleOAuthService
{
    private $client;
  

    public function __construct()
    {
        
        $this->client = new Client();
        // $this->client->setAuthConfig(storage_path('app/google/credientials.json'));
        // $this->client->setAuthConfig(storage_path('app/google/credientials_2.json'));
        $this->client->setAuthConfig(storage_path('app/google/credientials_4.json'));
        $this->client->addScope(Calendar::CALENDAR);
        $redirectUrl =env('GOOGLE_REDIRECT_URL');
        $this->client->setRedirectUri($redirectUrl);
        // $this->client->addScope(\Google\Service\Calendar::CALENDAR);
   

        // // Load token from database if exists
        // $tokenInfo = GoogleMeetToken::latest()->first();
      
        // if ($tokenInfo) {
        //     $token = $tokenInfo->access_token;
        //     $this->client->setAccessToken($token);
        // }
       
        $this->client->setApplicationName("CEF Online");
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
      
     
    }

    public function getClient()
    {
        return $this->client;
    }
    public function redirectToGoogle()
    {
        $authUrl = $this->getAuthUrl();
        header("Location: $authUrl");
        exit();
        // return Redirect::away($authUrl); // Redirect inside the service
    }

    public function getAuthUrl(): string
    {
      
        return $this->client->createAuthUrl();
    }


    /**
     * Returns the consent URL—and embeds our encrypted user ID in the `state` param.
     */
    public function getAuthUrlFor(User $user): string
    {
        $encrypted = Crypt::encryptString((string) $user->id);
        $this->client->setState($encrypted);
        return $this->client->createAuthUrl();
    }



    public function handleCallback(string $authCode,string $encryptedState): void
    {
      
        // 1) decrypt state → user id
        $userId = (int) Crypt::decryptString($encryptedState);
        $user   = User::findOrFail($userId);

        // 2) fetch tokens
        $token = $this->client->fetchAccessTokenWithAuthCode($authCode);
   
        // 3) persist on that user
        $user->update([
            'instructor_google_access_token'        => $token['access_token'],
            'instructor_google_refresh_token'       => $token['refresh_token'] ?? $user->instructor_google_refresh_token,
            'instructor_google_token_expires_at'    => now()->addSeconds($token['expires_in']),
        ]);

        $result_array = [
            'user_id' => $user->id,
            'access_token' => $token['access_token'],
            'refresh_token' => $token['refresh_token'] ?? null,
            'expires_in' => $token['expires_in'],
        ];
        // auth()->user()->update([
        //     'instructor_google_access_token' => $token['access_token'],
        //     'instructor_google_refresh_token' => $token['refresh_token'], // Save refresh token
        //     'instructor_google_token_expires_at' => now()->addSeconds($token['expires_in']),
        // ]);
       
        //    $result_array = [
        //     'user_id' => auth()->user()->id,
        //     'access_token' => $token['access_token'],
        //     'refresh_token' => $token['refresh_token'],
        //     'expires_in' => $token['expires_in'],
        // ];

        $this->storeToken($result_array);
        // $this->storeToken($token);
    }






    //  public function handleCallback(string $authCode): void
    // {
      
        
    //     // $googleUser = Socialite::driver('google')->stateless()->user();
    //     $token = $this->client->fetchAccessTokenWithAuthCode($authCode);
    //     auth()->user()->update([
    //         'instructor_google_access_token' => $token['access_token'],
    //         'instructor_google_refresh_token' => $token['refresh_token'], // Save refresh token
    //         'instructor_google_token_expires_at' => now()->addSeconds($token['expires_in']),
    //     ]);
       


    //     // $token = $this->client->fetchAccessTokenWithAuthCode($authCode);
    //     $result_array = [
    //         'user_id' => auth()->user()->id,
    //         'access_token' => $token['access_token'],
    //         'refresh_token' => $token['refresh_token'],
    //         'expires_in' => $token['expires_in'],
    //     ];
    //     $this->storeToken($result_array);
    //     // $this->storeToken($token);
    // }

    private function storeToken(array $token): void
    {
      
       
        $newToken = new GoogleMeetToken;
        // $newToken->user_id = auth()->id();
        $newToken->user_id = $token['user_id'];
        $newToken->access_token = $token['access_token'];
        $newToken->refresh_token = $token['refresh_token'] ?? null;
        $newToken->expires_at = now()->addSeconds($token['expires_in']);
        $newToken->save();
    }

    public function refreshTokenIfNeeded(User $user)
    {
    
        if($user == null){
            return 'null_tutor';
        }
        if (!$user->instructor_google_access_token) {
            return 'no_access_token';
        }
      
        if (($user->instructor_google_token_expires_at) > now()) {
            $this->client->setAccessToken($user->instructor_google_access_token);
            return true;
        }
        else{  // Current Select Token Has Time Expired
            // Request new access token
          
            $newToken = $this->client->fetchAccessTokenWithRefreshToken($user->instructor_google_refresh_token);
            if(!empty($newToken)){
                if(isset($newToken['error'])){
                    Log::info("ERROR IN (refreshTokenIfNeeded) IN APP/SERVICES/GOOGLEOAUTH AT ".Carbon::now());
                    Log::info("ERROR DETAILS:- ".$newToken['error']);
                    return 'no_refresh_access_token';
                }
                else{
                    $token = $newToken['access_token'];
                    $newToken['user_id'] = $user->id;
                    $this->client->setAccessToken($token);
                    $user->update([
                        'instructor_google_access_token' => $newToken['access_token'],
                        'instructor_google_refresh_token' => $newToken['refresh_token'] ?? null, // 👈 Keep old if missing
                        'instructor_google_token_expires_at' => Carbon::now()->addSeconds($newToken['expires_in']),
                    ]);
                    $this->storeToken($newToken);
                    return true;
                }    
            }
            else{
                Log::info("ERROR IN (refreshTokenIfNeeded) IN APP/SERVICES/GOOGLEOAUTH AT ".Carbon::now());
                Log::info("ERROR DETAILS:- no_refresh_access_token");
                return 'no_refresh_access_token'; 
            }


            // $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            //     'client_id' => config('services.google.client_id'),
            //     'client_secret' => config('services.google.client_secret'),
            //     'refresh_token' => $user->instructor_google_refresh_token,
            //     'grant_type' => 'refresh_token',
            // ]);
            // if ($response->successful()) {
            //     $data = $response->json();
             
            //     $refreshToken=$data['refresh_token'] ?? $user->instructor_google_refresh_token;
            //     $user->update([
            //         'instructor_google_access_token' => $data['access_token'],
            //         'instructor_google_refresh_token' => $data['refresh_token'] ?? $user->instructor_google_refresh_token, // 👈 Keep old if missing
            //         'instructor_google_token_expires_at' => Carbon::now()->addSeconds($data['expires_in']),
            //     ]);
            //     $data['refresh_token'] = $refreshToken;
            //     $data['user_id'] = $user->id;
            //     $this->storeToken($data);
            //     $this->client->setAccessToken($data['access_token']);
            //     return true;
            // }
            // else{
            //     return 'no_refresh_access_token';
            // }
        }

   
        // $tokenRecord = GoogleMeetToken::latest()->first();
        // if (!$tokenRecord) {
        //     $generatedFormUrl = $this->getAuthUrl();
        //     header("Location: $generatedFormUrl");
        //     exit();
        // }
        // if (now()->gt($tokenRecord->expires_at)) { // Current Select Token Has Time Expired
        //     $newToken = $this->client->fetchAccessTokenWithRefreshToken($tokenRecord->refresh_token);
          
        //     if(!empty($newToken)){
        //         if(isset($newToken['error'])){
        //             $generatedFormUrl = $this->getAuthUrl();
        //             header("Location: $generatedFormUrl");
        //             exit();
        //         }
        //         else{
        //             $token = $newToken['access_token'];
        //             $this->client->setAccessToken($token);
        //         }    
        //     }
        //     $this->storeToken($newToken);
        //     return true;
        // }
        // else{ // Current Select Token Has Time Left
        //         $token = $tokenRecord->access_token;
        //         $this->client->setAccessToken($token);
        //         return true;
        // }
    }



}