<?php

use App\Http\Controllers\AttractionController;
use App\Http\Controllers\ProfileController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/attractions/create', [AttractionController::class, 'create'])->name('attractions.create');
    Route::post('/attractions', [AttractionController::class, 'store'])->name('attractions.store');
    Route::get('/attractions/{attraction}/edit', [AttractionController::class, 'edit'])->name('attractions.edit');
    Route::put('/attractions/{attraction}', [AttractionController::class, 'update'])->name('attractions.update');
    Route::delete('/attractions/{attraction}', [AttractionController::class, 'destroy'])->name('attractions.destroy');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/attractions', [AttractionController::class, 'index'])->name('attractions.index');
    Route::get('/attractions/{attraction}', [AttractionController::class, 'show'])->name('attractions.show');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
