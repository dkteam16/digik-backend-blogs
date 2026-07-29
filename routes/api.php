<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\HiringPostController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Base URL: /api
|--------------------------------------------------------------------------
| All responses are JSON.
| Protected routes require: Authorization: Bearer {token}
|--------------------------------------------------------------------------
*/

// ── Auth ──────────────────────────────────────────────────────
Route::prefix('auth')->name('api.auth.')->group(function () {
   // Route::post('register', [AuthController::class, 'register'])->name('register');
   // Route::post('login',    [AuthController::class, 'login'])->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout',         [AuthController::class, 'logout'])->name('logout');
        Route::get('me',              [AuthController::class, 'me'])->name('me');
        Route::put('profile',         [AuthController::class, 'updateProfile'])->name('profile');
    });
});

// ── Public endpoints ──────────────────────────────────────────
Route::name('api.')->group(function () {

    // Posts (public read)
    Route::get('posts',             [PostController::class, 'index'])->name('posts.index');
    Route::get('posts/featured',    [PostController::class, 'featured'])->name('posts.featured');
    Route::get('posts/{slug}',      [PostController::class, 'show'])->name('posts.show');
    Route::get('posts/{slug}/related', [PostController::class, 'related'])->name('posts.related');

    // Comments (public read, write needs moderation)
    Route::get('posts/{slug}/comments',  [CommentController::class, 'index'])->name('comments.index');
    Route::post('posts/{slug}/comments', [CommentController::class, 'store'])->name('comments.store');

    // Categories
    Route::get('categories',        [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/{slug}', [CategoryController::class, 'show'])->name('categories.show');

    // Tags
    Route::get('tags',        [TagController::class, 'index'])->name('tags.index');
    Route::get('tags/{slug}', [TagController::class, 'show'])->name('tags.show');

    // Hiring posts (public read)
    Route::get('hiring-posts',          [HiringPostController::class, 'index'])->name('hiring-posts.index');
    Route::get('hiring-posts/featured', [HiringPostController::class, 'featured'])->name('hiring-posts.featured');
    Route::get('hiring-posts/{slug}',   [HiringPostController::class, 'show'])->name('hiring-posts.show');
});

// ── Protected write endpoints ─────────────────────────────────
Route::middleware('auth:sanctum')->name('api.')->group(function () {
    Route::post('posts',         [PostController::class, 'store'])->name('posts.store');
    Route::put('posts/{id}',     [PostController::class, 'update'])->name('posts.update');
    Route::delete('posts/{id}',  [PostController::class, 'destroy'])->name('posts.destroy');

    Route::post('hiring-posts',        [HiringPostController::class, 'store'])->name('hiring-posts.store');
    Route::put('hiring-posts/{id}',    [HiringPostController::class, 'update'])->name('hiring-posts.update');
    Route::delete('hiring-posts/{id}', [HiringPostController::class, 'destroy'])->name('hiring-posts.destroy');
});
