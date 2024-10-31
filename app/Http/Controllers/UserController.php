<?php

namespace App\Http\Controllers;

use App\DTOs\BaseDto;
use App\DTOs\BaseDtoStatusEnum;
use App\Http\Resources\UserResource;
use App\Services\FileService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function ShowInfo(Request $request)
    {
        $user = Auth::user();
        return new UserResource($user);
    }

    public function updateProfile(Request $request)
    {

        $validated = $request->validate([
            'name' => 'sometimes|required|max:255',
            'username' => 'sometimes|required|max:255|unique:users,username',
//            'phone' => 'sometimes|required|regex:/^09[0-9]{9}$/|size:11|unique:users,phone',
            'image' => 'sometimes|mimes:jpeg,png|max:1024', // فقط jpeg و png با حداکثر حجم 1MB
        ]);
        $user = Auth::user();
echo 'salam';
        if(key_exists('name', $validated))
            $user->name = $validated['name'];

        if(key_exists('username', $validated))
            $user->username = $validated['name'];

        if(key_exists('image', $validated)){
            var_dump($validated);
            $file_service = new   ('public');
            $user->image_profile_path = $file_service->update($validated['image'], $user->image_profile_path, 'images/user-profiles');
        }

        $user->save();

        return new BaseDto(BaseDtoStatusEnum::OK, 'update profile successful', new UserResource($user));
    }

    public function isCompleteProfile(Request $request)
    {
        $user = Auth::user();
        return $user->is_complete_profile;
    }
}
