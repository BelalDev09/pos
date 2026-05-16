<?php

namespace App\Http\Controllers\Web\Backend;

use App\Helper\Helper;
use App\Models\SystemSetting;
use App\Services\Service;
use App\Services\SettingService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SettingController extends Service
{
    public SettingService $settingServiceObj;

    public function __construct()
    {
        $this->settingServiceObj = new SettingService();
    }

    public function adminSetting()
    {
        return $this->settingServiceObj->adminSettingPage();
    }

    public function adminSettingUpdate(Request $request)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'admin_title'          => 'required|string|max:150',
            'admin_short_title'    => 'nullable|string|max:100',
            'admin_copyright_text' => 'nullable|string|max:500',

            'admin_logo'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'admin_favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico,webp|max:512',
        ], [
            'admin_logo.image'    => 'Admin logo must be an image file.',
            'admin_logo.mimes'    => 'Admin logo must be: jpeg, png, jpg, gif, svg, or webp.',
            'admin_logo.max'      => 'Admin logo size must not exceed 2MB.',
            'admin_favicon.image' => 'Admin favicon must be an image file.',
            'admin_favicon.mimes' => 'Admin favicon must be: jpeg, png, jpg, gif, svg, ico, or webp.',
            'admin_favicon.max'   => 'Admin favicon size must not exceed 512KB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        try {
            $setting = SystemSetting::firstOrNew([]);

            $data                  = $request->only(['admin_title', 'admin_short_title', 'admin_copyright_text']);
            $data['admin_title']   = Str::title($request->admin_title);

            if ($request->hasFile('admin_logo')) {

                Helper::deleteFile($setting->getRawAdminLogo());
                $data['admin_logo'] = Helper::fileUpload(
                    $request->file('admin_logo'),
                    'systems/logo',
                    'admin-logo'
                );
            }


            if ($request->hasFile('admin_favicon')) {
                Helper::deleteFile($setting->getRawAdminFavicon());
                $data['admin_favicon'] = Helper::fileUpload(
                    $request->file('admin_favicon'),
                    'systems/favicon',
                    'admin-favicon'
                );
            }

            $setting->fill($data)->save();

            return redirect()->back()->with('success', 'Updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function systemSetting()
    {
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403);
        }

        $data['setting'] = SystemSetting::firstOrNew([]);

        return view('backend.layout.setting.system-setting')->with($data);
    }

    public function systemSettingUpdate(Request $request)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'system_title'       => 'required|string|max:150',
            'system_short_title' => 'nullable|string|max:100',
            'tag_line'           => 'nullable|string|max:255',
            'company_name'       => 'required|string|max:150',
            'phone_code'         => 'required|string|max:5',
            'phone_number'       => 'required|string|max:15|regex:/^\d+$/',
            'email'              => 'required|email|max:150',
            'copyright_text'     => 'nullable|string|max:500',
        ], [
            'logo.image'    => 'Logo must be an image file.',
            'logo.mimes'    => 'Logo must be: jpeg, png, jpg, gif, svg, or webp.',
            'logo.max'      => 'Logo size must not exceed 2MB.',
            'favicon.image' => 'Favicon must be an image file.',
            'favicon.mimes' => 'Favicon must be: jpeg, png, jpg, gif, svg, ico, or webp.',
            'favicon.max'   => 'Favicon size must not exceed 512KB.',
            'phone_number.regex' => 'Phone number must contain only digits.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        try {
            $setting = SystemSetting::firstOrNew([]);

            $data = $request->only([
                'system_title',
                'system_short_title',
                'tag_line',
                'company_name',
                'phone_code',
                'phone_number',
                'email',
                'copyright_text',
            ]);
            $data['system_title'] = Str::title($request->system_title);

            if ($request->hasFile('logo')) {
                Helper::deleteFile($setting->getRawLogo());
                $data['logo'] = Helper::fileUpload(
                    $request->file('logo'),
                    'systems/logo',
                    'logo'
                );
            }

            if ($request->hasFile('favicon')) {
                Helper::deleteFile($setting->getRawFavicon());
                $data['favicon'] = Helper::fileUpload(
                    $request->file('favicon'),
                    'systems/favicon',
                    'favicon'
                );
            }

            $setting->fill($data)->save();

            return redirect()->back()->with('success', 'Updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function mail()
    {
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403);
        }

        return view('backend.layout.setting.mail');
    }

    public function mailstore(Request $request)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403);
        }

        $request->validate([
            'mail_mailer'       => 'required|string',
            'mail_host'         => 'required|string',
            'mail_port'         => 'required|string',
            'mail_username'     => 'nullable|string',
            'mail_password'     => 'nullable|string',
            'mail_encryption'   => 'nullable|string',
            'mail_from_address' => 'required|string',
        ]);

        try {
            $envContent = File::get(base_path('.env'));
            $lineBreak  = "\n";

            $envContent = preg_replace([
                '/MAIL_MAILER=(.*)\s/',
                '/MAIL_HOST=(.*)\s/',
                '/MAIL_PORT=(.*)\s/',
                '/MAIL_USERNAME=(.*)\s/',
                '/MAIL_PASSWORD=(.*)\s/',
                '/MAIL_ENCRYPTION=(.*)\s/',
                '/MAIL_FROM_ADDRESS=(.*)\s/',
            ], [
                'MAIL_MAILER='       . $request->mail_mailer       . $lineBreak,
                'MAIL_HOST='         . $request->mail_host         . $lineBreak,
                'MAIL_PORT='         . $request->mail_port         . $lineBreak,
                'MAIL_USERNAME='     . $request->mail_username     . $lineBreak,
                'MAIL_PASSWORD='     . $request->mail_password     . $lineBreak,
                'MAIL_ENCRYPTION='   . $request->mail_encryption   . $lineBreak,
                'MAIL_FROM_ADDRESS=' . '"' . $request->mail_from_address . '"' . $lineBreak,
            ], $envContent);

            if ($envContent !== null) {
                File::put(base_path('.env'), $envContent);
            }

            return back()->with('success', 'Updated successfully');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to update');
        }
    }
}
