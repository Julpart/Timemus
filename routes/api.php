<?php

use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\VerifyEmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
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

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword'])->middleware('guest')->name('password.reset');

Route::get('/email/verify', function () {
    return response()->json(['message'=>'Verification link sent'], 200);
})->middleware(['auth:api'])->name('verification.notice');

// Verify email
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verifyEmail'])
    ->middleware('auth:api')
    ->name('verification.verify');


// Resend link to verify email
Route::post('/email/verify/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response()->json(['message'=>'Verification link sent'], 200);
})->middleware(['auth:api'])->name('verification.send');

Route::group(['middleware' => ['auth:api','verified']], function(){

    //CRUD user todo объединить роуты
    Route::get('/users',[UserController::class,'index']);
    Route::get('/users/{id}',[UserController::class,'show']);
    Route::put('/users/{id}',[UserController::class,'update']);
    Route::delete('/users/{id}',[UserController::class,'destroy']);

    //CRUD project todo объединить роуты
    Route::get('/projects',[ProjectController::class,'index']);
    Route::post('/projects',[ProjectController::class,'store']);
    Route::get('/projects/{id}',[ProjectController::class,'show']);
    Route::put('/projects/{id}',[ProjectController::class,'update']);
    Route::delete('/projects/{id}',[ProjectController::class,'destroy']);

    //CRUD task
    Route::get('/tasks',[TaskController::class,'index']);
    Route::post('/tasks',[TaskController::class,'store']);
    Route::get('/tasks/{id}',[TaskController::class,'show']);
    Route::put('/tasks/{id}',[TaskController::class,'update']);
    Route::delete('/tasks/{id}',[TaskController::class,'destroy']);

    Route::get('/tags',[TagController::class,'index']);
    Route::get('/tags/{id}',[TagController::class,'show']);
});

