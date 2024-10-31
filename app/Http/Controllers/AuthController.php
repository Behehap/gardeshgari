<?php

namespace App\Http\Controllers;


use App\DTOs\BaseDto;
use App\DTOs\BaseDtoStatusEnum;
use App\Http\Requests\CompleteProfileRequest;
use App\Services\FileService;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Kavenegar\KavenegarApi;
use Kavenegar\Exceptions\ApiException;
use Kavenegar\Exceptions\HttpException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function sendCode(Request $request)
    {

        $validated = $request->validate([
            'phone' => 'required|regex:/^09[0-9]{9}$/|size:11',
        ]);


        $phone = $validated['phone'];
        $code = rand(100000, 999999);
        echo $code;


        try {
            Redis::setex("verification_code:{$phone}", 600, 111111); //$code

            // $api = new KavenegarApi("493036347A343565484D3767455769504867546F636A7A30664D6C36316F724E38654E2B42324A2F4166633D");


            $sender = "gardeshgari";

            $message = "code: {$code}";
            // $api->Send($sender, $phone, $message);

//            return response()->json(['message' => 'verify code send', 'success' => true]);

            return response()->json(new BaseDto(BaseDtoStatusEnum::OK, "verify code send"));
        }
        catch (ApiException $e) {
            // خطای مربوط به API کاوه‌نگار (مثلاً خروجی 200 دریافت نشده)
            return response()->json(['error' => 'ERROR API: ' . $e->errorMessage()], 500);
        }
        catch (HttpException $e) {
            // خطا در ارتباط با کاوه‌نگار (مشکل اتصال)
            return response()->json(['error' => 'ERROR HTTP: ' . $e->errorMessage()], 500);
        }
    }

    public function confirmCode(Request $request)
    {

        $validated = $request->validate([
            'phone' => 'required|regex:/^09[0-9]{9}$/|size:11',
            'code'  => 'required|digits:6',
        ]);



        $phone = $validated['phone'];
        $code = $validated['code'];

        $savedCode = Redis::get("verification_code:{$phone}");

        if (!$savedCode) {
            return response()->json(new BaseDto(BaseDtoStatusEnum::ERROR, 'code not found'), 404);
        }


        if ($savedCode != $code) {
            return response()->json(new BaseDto(BaseDtoStatusEnum::ERROR, 'code not currect'), 400);
        }

        Redis::del("verification_code:{$phone}");

        $user = User::where('phone', $phone)->first();

        if (!$user) {

            $user = User::create([
                'phone' => $phone
            ]);
            $user->is_complete_profile = false;
        }
        Auth::login($user);
//        return response()->json([
//            'success' => true,
//            'message' => 'successful confirm',
//            'user' => $user,
//            'token' => $user->createToken('auth_token')->plainTextToken // تولید توکن برای API
//        ]);
        if($user->is_complete_profile) {
            return response()->json(new BaseDto(BaseDtoStatusEnum::OK, "successful confirm", [
                'user' => $user,
                'access_token' => $user->createToken('api_token')->plainTextToken, // تولید توکن برای API
                'token_type' => 'Bearer',
            ]));
        }
        return response()->json(new BaseDto(BaseDtoStatusEnum::OK, "successful confirm, please complete profile", [
            'user' => $user,
            'access_token' => $user->createToken('api_token')->plainTextToken, // تولید توکن برای API
            'token_type' => 'Bearer',
        ]));

    }

    public function completeProfile(CompleteProfileRequest $request)
    {
        $validated = $request->validated();
        $user = Auth::user();

        if(key_exists('image', $validated)) {
            $file_service = new FileService('public');
            $user->image_profile_path = $file_service->upload($validated['image'], 'images/user-profiles');
        }
        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->is_complete_profile = true;
        $user->save();

        return response()->json(new BaseDto(BaseDtoStatusEnum::OK, "complete profile successful", [
        'user' => $user
    ]));
    }
}
