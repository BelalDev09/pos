<?php

namespace App\Http\Controllers\API\user;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Certification;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    use apiresponse;
    public function index()
    {
        $user = auth()->user()->load('certifications');

        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avartar_url,
            'birth_country' => $user->birth_country,
            'address' => $user->address,
            'description' => $user->description,
            'phone' => $user->phone,
        ];

        return $this->success($data, 'Profile Fetched Successfully', 200);
    }

    public function update(Request $request)
    {
        try {
            $user = auth()->user();

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'birth_country' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'phone' => 'nullable|string|max:20',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            if ($request->hasFile('avatar')) {
                if ($user->avatar && file_exists(public_path($user->avatar))) {
                    unlink(public_path($user->avatar));
                }

                $avatarName = Helper::fileUpload($request->file('avatar'), 'user', $user->last_name);

                $validated['avatar'] = $avatarName;
            }

            $user->update($validated);

            $data = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar_url' => $user->avartar_url,
                'birth_country' => $user->birth_country,
                'description' => $user->description,
                'address' => $user->address,
                'phone' => $user->phone,
            ];

            return $this->success($data, 'Profile updated successfully.', 200);
        } catch (\Exception $e) {
            return $this->error([], [$e->getMessage()], 500);
        }
    }
}
