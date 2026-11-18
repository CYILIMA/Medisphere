<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\MedicineController as AdminMedicineController;
use App\Http\Controllers\HealthPageController;
use App\Http\Controllers\MedicineController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HealthPageController::class, 'home'])->name('home');
Route::get('/medicines', [MedicineController::class, 'index'])->name('medicines.index');
Route::get('/medicines/{medicine:slug}', [MedicineController::class, 'show'])->name('medicines.show');
Route::get('/medicine/{specialty}', [HealthPageController::class, 'medicine'])->name('medicine.show');
Route::get('/services/{service}', [HealthPageController::class, 'service'])->name('service.show');
Route::get('/about', [HealthPageController::class, 'information'])->defaults('topic', 'about')->name('about');
Route::get('/about/{topic}', [HealthPageController::class, 'information'])
    ->whereIn('topic', ['mission', 'vision'])
    ->name('about.show');

Route::get('/admin/login', [AuthController::class, 'login'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'authenticate'])->middleware('throttle:5,1')->name('admin.login.store');

Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::resource('medicines', AdminMedicineController::class)->except(['show']);
});
