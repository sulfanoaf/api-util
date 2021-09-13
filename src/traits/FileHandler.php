<?php

namespace DAI\Utils\Traits;

use Exception;
use Illuminate\Support\Facades\Storage;

trait FileHandler
{
    private function getFileExtension($file_name)
    {
        return substr(strrchr($file_name, '.'), 1);
    }

    private function disk()
    {
        return Storage::disk(env('ASSET_STORAGE', env('FILESYSTEM_DRIVER', 'public')));
    }

    public function saveFile($file, $filename = null, $dirname = null, $time = null, $index = 1)
    {
        ini_set('memory_limit', '-1');
        $time = $time ? $time : time();
        $original_name = $file->getClientOriginalName();
        $extension = $this->getFileExtension(($original_name));
        $filename = $filename ? $filename . "-" . $time . "-" . $index . "." . $extension : $time . "-" + $original_name;
        $uploaded_path = $this->disk()->putFileAs($dirname, $file, $filename);

        return $uploaded_path;
    }

    public function saveFiles($files, $filename = null, $dirname = null)
    {
        ini_set('memory_limit', '-1');
        $time = time();
        $uploaded_path = [];
        foreach ($files as $index => $file) {
            $uploaded_path[] = $this->saveFile($file, $filename, $dirname, $time, $index);
        }

        return $uploaded_path;
    }

    public function saveBase64($file, $filename, $dirname = null)
    {
        $time = time();
        $encoded_file = $file;
        $file_parts = explode(',', $encoded_file);
        $base64_header = $file_parts[0];
        $decoded_body = base64_decode($file_parts[1]);

        $base64_header_parts = explode(';', $base64_header);
        $mimetype_parts = explode('/', $base64_header_parts[0]);
        $file_type = $mimetype_parts[1];

        $filepath = '';
        if (!is_null($dirname)) {
            $filepath .= $dirname . '/';
        }
        $uploaded_path = $filepath . $time . "-" . $filename . "." . $file_type;
        $this->disk()->put($uploaded_path, $decoded_body);
        return $uploaded_path;
    }


    public function pathFile($file)
    {
        $disk = env('ASSET_STORAGE', env('FILESYSTEM_DRIVER', 'public'));
        $path = '';
        if (!is_null($file) && $file != '') {
            $file_exists = $this->disk()->exists($file);
            if ($file_exists) {
                if ($disk != 'public' && $disk != 'local') {
                    $path = $this->disk()->url($file);
                } else {
                    $path = $this->disk()->path($file);
                }
            }
        }

        return $path;
    }

    public function viewFile($file)
    {
        try {
            $path = $this->pathFile($file);
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                $path = str_replace('\\', '/', $path);
                $headers = get_headers($path, 1);
                $type = $headers['Content-Type'];
                header("Content-type:$type");
                ob_clean();
                readfile($path);

                return;
            }
            $mime = mime_content_type($path);
            header("Content-type:$mime");
            readfile($path);
        } catch (Exception $e) {
            $path = public_path('no-image.png');
            $mime = mime_content_type($path);
            header("Content-type:$mime");
            readfile($path);
        }
    }

    public function deleteFile($stored_file)
    {
        $this->disk()->delete($stored_file);
    }

    public function downloadFile($file)
    {
        return $this->disk()->download($file);
    }
}
