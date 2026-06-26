<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait UploadFileTrait
{
    public static function uploadFile($requestFile, $folder , $disk = 'public', $filename = null)
    {
        ini_set('memory_limit', '256M');
        try {
            $fileName = !is_null($filename) ? $filename : Str::random(10);
            $extension = $requestFile->getClientOriginalExtension();
            $fullFileName = $fileName . "." . $extension;

            $uploadPath = $folder;

            $subdomain = \App\Support\TenantContext::subdomain();
            if ($subdomain) {
                $uploadPath .= "/{$subdomain}";
            }
            // Tạo thư mục nếu chưa tồn tại
            if (!file_exists(public_path("storage/{$uploadPath}"))) {
                mkdir(public_path("storage/{$uploadPath}"), 0775, true);
            }

            // Upload file
            $requestFile->move(public_path("storage/{$uploadPath}"), $fullFileName);

            // Trả về đường dẫn tương đối
            return "storage/{$uploadPath}/{$fullFileName}";

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $e->getMessage();
        }
    }

    public function deleteFiles(array $fileNames, $disk = 'public')
    {
        try {
            if ($fileNames) {
                foreach ($fileNames as $fileName) {
                    $this->deleteFile($fileName, $disk);
                }
            }
            return true;
        } catch (\Exception $e) {
            report($e);
            return $e->getMessage();
        }
    }

    public static function deleteFile($fileName, $disk = 'public')
    {
        try {
            if ($fileName) {
                $filePath = str_replace('storage/', '', $fileName);
                $fullPath = public_path("storage/{$filePath}");
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }
            return true;
        } catch (\Exception $e) {
            report($e);
            return $e->getMessage();
        }
    }
}