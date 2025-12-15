<?php

require_once __DIR__ . '/Route.php';

use routes\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\AdminController;

// Главная
Route::get('/', function() {
    if (session('user_id')) {
        header('Location: /proposals');
    } else {
        header('Location: /login');
    }
    exit;
});

// Аутентификация
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout']);

// КП (требуется авторизация)
Route::middleware('auth')->group(function() {
    Route::get('/proposals', [ProposalController::class, 'index']);
    Route::get('/proposals/create', [ProposalController::class, 'create']);
    Route::post('/proposals', [ProposalController::class, 'store']);
    Route::get('/proposals/{id}', [ProposalController::class, 'show']);
    Route::get('/proposals/{id}/edit', [ProposalController::class, 'edit']);
    Route::post('/proposals/{id}/update', [ProposalController::class, 'update']);
    Route::post('/proposals/{id}/delete', [ProposalController::class, 'destroy']);
    Route::post('/proposals/{id}/publish', [ProposalController::class, 'publish']);
    
    Route::get('/proposals/{id}/pdf', [ExportController::class, 'pdf']);
    Route::get('/proposals/{id}/docx', [ExportController::class, 'docx']);

    // Шаблоны
    Route::get('/templates', [TemplateController::class, 'index']);
    Route::get('/templates/create', [TemplateController::class, 'create']);
    Route::post('/templates', [TemplateController::class, 'store']);
    Route::get('/templates/{id}', [TemplateController::class, 'show']);
    Route::get('/templates/{id}/edit', [TemplateController::class, 'edit']);
    Route::post('/templates/{id}/update', [TemplateController::class, 'update']);
    Route::post('/templates/{id}/delete', [TemplateController::class, 'destroy']);

    // Файлы
    Route::post('/files/upload', [FileController::class, 'upload']);
    Route::post('/files/{id}/delete', [FileController::class, 'destroy']);
});

// Админка
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function() {
    Route::get('/', [AdminController::class, 'dashboard']);
    Route::get('/users', [AdminController::class, 'users']);
    Route::post('/users/{id}/role', [AdminController::class, 'updateUserRole']);
});

