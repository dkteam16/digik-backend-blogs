<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HiringPostController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\NewsletterSubscriberController;
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

    Route::get('posts/data', [PostController::class, 'data'])->name('posts.data');
    Route::post('posts/bulk-action', [PostController::class, 'bulkAction'])->name('posts.bulk-action');
    Route::resource('posts', PostController::class);

    Route::get('hiring-posts/data', [HiringPostController::class, 'data'])->name('hiring-posts.data');
    Route::post('hiring-posts/bulk-action', [HiringPostController::class, 'bulkAction'])->name('hiring-posts.bulk-action');
    Route::resource('hiring-posts', HiringPostController::class);

    Route::get('categories/data', [CategoryController::class, 'data'])->name('categories.data');
    Route::resource('categories', CategoryController::class);

    Route::get('tags',          [TagController::class, 'index'])->name('tags.index');
    Route::get('tags/data',     [TagController::class, 'data'])->name('tags.data');
    Route::post('tags',         [TagController::class, 'store'])->name('tags.store');
    Route::put('tags/{tag}',    [TagController::class, 'update'])->name('tags.update');
    Route::delete('tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');

    Route::get('comments',                    [CommentController::class, 'index'])->name('comments.index');
    Route::get('comments/data',               [CommentController::class, 'data'])->name('comments.data');
    Route::post('comments/bulk-action',       [CommentController::class, 'bulkAction'])->name('comments.bulk-action');
    Route::post('comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve');
    Route::post('comments/{comment}/reject',  [CommentController::class, 'reject'])->name('comments.reject');
    Route::delete('comments/{comment}',       [CommentController::class, 'destroy'])->name('comments.destroy');

    Route::get('users/data', [UserController::class, 'data'])->name('users.data');
    Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::resource('users', UserController::class);

    Route::get('media',          [MediaController::class, 'index'])->name('media.index');
    Route::post('media/upload',  [MediaController::class, 'upload'])->name('media.upload');
    Route::delete('media',       [MediaController::class, 'destroy'])->name('media.destroy');

    Route::get('newsletter',                     [NewsletterSubscriberController::class, 'index'])->name('newsletter.index');
    Route::get('newsletter/data',                [NewsletterSubscriberController::class, 'data'])->name('newsletter.data');
    Route::post('newsletter/bulk-action',         [NewsletterSubscriberController::class, 'bulkAction'])->name('newsletter.bulk-action');
    Route::delete('newsletter/{subscriber}',      [NewsletterSubscriberController::class, 'destroy'])->name('newsletter.destroy');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
