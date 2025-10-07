<?php

namespace App\Helpers;

use App\Models\Image;
use Exception;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as FacadeImage;

class Images
{
    public function uploadImage($imagen, $folder)
    {
        $image = FacadeImage::make($imagen);
        $image->encode('webp');
        $nombreImagen = $folder . '/' . uniqid() . uniqid() . '.webp';
        Storage::disk('public')->put($nombreImagen, $image->stream(), 'public');
        return $nombreImagen;
    }

    public function convertUrlToBase64()
    {
        return base64_encode(file_get_contents(request()->file('image')));
    }

}
