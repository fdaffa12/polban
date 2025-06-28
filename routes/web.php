<?php

use App\Http\Controllers\HomeController;
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
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\ProposalSpjController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AdministrationController;
use App\Http\Controllers\RapotHimpunanController;

Route::get('/', [HomeController::class, 'index'])->name('home');

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
    Route::get('/himpunan-management', [LenteraRestorasiImageController::class, 'index'])
        ->name('himpunan-management');
    Route::post('/lentera-restorasi', [LenteraRestorasiImageController::class, 'store'])
        ->name('lentera-restorasi.store');
    Route::post('/lentera-restorasi/{image}', [LenteraRestorasiImageController::class, 'update'])
        ->name('lentera-restorasi.update');
    Route::delete('/lentera-restorasi/{image}', [LenteraRestorasiImageController::class, 'destroy'])
        ->name('lentera-restorasi.destroy');
    Route::post('/lentera-restorasi/himpunan/update', [LenteraRestorasiImageController::class, 'updateHimpunan'])
        ->name('lentera-restorasi.himpunan.update');

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

    // Proposal routes
    Route::get('/proposals', [ProposalController::class, 'index'])->name('proposals.index');
    Route::get('/proposals/create', [ProposalController::class, 'create'])->name('proposals.create');
    Route::post('/proposals', [ProposalController::class, 'store'])->name('proposals.store');
    Route::get('/proposals/{proposal}', [ProposalController::class, 'show'])->name('proposals.show');
    Route::get('/proposals/{proposal}/edit', [ProposalController::class, 'edit'])->name('proposals.edit');
    Route::post('/proposals/{proposal}', [ProposalController::class, 'update'])->name('proposals.update');
    Route::delete('/proposals/{proposal}', [ProposalController::class, 'destroy'])->name('proposals.destroy');
    Route::post('/proposals/{proposal}/status', [ProposalController::class, 'updateStatus'])->name('proposals.update-status');

    // Add these new routes
    Route::put('/proposals/{proposal}/approve', [ProposalController::class, 'approve'])->name('proposals.approve');
    Route::put('/proposals/{proposal}/revise', [ProposalController::class, 'revise'])->name('proposals.revise');

    // User Management Routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/proposals/{proposal}/spj', [ProposalSpjController::class, 'index'])->name('proposals.spj');
    Route::post('/proposals/{proposal}/spj', [ProposalSpjController::class, 'store'])->name('proposals.spj.store');
    Route::post('/proposals/spj/{spj}', [ProposalSpjController::class, 'update'])->name('proposals.spj.update');
    Route::delete('/proposals/spj/{spj}', [ProposalSpjController::class, 'destroy'])->name('proposals.spj.destroy');
    Route::get('/proposals/{proposal}/spj/{spj}', [ProposalSpjController::class, 'show'])->name('proposals.spj.show');

    Route::put('/proposals/spj/{spj}/approve', [ProposalSpjController::class, 'approve'])->name('proposals.spj.approve');
    Route::put('/proposals/spj/{spj}/revise', [ProposalSpjController::class, 'revise'])->name('proposals.spj.revise');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Department Logo routes
    Route::post('/dept-logos', [DepartmentController::class, 'storeDeptLogo'])->name('dept-logos.store');
    Route::post('/dept-logos/{deptLogo}', [DepartmentController::class, 'updateDeptLogo'])->name('dept-logos.update');
    Route::delete('/dept-logos/{deptLogo}', [DepartmentController::class, 'destroyDeptLogo'])->name('dept-logos.destroy');

    // Administration routes
    Route::get('/notulensi', [AdministrationController::class, 'notulensiIndex'])->name('notulensi.index');
    Route::post('/notulensi', [AdministrationController::class, 'notulensiStore'])->name('notulensi.store');
    Route::post('/notulensi/{notulensi}', [AdministrationController::class, 'notulensiUpdate'])->name('notulensi.update');
    Route::delete('/notulensi/{notulensi}', [AdministrationController::class, 'notulensiDestroy'])->name('notulensi.destroy');
    Route::get('/format-administrasi', [AdministrationController::class, 'formatAdministrasiIndex'])->name('format-administrasi.index');
    Route::post('/format-administrasi', [AdministrationController::class, 'formatAdministrasiStore'])->name('format-administrasi.store');
    Route::post('/format-administrasi/{formatAdministrasi}', [AdministrationController::class, 'formatAdministrasiUpdate'])->name('format-administrasi.update');
    Route::delete('/format-administrasi/{formatAdministrasi}', [AdministrationController::class, 'formatAdministrasiDestroy'])->name('format-administrasi.destroy');
    Route::get('/buku-panduan', [AdministrationController::class, 'bukuPanduanIndex'])->name('buku-panduan.index');
    Route::post('/buku-panduan', [AdministrationController::class, 'bukuPanduanStore'])->name('buku-panduan.store');
    Route::post('/buku-panduan/{bukuPanduan}', [AdministrationController::class, 'bukuPanduanUpdate'])->name('buku-panduan.update');
    Route::delete('/buku-panduan/{bukuPanduan}', [AdministrationController::class, 'bukuPanduanDestroy'])->name('buku-panduan.destroy');

    Route::get('/rapot-hmjt', [RapotHimpunanController::class, 'rapotHmjtIndex'])->name('rapot-hmjt.index');
    Route::post('/rapot-hmjt', [RapotHimpunanController::class, 'rapotHmjtStore'])->name('rapot-hmjt.store');
    Route::post('/rapot-hmjt/{rapotHmjt}', [RapotHimpunanController::class, 'rapotHmjtUpdate'])->name('rapot-hmjt.update');
    Route::delete('/rapot-hmjt/{rapotHmjt}', [RapotHimpunanController::class, 'rapotHmjtDestroy'])->name('rapot-hmjt.destroy');


});

// New about route
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/himpunan', [HomeController::class, 'lenteraRestorasi'])->name('himpunan');
Route::get('/department', [HomeController::class, 'department'])->name('department');

require __DIR__ . '/auth.php';

// Tambahkan routes ini
Route::get('/news', [HomeController::class, 'news'])->name('news');
Route::get('/news/category/{categoryId}', [HomeController::class, 'newsByCategory'])->name('news.category');
Route::get('/article/{slug}', [HomeController::class, 'articleDetail'])->name('article.detail');

// Tambahkan route event publik
Route::get('/our-event', [HomeController::class, 'events'])->name('public.events');
Route::get('/our-event/department/{departmentId}', [HomeController::class, 'eventsByDepartment'])->name('public.events.by.department');
Route::get('/event/{slug}', [HomeController::class, 'eventDetail'])->name('public.event.detail');

// Tambahkan route untuk calendar view
Route::get('/event-calendar', [HomeController::class, 'eventCalendar'])->name('public.event.calendar');

// Update route binding at the top
Route::bind('article', function ($value) {
    return \App\Models\Article::where('id', $value)->firstOrFail();
});