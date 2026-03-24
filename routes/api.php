<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\SupportController;
use Illuminate\Support\Facades\Route;

// Authentication
Route::post('/register' , [AuthController::class, 'register']);
Route::post('/login' , [AuthController::class, 'login']);

// Events
Route::get('/events',[EventController::class , 'index']);
Route::post('/events',[EventController::class , 'store']);

// Support
Route::post('/support',[EventController::class , 'ToggleSupport']);