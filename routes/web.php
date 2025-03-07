<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ExpenseController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
});

Route::get('/register', [UserController::class, 'showRegister'])->name('register');
Route::post('/register', [UserController::class, 'register']);

Route::get('/login', [UserController::class, 'showLogin'])->name('login');
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/tracker', [IncomeController::class, 'index'])->name('tracker');

    Route::prefix('incomes')->name('incomes.')->group(function () {
        Route::get('/', [IncomeController::class, 'index'])->name('index');
        Route::post('/create', [IncomeController::class, 'store'])->name('store');
        Route::post('/update/{id}', [IncomeController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [IncomeController::class, 'destroy'])->name('incomes.destroy');
        Route::get('/find/{id}', [IncomeController::class, 'find'])->name('find');
        Route::get('/search', [IncomeController::class, 'search'])->name('search');
    });

    Route::prefix('expenses')->name('expenses.')->group(function () {
        Route::get('/', [ExpenseController::class, 'index'])->name('index');
        Route::post('/create', [ExpenseController::class, 'store'])->name('store');
        Route::post('/update/{id}', [ExpenseController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ExpenseController::class, 'destroy'])->name('destroy');
        Route::get('/find/{id}', [ExpenseController::class, 'find'])->name('find');
        Route::get('/search', [ExpenseController::class, 'search'])->name('search');

    });
    Route::post('/expense/bulk-import', [ExpenseController::class, 'bulkImport']);
    Route::post('/income/bulk-import', [IncomeController::class, 'bulkImport']);
    Route::get('/all', [IncomeController::class, 'getAll'])->name('getAll');
});


