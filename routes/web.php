<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DraftController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/login', function () {
    return view('auth.login');
})->name('login.index');
Route::post('/login', [UserController::class, 'login'])->name('login');

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [PostController::class, 'index']);
    Route::get('/logout',[UserController::class,'logout'])->name('logout');
    Route::post('drafts', [DraftController::class, 'store']);
    Route::get('drafts', function () {
        abort(404);
    });
});