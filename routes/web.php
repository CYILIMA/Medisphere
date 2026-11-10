<?php

use App\Http\Controllers\HealthPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HealthPageController::class, 'home'])->name('home');
Route::get('/medicine/{specialty}', [HealthPageController::class, 'medicine'])->name('medicine.show');
Route::get('/services/{service}', [HealthPageController::class, 'service'])->name('service.show');
Route::get('/about', [HealthPageController::class, 'information'])->defaults('topic', 'about')->name('about');
Route::get('/about/{topic}', [HealthPageController::class, 'information'])
    ->whereIn('topic', ['mission', 'vision'])
    ->name('about.show');
