<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AgentReportController;
use App\Http\Controllers\LangController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register',[AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Profile routes (all authenticated users)
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/',              [ProfileController::class, 'show'])->name('show');
    Route::put('/',              [ProfileController::class, 'update'])->name('update');
    Route::post('/photo',        [ProfileController::class, 'updatePhoto'])->name('photo');
    Route::put('/password',      [ProfileController::class, 'updatePassword'])->name('password');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard',                      [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/agents',                         [AdminController::class, 'agents'])->name('agents.index');
    Route::patch('/agents/{agent}/status',        [AdminController::class, 'updateAgentStatus'])->name('agents.status');
    Route::patch('/agents/{agent}/promote',       [AdminController::class, 'promoteAgent'])->name('agents.promote');
    Route::delete('/agents/{agent}',              [AdminController::class, 'destroyAgent'])->name('agents.destroy');
    Route::get('/transactions',                   [AdminController::class, 'transactions'])->name('transactions.index');
    Route::get('/statistics',                     [AdminController::class, 'statistics'])->name('statistics');
    Route::get('/export/csv',                     [ExportController::class, 'exportCsv'])->name('export.csv');
    Route::get('/reports',                        [AgentReportController::class, 'adminIndex'])->name('reports.index');
    Route::patch('/reports/{report}/read',        [AgentReportController::class, 'markRead'])->name('reports.read');
    // Countries management
    Route::get('/countries',                      [CountryController::class, 'index'])->name('countries.index');
    Route::get('/countries/create',               [CountryController::class, 'create'])->name('countries.create');
    Route::post('/countries',                     [CountryController::class, 'store'])->name('countries.store');
    Route::get('/countries/{country}/edit',       [CountryController::class, 'edit'])->name('countries.edit');
    Route::put('/countries/{country}',            [CountryController::class, 'update'])->name('countries.update');
    Route::patch('/countries/{country}/toggle',   [CountryController::class, 'toggle'])->name('countries.toggle');
    Route::delete('/countries/{country}',         [CountryController::class, 'destroy'])->name('countries.destroy');
});

// Agent routes
Route::middleware(['auth', 'role:agent,admin'])->prefix('agent')->name('agent.')->group(function () {
    Route::get('/dashboard',                      [AgentController::class, 'dashboard'])->name('dashboard');
    Route::get('/transactions',                   [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/create',            [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions',                  [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/{transaction}',      [TransactionController::class, 'show'])->name('transactions.show');
    Route::get('/transactions/{transaction}/edit',[TransactionController::class, 'edit'])->name('transactions.edit');
    Route::get('/transactions/{transaction}/print',[TransactionController::class, 'printReceipt'])->name('transactions.print');
    Route::put('/transactions/{transaction}',      [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{transaction}',  [TransactionController::class, 'destroy'])->name('transactions.destroy');
    Route::get('/export/csv',                     [ExportController::class, 'exportCsv'])->name('export.csv');
    Route::post('/reports',                       [AgentReportController::class, 'store'])->name('reports.store');
});

// Language switcher
Route::get('/lang/{locale}', [LangController::class, 'switch'])->name('lang.switch');

// API helpers
Route::middleware('auth')->get('/api/countries/{country}/fee', [TransactionController::class, 'getFeeForCountry'])->name('api.country.fee');
