<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FcmController extends Controller
{
    public function saveToken(Request $request)
    {
        $request->validate(['token' => 'required']);
        
        if (Auth::check()) {
            Auth::user()->update(['fcm_token' => $request->token]);
            return response()->json(['message' => 'Token saved successfully.']);
        }

        return response()->json(['message' => 'User not authenticated.'], 401);
    }
}
