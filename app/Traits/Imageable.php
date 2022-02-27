<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait Imageable{

    public function imageUpload($img_src, $location, $s1, $s2){
        $path = $location.'/'.date('Y').'/'.date('m').'/';
        $image = Image::make($img_src);
        $image->encode('png');
        $imageName = str_random(30);

        //Image size original
        Storage::disk('local')->put($path . $imageName . '.png', (string)$image->encode());

        //Image size $s1
        $image->fit($s1, $s1);
        Storage::disk('local')->put($path .$imageName . '_' . $s1 . 'x' . $s1 . '.png', (string)$image->encode());

        //Image size $s2
        $image->fit($s2, $s2);
        Storage::disk('local')->put($path . $imageName . '_' . $s2 . 'x' . $s2 . '.png', (string)$image->encode());

        return $imageName = env('APP_PATH_UPLOADS') . $path . $imageName . '.png';
    }

}
