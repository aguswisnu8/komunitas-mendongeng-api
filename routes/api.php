<?php

use App\Http\Controllers\API\KontenController;
use App\Http\Controllers\API\MendongengController;
use App\Http\Controllers\API\PartisipanController;
use App\Http\Controllers\API\UndanganController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::get('test', [KontenController::class, 'test']);


Route::get('kontens', [KontenController::class, 'all']);
Route::get('mendongengs', [MendongengController::class, 'all']);
Route::get('undangans', [UndanganController::class, 'all']);
Route::get('partisipans', [PartisipanController::class, 'all']);


Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    // current login user
    Route::get('user', [UserController::class, 'fetch']);
    Route::post('user', [UserController::class, 'updateProfile']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('reset', [UserController::class, 'reset']);
    Route::delete('user', [UserController::class, 'destroy']);


    // operasi untuk admin terhadap tabel user
    Route::get('users', [UserController::class, 'getUsers']);
    Route::post('users/{id}', [UserController::class, 'updateUser']);
    Route::delete('users/{id}', [UserController::class, 'deleteUser']);

    // konten
    Route::post('kontens', [KontenController::class, 'store']);
    Route::post('kontens/{id}', [KontenController::class, 'update']);
    Route::delete('kontens/{id}', [KontenController::class, 'delete']);

    // Kegiatan Mendongeng
    Route::post('mendongengs', [MendongengController::class, 'store']);
    Route::post('mendongengs/{id}', [MendongengController::class, 'update']);
    Route::delete('mendongengs/{id}', [MendongengController::class, 'delete']);

    // Undangan
    Route::post('undangans', [UndanganController::class, 'store']);
    Route::post('undangans/{id}', [UndanganController::class, 'update']);
    Route::delete('undangans/{id}', [UndanganController::class, 'delete']);


    // partisipan
    Route::post('partisipans', [PartisipanController::class, 'store']);
    Route::post('partisipans/{id}', [PartisipanController::class, 'update']);
    Route::post('ptest', [PartisipanController::class, 'test']);
    Route::delete('partisipans/{id}', [PartisipanController::class, 'delete']);
});
