<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\URL;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function saveImage($image, $path = 'public')
    {
        if (!$image) {
            return null;
        }

        // Ambil ekstensi gambar dari data gambar yang diterima
        $imageData = base64_decode($image);
        $extension = $this->getImageExtension($imageData);
        $filename = time() . '.' . $extension;

        // $filename = time() . '.png';
        \Storage::disk($path)->put($filename, base64_decode($image));

        return URL::to('/') . '/storage/' . $path . '/' . $filename;
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
