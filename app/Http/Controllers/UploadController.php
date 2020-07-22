<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        if(!is_dir(storage_path('app/public/img'))){
            mkdir(storage_path('app/public/img'));
        }
        $image = request()->file('file');

        if(!in_array($image->getClientMimeType(), ['image/png', 'image/jpeg'])){
            return response(['invalidImage' => 'formato de arquivo não aceito'], 422);
        }

        $user = User::find(request()->userId);

        $str_random = Str::random();

        $thumbnail_name = $str_random . '_thumb.' . $image->getClientOriginalExtension();
        $image_name = $str_random . '.' . $image->getClientOriginalExtension();
        
        
        Image::make($image)->resize('200', null, function($constraint){
            $constraint->aspectRatio();
        })->save('storage/img/'.$thumbnail_name);

        Storage::disk('public')->delete([$user->profile->image, $user->profile->image_thumb]);

        $profile = $user->profile;
        $profile->image = 'img/'. $image_name;
        $profile->image_thumb = 'img/' . $thumbnail_name;
        $profile->save();

        request()->file->storeAs('img', $image_name, 'public');

        return response(['file_path' => $profile->image_thumb, 'original_path' => $profile->image]);
    }
}
