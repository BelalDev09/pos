<?php

namespace App\Http\Controllers\API\User;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    use apiresponse;

    public function index()
    {
        $user = auth()->user();

        if ($user) {
            $user = $user->only(['name', 'email', 'avartar', 'birth_country', 'description', 'phone']);
            return $this->success($user, 'User fetched successfully', 200);
        }

        return $this->error([], 'User not authenticated', 401);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'birth_country'  => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'phone'          => 'nullable|string|max:20',
            'avatar'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                @unlink(public_path($user->avatar));
            }

            $avatarName = Helper::fileUpload($request->file('avatar'), 'user', $user->last_name);
            $validated['avatar'] = $avatarName;
        }

        // Update user with validated fields
        $user->update($validated);

        // Return updated user info (excluding email)
        return $this->success(
            $user->only([
                'name',
                'avartar_url',
                'birth_country',
                'description',
                'phone',
            ]),
            'User updated successfully',
            200
        );
    }



    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->error([], 'Current password is incorrect', 400);
        }
        $user->update([
            'password' => bcrypt($request->new_password),
        ]);

        return $this->success([], 'Password changed successfully', 200);
    }

    public function destroy(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validation->fails()) {
            return $this->error([], $validation->errors(), 500);
        }
        $user = auth()->user();

        if ($user->email != $request->email) {
            return $this->error([], 'Email does not match', 500);
        }
        $user->delete();
        return $this->success([], 'Account deleted successfully', 200);
    }
}
