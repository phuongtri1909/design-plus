<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DraftController;
use App\Http\Controllers\FileController;
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





Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login.index');
    Route::post('/login', [UserController::class, 'login'])->name('login');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [PostController::class, 'index'])->name('home');
    Route::get('/logout',[UserController::class,'logout'])->name('logout');
    Route::post('drafts', [DraftController::class, 'store']);
    Route::get('drafts', function () {
        abort(404);
    });

    Route::post('posts', [PostController::class, 'store']);
    Route::get('posts',[PostController::class,'allPosts'])->name('posts.allPosts');
    Route::get('/posts/{slug}/edit',[PostController::class,'edit'])->name('posts.edit');
    Route::get('posts/{slug}', [PostController::class, 'show'])->name('posts.show');
    Route::put('posts/{slug}', [PostController::class, 'update'])->name('posts.update');

    Route::get('/file/{path}', [FileController::class,'show'])->where('path', '.*');
    Route::post('classify',[PostController::class,'classify']);

    Route::delete('posts/{id}', [PostController::class, 'destroy']);
    Route::patch('recall/{id}', [PostController::class, 'recall']);
    Route::patch('send/{id}', [PostController::class, 'send']);
    Route::patch('resend/{id}', [PostController::class, 'resend']);

    Route::group(['middleware' => 'admin'], function () {
        Route::get('approve',[PostController::class,'approve_index'])->name('approve.index');
        Route::post('approve_list',[PostController::class,'approve_list'])->name('approve.list');
        Route::post('approve_classify',[PostController::class,'approve_classify'])->name('approve.classify');
    });

    Route::group(['middleware' => 'getPost'], function () {
        Route::post('handleApproveAction',[PostController::class,'handleApproveAction'])->name('approve.handleApproveAction');
        Route::get('get-posts', [PostController::class, 'getPosts'])->name('get.posts');
        Route::post('getPost_classify', [PostController::class, 'getPost_classify'])->name('getPost.classify');
        Route::post('save-link',[PostController::class,'saveLink'])->name('save.link');
    });
   
});