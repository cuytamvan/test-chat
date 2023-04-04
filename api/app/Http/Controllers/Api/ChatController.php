<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ValidationException;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    protected $guard = 'user_api';

    public function room(): JsonResponse {
        $uid = auth($this->guard)->id();
        $data = User::whereNot('id', $uid)->get();

        return response()->json([
            'message' => 'success',
            'data' => $data,
        ]);
    }

    public function getChat($userId) {
        $uid = auth($this->guard)->id();

        $data = Chat::query()
            ->where(['from_id' => $userId, 'to_id' => $uid])
            ->orWhere(function($q) use($uid, $userId) {
                $q->where(['from_id' => $uid, 'to_id' => $userId]);
            })
            ->orderBy('id', 'asc')
            // ->toSql();
            ->get();

        return response()->json([
            'message' => 'success',
            'data' => $data,
        ]);
    }

    public function store(Request $request) {
        $input = $request->only(['to_id', 'body']);

        $validator = Validator::make($request->all(), [
            'to_id' => 'required',
            'body' => 'required',
        ]);
        if ($validator->fails()) throw new ValidationException($validator->errors()->first());

        $input['from_id'] = auth($this->guard)->id();
        $data = Chat::create($input);

        return response()->json([
            'message' => 'success',
            'data' => $data,
        ]);
    }
}
