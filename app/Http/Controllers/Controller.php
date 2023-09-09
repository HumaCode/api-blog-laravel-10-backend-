<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use PharIo\Manifest\Url;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function saveImage($image, $path = 'public')
    {
        if (!$image || !base64_decode($image, true)) {
            return null;
        }

        // Ambil ekstensi gambar dari data gambar yang diterima
        $imageData = base64_decode($image);
        $extension = $this->getImageExtension($imageData);

        // Generate filename with the correct extension
        $filename = time() . '.' . $extension;

        try {
            // Simpan gambar ke penyimpanan
            Storage::disk($path)->put($filename, base64_decode($image));

            // Dapatkan URL gambar yang disimpan
            $url = Storage::url($filename);

            return $url;
        } catch (\Exception $e) {
            \Log::error('Gagal menyimpan gambar: ' . $e->getMessage());
            return null;
        }
    }

    // Fungsi untuk mendapatkan ekstensi gambar
    private function getImageExtension($imageData)
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($imageData);

        switch ($mime) {
            case 'image/png':
                return 'png';
            case 'image/jpeg':
                return 'jpg';
            case 'image/gif':
                return 'gif';
            default:
                return 'jpg'; // Default extension jika tidak dikenali
        }
    }
}
