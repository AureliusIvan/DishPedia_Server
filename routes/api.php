<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// User routes
use App\Http\Controllers\UserController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\NotificationController;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [UserController::class, 'getUser'])->middleware('auth:sanctum');
Route::put('/user', [UserController::class, 'updateUser'])->middleware('auth:sanctum');
Route::delete('/user', [UserController::class, 'deleteUser'])->middleware('auth:sanctum');

// Recipe routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/recipes', [RecipeController::class, 'index']);
    Route::get('/recipes/{recipe}', [RecipeController::class, 'show']);
    Route::post('/recipes', [RecipeController::class, 'store']);
    Route::post('/recipes/{recipe}', [RecipeController::class, 'update']);
    Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy']);
});

// Category routes 
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
});

// Like routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/recipes/{recipe}/like', [LikeController::class, 'toggleLike']);
}); 

// Comment routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/recipes/{recipe}/comments', [CommentController::class, 'store']);
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
});

// Favorite routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/recipes/{recipe}/favorite', [FavoriteController::class, 'toggleFavorite']);
});

// Notification routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{notification}', [NotificationController::class, 'markAsRead']);
});