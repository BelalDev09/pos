<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Services\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\AuthorizesRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ProfileSettingController extends Controller
{
    use AuthorizesRequest;

    public function index(Request $request)
    {
        // Note: Users can only view their own profile settings
        return view('backend.layout.setting.profileSettings');
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
        ], [
            'name.required'     => 'The name field is required.',
            'name.string'       => 'The name must be a valid string.',
            'name.max'          => 'The name must not exceed 255 characters.',

            'username.required' => 'The username field is required.',
            'username.string'   => 'The username must be a valid string.',
            'username.max'      => 'The username must not exceed 255 characters.',
            'username.unique'   => 'The username has already been taken.',

            'email.required'    => 'The email field is required.',
            'email.string'      => 'The email must be a valid string.',
            'email.email'       => 'Please enter a valid email address.',
            'email.max'         => 'The email must not exceed 255 characters.',
            'email.unique'      => 'The email address has already been taken.',
        ]);


        if ($validator->fails()) {
            return back()
                ->withInput()
                ->with([
                    'error' => $validator->errors()->first(),
                    'type'  => 'profile'
                ]);
        }

        try {
            User::findOrFail(Auth::id())->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            return back()->with('success', 'Profile updated successfully.');
        } catch (Throwable $e) {
            Log::error('Profile update failed', [
                'user_id' => Auth::id(),
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Profile was not updated. Reason: ' . $e->getMessage());
        }
    }

    public function updatePassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'password' => 'required|string|confirmed|min:6',
        ], [
            'old_password.required' => 'Current password is required.',
            'password.required' => 'New password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 6 characters.',
        ]);


        if ($validator->fails()) {
            return back()->with([
                'error' => $validator->errors()->first(),
                'type'  => 'password'
            ]);
        }

        // dd($validatedData);

        try {

            if (! Hash::check($request->old_password, Auth::user()->password)) {
                return back()->with([
                    'error' => 'Current password does not match.',
                    'type' => 'password',
                ]);
            }

            $user = User::findOrFail(Auth::id());
            $user->password = Hash::make($request->password);
            $user->save();

            return redirect()->route('admin.profile')->with('success', 'Password updated successfully.');
        } catch (Throwable $e) {
            Log::error('Password update failed', [
                'user_id' => Auth::id(),
                'message' => $e->getMessage(),
            ]);

            return back()->with([
                'error' => 'Password was not updated. Reason: ' . $e->getMessage(),
                'type' => 'password',
            ]);
        }
    }

    public function updateProfilePicture(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ], [
            'avatar.required' => 'Please select an image.',
            'avatar.image' => 'The file must be an image.',
            'avatar.mimes' => 'Only jpeg, png, jpg, gif, svg formats are allowed.',
            'avatar.max' => 'Image size must not exceed 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        try {

            $user = User::findOrFail(Auth::id());

            if ($request->hasFile('avatar')) {

                // delete old file
                if ($user->avatar && file_exists(public_path($user->avatar))) {
                    @unlink(public_path($user->avatar));
                }

                $path = Service::fileUpload($request->file('avatar'), 'avatars/admins/');

                $user->update([
                    'avatar' => $path
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Profile picture updated successfully',
                    'image_url' => asset($path)
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No file uploaded'
            ], 422);
        } catch (Throwable $e) {
            Log::error('Profile picture update failed', [
                'user_id' => Auth::id(),
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Profile picture was not updated. Reason: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkusername(Request $request)
    {
        $input = $request->input('input');

        $currentUserId = Auth::id();

        $exists = User::where('username', $input)
            ->where('id', '!=', $currentUserId)
            ->exists();

        return response()->json([
            'exists' => $exists,
            'input' => $input
        ]);
    }
}
