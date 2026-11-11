<?php

namespace App\Services;

use App\Models\GoogleMeetToken;
use App\Models\User;

class InstructorGoogleAuthService
{
    /**
     * Clear Google Meet OAuth tokens for an instructor user (same as admin "Remove Google Authorization").
     */
    public function clearForUser(User $user): void
    {
        GoogleMeetToken::where('user_id', $user->id)->delete();

        $user->update([
            'instructor_google_access_token' => null,
            'instructor_google_refresh_token' => null,
            'instructor_google_token_expires_at' => null,
        ]);
    }
}
