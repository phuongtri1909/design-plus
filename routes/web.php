<?php

use App\Models\User;
use App\Http\Middleware\Admin;
use App\Http\Controllers\FileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DraftController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

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
    Route::get('/logout',[UserController::class,'logout'])->name('logout');
    Route::get('posts/{slug}', [PostController::class, 'show'])->name('posts.show');
    
    Route::get('/file/{path}', [FileController::class,'show'])->where('path', '.*');
    Route::post('classify',[PostController::class,'classify']);
    
    Route::group(['middleware' => ['active','roleReporter']], function () {
        Route::post('drafts', [DraftController::class, 'store']);
        Route::get('drafts', function () {
            abort(404);
        });
        Route::get('/', [PostController::class, 'index'])->name('home');
        Route::post('posts', [PostController::class, 'store']);
        Route::get('/posts/{slug}/edit',[PostController::class,'edit'])->name('posts.edit');
        Route::delete('posts/{id}', [PostController::class, 'destroy']);
        Route::patch('recall/{id}', [PostController::class, 'recall']);
        Route::patch('send/{id}', [PostController::class, 'send']);
        Route::patch('resend/{id}', [PostController::class, 'resend']);
        Route::put('posts/{slug}', [PostController::class, 'update'])->name('posts.update');
        Route::get('posts',[PostController::class,'allPosts'])->name('posts.allPosts');
    });
   

    Route::group(['middleware' => ['getPost','active']], function () {
        Route::get('get-posts', [PostController::class, 'getPosts'])->name('get.posts');
        Route::post('getPost_classify', [PostController::class, 'getPost_classify'])->name('getPost.classify');
        Route::post('save-link',[PostController::class,'saveLink'])->name('save.link');
        Route::post('push-post',[PostController::class,'pushPost'])->name('push.post');
        Route::get('dashboard/affiliate',[AdminController::class,'indexAffiliate'])->name('dashboard.affiliate');
        Route::post('report-this-user-get-posts',[AdminController::class,'report_this_user_get_posts'])->name('report.this.user.getPosts');
    });

    Route::group(['middleware' => 'roleAdminApprover'], function () {
        Route::get('approve',[PostController::class,'approve_index'])->name('approve.index');
        Route::post('approve_list',[PostController::class,'approve_list'])->name('approve.list');
        Route::post('approve_classify',[PostController::class,'approve_classify'])->name('approve.classify');
        Route::post('handleApproveAction',[PostController::class,'handleApproveAction'])->name('approve.handleApproveAction');
    });

    Route::group(['middleware' => 'roleApprover'], function () {
        Route::get('dashboard/approver',[AdminController::class,'indexApprover'])->name('dashboard.approver');
        Route::post('report-this-user-approval',[AdminController::class,'report_this_user_approval'])->name('report.this.user.approval');
    });
   
    Route::group(['middleware' => 'admin'], function () {

        Route::get('dashboard',[AdminController::class,'index'])->name('dashboard.index');

        Route::resource('categories', CategoryController::class);
        Route::get('search',[CategoryController::class,'search'])->name('categories.search');

        Route::get('reporter',[AdminController::class,'reporter_index'])->name('reporter.index');
        Route::get('reporter-report/{id}/{report}',[AdminController::class,'reporter_report'])->name('reporter.report');
        Route::get('create-user',[AdminController::class,'create_user'])->name('create.user');
        Route::post('create-user',[AdminController::class,'store_user'])->name('store.user');
        Route::get('edit-user/{id}',[AdminController::class,'edit_user'])->name('edit.user');
        Route::put('update-user/{id}',[AdminController::class,'update_user'])->name('update.user');
        Route::delete('delete-user/{id}',[AdminController::class,'delete_user'])->name('delete.user');
        Route::get('show-user/{id}',[AdminController::class,'show_user'])->name('show.user');
        Route::get('search-user',[AdminController::class,'search_user'])->name('search.user');
        Route::get('user-post',[AdminController::class,'user_post_index'])->name('user.post.index');
        Route::get('user-post/{id}',[AdminController::class,'user_post_show'])->name('user.post.show');

        Route::get('user-approval',[AdminController::class,'user_approval_post_index'])->name('list.user.approval');
        Route::get('user-approval/{id}',[AdminController::class,'user_approval_show'])->name('show.user.approval');

        Route::post('report-user-get-posts',[AdminController::class,'report_user_get_posts'])->name('report.user.getPosts');
        Route::post('report-user-approval',[AdminController::class,'report_user_approval'])->name('report.user.approval');
    
    });
});