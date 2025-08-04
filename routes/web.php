<?php

use App\Http\Controllers\CarController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PasswordResetLinkController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/login');

});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Car Management (CRUD)
    Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
    Route::post('/cars', [CarController::class, 'store'])->name('cars.store');
    Route::get('/cars/{car}', [CarController::class, 'show'])->name('cars.show'); // For showing details (if needed)
    Route::put('/cars/{car}', [CarController::class, 'update'])->name('cars.update');
    Route::delete('/cars/{car}', [CarController::class, 'destroy'])->name('cars.destroy');
    Route::get('/cars/{car}/edit', [CarController::class, 'edit'])->name('cars.edit'); // For fetching data for edit modal

    // Loan Calculator
    Route::get('/loan-calculator', function () {
        return view('calculator.index');
    })->name('loan.calculator');
    Route::post('/loan-calculator', [CarController::class, 'calculateLoan'])->name('loan.calculate');


    // Admin Routes (Protected by 'admin' gate)
    Route::middleware(['can:admin'])->group(function () {
        Route::get('/admin/users', [CarController::class, 'manageUsers'])->name('admin.users.index');
        Route::post('/admin/users/{user}/authorize', [CarController::class, 'authorizeUser'])->name('admin.users.authorize');
        Route::post('/admin/users/{user}/reset-password', [CarController::class, 'resetUserPassword'])->name('admin.users.reset-password');
        Route::post('/admin/users/{user}/toggle-status', [CarController::class, 'toggleUserStatus'])->name('admin.users.toggle-status');
    });

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('guest')
        ->name('password.email');
        
    });

require __DIR__.'/auth.php';