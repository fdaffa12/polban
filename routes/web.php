<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AboutUsController;
use App\Http\Controllers\LenteraRestorasiImageController;
use App\Models\LenteraRestorasiImage;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EventController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
    Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
    Route::put('/articles/{article}', [ArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('articles.destroy');

    Route::post('/categories', [ArticleController::class, 'storeCategory'])->name('categories.store');
    Route::put('/categories/{category}', [ArticleController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{category}', [ArticleController::class, 'deleteCategory'])->name('categories.destroy');

    Route::get('/about-us', [AboutUsController::class, 'index'])->name('about-us.index');
    Route::post('/about-us', [AboutUsController::class, 'update'])->name('about-us.update');
    Route::post('/about-us/remove-image', [AboutUsController::class, 'removeImage'])->name('about-us.remove-image');

    // Vision routes
    Route::post('/lentera-restorasi/vision', [LenteraRestorasiImageController::class, 'storeVision'])
        ->name('lentera-restorasi.vision.store');
    Route::post('/lentera-restorasi/vision/{vision}', [LenteraRestorasiImageController::class, 'updateVision'])
        ->name('lentera-restorasi.vision.update');
    Route::delete('/lentera-restorasi/vision/{vision}', [LenteraRestorasiImageController::class, 'destroyVision'])
        ->name('lentera-restorasi.vision.destroy');

    // Mission routes
    Route::post('/lentera-restorasi/mission', [LenteraRestorasiImageController::class, 'storeMission'])
        ->name('lentera-restorasi.mission.store');
    Route::post('/lentera-restorasi/mission/{mission}', [LenteraRestorasiImageController::class, 'updateMission'])
        ->name('lentera-restorasi.mission.update');
    Route::delete('/lentera-restorasi/mission/{mission}', [LenteraRestorasiImageController::class, 'destroyMission'])
        ->name('lentera-restorasi.mission.destroy');

    // Core Values routes
    Route::post('/lentera-restorasi/core-value', [LenteraRestorasiImageController::class, 'storeCoreValue'])
        ->name('lentera-restorasi.core-value.store');
    Route::post('/lentera-restorasi/core-value/{coreValue}', [LenteraRestorasiImageController::class, 'updateCoreValue'])
        ->name('lentera-restorasi.core-value.update');
    Route::delete('/lentera-restorasi/core-value/{coreValue}', [LenteraRestorasiImageController::class, 'destroyCoreValue'])
        ->name('lentera-restorasi.core-value.destroy');

    // Image routes last
    Route::get('/lentera-restorasi', [LenteraRestorasiImageController::class, 'index'])
        ->name('lentera-restorasi');
    Route::post('/lentera-restorasi', [LenteraRestorasiImageController::class, 'store'])
        ->name('lentera-restorasi.store');
    Route::post('/lentera-restorasi/{image}', [LenteraRestorasiImageController::class, 'update'])
        ->name('lentera-restorasi.update');
    Route::delete('/lentera-restorasi/{image}', [LenteraRestorasiImageController::class, 'destroy'])
        ->name('lentera-restorasi.destroy');

    // Department routes
    Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
    Route::post('/departments', [DepartmentController::class, 'storeDepartment'])->name('departments.store');
    Route::post('/departments/{department}', [DepartmentController::class, 'updateDepartment'])->name('departments.update');
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroyDepartment'])->name('departments.destroy');

    // Member routes
    Route::post('/members', [DepartmentController::class, 'storeMember'])->name('members.store');
    Route::post('/members/{member}', [DepartmentController::class, 'updateMember'])->name('members.update');
    Route::delete('/members/{member}', [DepartmentController::class, 'destroyMember'])->name('members.destroy');

    // Event routes
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::post('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
});

require __DIR__ . '/auth.php';