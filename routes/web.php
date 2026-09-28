<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\DirectMessageController;
use App\Http\Controllers\ItemReviewController;
use App\Http\Controllers\Admin\AdminController;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index'])->name('home');

// Catalog — public read
Route::get('/catalog',        [ItemController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{item}', [ItemController::class, 'show'])->name('catalog.show');

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    /* ── Donor ── */
    Route::prefix('donor')->name('donor.')->middleware('role:donor')->group(function () {
        Route::get('items/create', [ItemController::class, 'create'])->name('items.create');
        Route::post('items',       [ItemController::class, 'store'])->name('items.store');
        Route::get('items',        [ItemController::class, 'myItems'])->name('items');
        Route::delete('items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');

        Route::get('requests',              [RequestController::class, 'donorRequests'])->name('requests');
        Route::patch('requests/{donationRequest}/approve',  [RequestController::class, 'approve'])->name('requests.approve');
        Route::patch('requests/{donationRequest}/reject',   [RequestController::class, 'reject'])->name('requests.reject');
        Route::patch('requests/{donationRequest}/handover', [RequestController::class, 'handover'])->name('requests.handover');
    });

    /* ── Recipient ── */
    Route::prefix('recipient')->name('recipient.')->middleware('role:recipient')->group(function () {
        Route::get('requests',  [RequestController::class, 'myRequests'])->name('requests');
        Route::patch('requests/{donationRequest}/complete', [RequestController::class, 'complete'])->name('requests.complete');
        Route::patch('requests/{donationRequest}/confirm',  [RequestController::class, 'recipientConfirm'])->name('requests.confirm');
    });

    // Submission form — any authenticated user can submit (will be validated in FormRequest)
    Route::post('requests', [RequestController::class, 'store'])->name('requests.store');

    /* ── Chat donatur↔penerima ── */
    Route::get('chat/{donationRequest}',  [DirectMessageController::class, 'show'])->name('chat.show');
    Route::post('chat/{donationRequest}', [DirectMessageController::class, 'store'])->name('chat.store');

    /* ── Ulasan ── */
    Route::post('reviews', [ItemReviewController::class, 'store'])->name('reviews.store');

    /* ── Admin ── */
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/',                                          [AdminController::class, 'dashboard'])->name('dashboard');
        Route::patch('items/{item}/verify',                      [AdminController::class, 'verifyItem'])->name('items.verify');
        Route::patch('items/{item}/reject',                      [AdminController::class, 'rejectItem'])->name('items.reject');
        Route::patch('requests/{donationRequest}/confirm',       [AdminController::class, 'confirmHandover'])->name('requests.confirm');
    });
});

/*
|--------------------------------------------------------------------------
| Auth routes (Laravel Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
