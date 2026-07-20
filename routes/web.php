<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ApiDocsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('admin.dashboard'));
Route::get('/api-docs', [ApiDocsController::class, 'index'])->name('api.docs');

Auth::routes(['register' => false]);

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('posts/bulk-action', [PostController::class, 'bulkAction'])->name('posts.bulk-action');
    Route::resource('posts', PostController::class);

    Route::resource('categories', CategoryController::class);

    Route::get('tags',          [TagController::class, 'index'])->name('tags.index');
    Route::post('tags',         [TagController::class, 'store'])->name('tags.store');
    Route::put('tags/{tag}',    [TagController::class, 'update'])->name('tags.update');
    Route::delete('tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');

    Route::get('comments',                    [CommentController::class, 'index'])->name('comments.index');
    Route::post('comments/bulk-action',       [CommentController::class, 'bulkAction'])->name('comments.bulk-action');
    Route::post('comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve');
    Route::post('comments/{comment}/reject',  [CommentController::class, 'reject'])->name('comments.reject');
    Route::delete('comments/{comment}',       [CommentController::class, 'destroy'])->name('comments.destroy');

    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::resource('users', UserController::class);

    Route::get('media',          [MediaController::class, 'index'])->name('media.index');
    Route::post('media/upload',  [MediaController::class, 'upload'])->name('media.upload');
    Route::delete('media',       [MediaController::class, 'destroy'])->name('media.destroy');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
