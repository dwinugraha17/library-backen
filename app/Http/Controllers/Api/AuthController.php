<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Rate limiting for registration
        $key = 'register_' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'message' => 'Too many registration attempts. Please try again later.'
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone_number' => 'required|string|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone_number' => $request->phone_number,
            'role' => 'user',
        ]);

        // Clear the rate limiter after successful registration
        RateLimiter::clear($key);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request)
    {
        // Rate limiting for login
        $key = 'login_' . $request->ip() . '_' . $request->email;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'message' => 'Too many login attempts. Please try again later.'
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            // Increment rate limiter on failed attempt
            RateLimiter::hit($key);
            return response()->json(['message' => 'Invalid login credentials'], 401);
        }

        // Clear rate limiter on successful login
        RateLimiter::clear($key);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $rules = [
            'name' => 'sometimes|string|max:255',
            'phone_number' => 'sometimes|string|max:15',
        ];

        // Only validate profile_photo if it's present in the request
        if ($request->hasFile('profile_photo')) {
            $rules['profile_photo'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->has('name')) $user->name = $request->name;
        if ($request->has('phone_number')) $user->phone_number = $request->phone_number;

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');

            // Validate the file is a proper image
            if (!$file->isValid()) {
                return response()->json(['message' => 'Uploaded file is not valid'], 422);
            }

            // Check if it's actually an image
            $imageInfo = getimagesize($file->getRealPath());
            if (!$imageInfo) {
                return response()->json(['message' => 'Uploaded file is not a valid image'], 422);
            }

            // Allowed image types
            $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF];
            if (!in_array($imageInfo[2], $allowedTypes)) {
                return response()->json(['message' => 'Only JPEG, PNG, and GIF images are allowed'], 422);
            }

            // Delete old file if exists
            if ($user->profile_photo) {
                // If it's a full URL, parse it. If relative, use directly.
                $oldPath = $user->profile_photo;
                if (filter_var($oldPath, FILTER_VALIDATE_URL)) {
                    $oldPath = 'profiles/' . basename(parse_url($oldPath, PHP_URL_PATH));
                }

                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                }
            }

            // Store the file and save relative path
            $path = $file->store('profiles', 'public');
            $user->profile_photo = $path;
            \Log::info('Profile photo updated for user ' . $user->id . ': ' . $path);
        }

        $user->save();
        $user->refresh(); // Ambil data terbaru dari DB

        return response()->json(['user' => $user]);
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();
        
        // Revoke all tokens
        $user->tokens()->delete();
        
        // Delete user
        $user->delete();

        return response()->json(['message' => 'Account deleted successfully']);
    }
}
