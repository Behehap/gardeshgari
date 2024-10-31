<?php

use App\Http\Controllers\AdminArticleController as AdminArticleControllerAlias;
use App\Http\Controllers\AdminMessageController;
use App\Http\Controllers\AdminTicketController;
use App\Http\Controllers\ArticleImageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserArticleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserMessageController;
use App\Http\Controllers\UserTicketController;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsCompleteProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');



Route::post('send-code', [AuthController::class, 'sendCode']);
Route::post('confirm-code', [AuthController::class, 'confirmCode']);
Route::post('complete-profile', [AuthController::class, 'completeProfile'])->middleware('auth:sanctum');


Route::prefix('user')->group(function (){
    Route::get('/', [UserController::class, 'ShowInfo'])->middleware('auth:sanctum');
    Route::put('/', [UserController::class, 'updateProfile'])->middleware('auth:sanctum');
    Route::get('/is-complete', [UserController::class, 'isCompleteProfile'])->middleware('auth:sanctum');
    Route::apiResource('tickets', UserTicketController::class)->middleware(['auth:sanctum', IsCompleteProfile::class]);
    Route::post('tickets/{ticket}', [UserMessageController::class, 'store'])->middleware(['auth:sanctum', IsCompleteProfile::class]);

    Route::apiResource('articles', UserArticleController::class)->middleware(['auth:sanctum', IsCompleteProfile::class]);
    Route::post('articles/{articleId}/images', [ArticleImageController::class, 'upload'])->middleware(['auth:sanctum', IsCompleteProfile::class]);
    Route::delete('articles/images/{id}', [ArticleImageController::class, 'delete'])->middleware(['auth:sanctum', IsCompleteProfile::class]);
    Route::put('articles/images/{id}', [ArticleImageController::class, 'replace'])->middleware(['auth:sanctum', IsCompleteProfile::class]);
    });

Route::prefix('admin')->group(function (){
    Route::apiResource('tickets', AdminTicketController::class)->only(['index', 'show'])->middleware(['auth:sanctum', IsAdmin::class]);
    Route::post('tickets/{ticket}', [AdminMessageController::class, 'store'])->middleware(['auth:sanctum', IsAdmin::class]);

    Route::apiResource('articles', AdminArticleControllerAlias::class)->only(['update','show','index','destroy'])->middleware(['auth:sanctum', IsAdmin::class]);
    });



