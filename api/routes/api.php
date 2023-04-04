<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('verify', [AuthController::class, 'verify'])->name('verify');

Route::middleware('auth:user_api')->group(function() {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('me', function (Request $request) {
        return response()->json([
            'message' => 'success',
            'data' => $request->user(),
        ]);
    });

    Route::prefix('chats')->name('chats.')->group(function() {
        Route::post('/', [ChatController::class, 'store'])->name('store');
        Route::get('/rooms', [ChatController::class, 'room'])->name('room');
        Route::get('/{id}', [ChatController::class, 'getChat'])->name('show');
    });
});
