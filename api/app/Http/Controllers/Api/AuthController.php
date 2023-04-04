<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Mail\EmailVerification;
use App\Models\User;
use App\Models\UserToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse {
        $data = User::where('email', $request->email)->first();
        if (!$data) throw new ValidationException('User not found');
        if (!Hash::check($request->password, $data->password)) throw new ValidationException('Wrong password');

        $token = Str::random(10);
        UserToken::create([
            'user_id' => $data->id,
            'token' => $token,
        ]);
        Mail::to($data->email)->send(new EmailVerification($token, $data));

        return response()->json([
            'message' => 'Please check your email',
            'data' => $data,
        ]);
    }

    public function verify(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'token' => 'required',
        ]);
        if ($validator->fails()) throw new ValidationException($validator->errors()->first());

        $user = User::find($request->user_id);
        if (!$user) throw new ValidationException('User not found');

        $data = UserToken::where([
            'user_id' => $request->user_id,
            'token' => $request->token,
        ])->first();
        if (!$data) throw new ValidationException('Token not found');

        $data->delete();
        $user->api_token = $user->createToken($request->userAgent())->plainTextToken;

        return response()->json([
            'message' => 'success',
            'data' => $user,
        ]);
    }

    public function logout()
    {
        try {
            auth('user_api')->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'success logout'
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
