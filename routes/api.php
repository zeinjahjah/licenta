<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TemeController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AttachmentController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/




// public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// // Route::post('/contact', [mailController::class, 'contact']);

// // protect routes
Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('/teme/coordonator/{id}', [TemeController::class, 'temeByCoordonator']);
    Route::resource('teme', TemeController::class);

    Route::resource('workspace', WorkspaceController::class);

    Route::get('/event/student/{id}', [EventController::class, 'index']);
    Route::resource('event', EventController::class);

    Route::resource('comment', CommentController::class);

    Route::post('/upload-file', [AttachmentController::class, 'fileUpload'])->name('fileUpload');
    Route::get('/get-file/{eventId}', [AttachmentController::class, 'getEventFile']);
    Route::get('/remove-file/{fileId}', [AttachmentController::class, 'RemoveFile']);

    //logout
    Route::get('/logout', [AuthController::class, 'logout']);

});
