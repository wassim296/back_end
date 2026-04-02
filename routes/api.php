<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\SupportController;
use Illuminate\Support\Facades\Route;



// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{id}', [EventController::class, 'show']);
Route::get('/three-events', [EventController::class, 'threeEvents']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user/{id}', [AuthController::class , 'getUserById']);
    Route::put('/update-profile', [AuthController::class, 'updateProfile']);


    Route::get('/users/search', [AuthController::class , 'searchUsers']);
    Route::post('/update-role', [AuthController::class, 'updateRole']);

    // Event 
    Route::post('/events/created', [EventController::class, 'store']);
    Route::put('/events/update/{id}', [EventController::class, 'update']);
    Route::get('/myEvents/supportes', [SupportController::class, 'getMySupportedEvents']);
    Route::get('/myEvents/crees', [EventController::class, 'myEvents']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);

    Route::post('/events/supported', [SupportController::class, 'toggleSupport']);

    Route::post('/logout', [AuthController::class, 'logout']);

}
);


