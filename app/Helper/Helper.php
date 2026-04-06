<?php

namespace App\Helper;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class Helper
{

    // Upload Image
    public static function fileUpload($file, $folder, $name)
    {
        $imageName = Str::slug($name) . '.' . $file->extension();
        $file->move(public_path('uploads/' . $folder), $imageName);
        $path = 'uploads/' . $folder . '/' . $imageName;
        return $path;
    }
    //tableCheckbox
    public static function tableCheckbox($row_id)
    {
        return '<div class="form-checkbox">
                <input type="checkbox" class="form-check-input select_data" id="checkbox-' . $row_id . '" value="' . $row_id . '" onClick="select_single_item(' . $row_id . ')">
                <label class="form-check-label" for="checkbox-' . $row_id . '"></label>
            </div>';
    }

    //video upload
    public static function videoUpload($file, $folder, $name)
    {
        $videoName = Str::slug($name) . '.' . $file->extension();
        $file->move(public_path('uploads/' . $folder), $videoName);
        $path = 'uploads/' . $folder . '/' . $videoName;
        return $path;
    }

    // audio upload
    public static function audioUpload($file, $folder, $name)
    {
        $audioName = Str::slug($name) . '.' . $file->extension();
        $file->move(public_path('uploads/' . $folder), $audioName);
        $path = 'uploads/' . $folder . '/' . $audioName;
        return $path;
    }

    public static function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    public static function deleteFile($filePath)
    {
        if (!$filePath) {
            return false;
        }

        try {
            // Normalize path (remove domain if full URL)
            if (filter_var($filePath, FILTER_VALIDATE_URL)) {
                $path = parse_url($filePath, PHP_URL_PATH);
                $path = ltrim($path, '/');
            } else {
                $path = ltrim($filePath, '/');
            }

            // Security check – allow only uploads folder
            if (!Str::startsWith($path, 'uploads/')) {
                Log::warning('Blocked suspicious local delete path: ' . $path);
                return false;
            }

            $fullPath = public_path($path);

            if (File::exists($fullPath)) {
                File::delete($fullPath);
                Log::info('Local file deleted: ' . $fullPath);
                return true;
            }

            // File already deleted
            Log::info('Local file not found: ' . $fullPath);
            return true;

        } catch (\Exception $e) {
            Log::error('Local file delete failed: ' . $e->getMessage(), [
                'path' => $filePath
            ]);
            return false;
        }
    }

    public static function notifyAgent(string $type, string $message, ?int $relatedId = null)
    {
        $user = Auth::user(); 

        Notification::create([
            'user_id' => $user->id,
            'slug' => Str::random(16) . '-' . time(),
            'type' => $type,
            'message' => $message,
            'is_read' => false,
            'status' => 'active',
        ]);
    }



}
