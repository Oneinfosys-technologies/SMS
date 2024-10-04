<?php

use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Config\MailTemplateController;
use App\Http\Controllers\SignedMediaController;
use App\Http\Controllers\Student\OnlineRegistrationController;
use App\Http\Controllers\Student\PaymentController;
use Illuminate\Support\Facades\Route;

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

Route::get('/app/config/mail-template/{mail_template}', [MailTemplateController::class, 'detail'])
    ->name('config.mail-template.detail')
    ->middleware('permission:config:store');

Route::get('/media/{media}/{conversion?}', SignedMediaController::class)->name('media');

Route::prefix('app')
    ->middleware(['auth:sanctum', 'two.factor.security', 'screen.lock', 'under.maintenance', 'user.config'])
    ->group(function () {
        Route::get('students/{student}/transactions/{transaction}/export', [PaymentController::class, 'export'])
            ->name('students.transactions.export');
    });

Route::get('/auth/{provider}/redirect', [OAuthController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [OAuthController::class, 'callback']);

Route::get('online-registrations/{number}/download', [OnlineRegistrationController::class, 'download'])->name('online-registrations.download');

Route::redirect('/log', 'log-viewer', 301);

Route::view('/livewire-test', 'livewire-test');

// app route
Route::redirect('/app', '/app/login');

Route::get('/app/login', function () {
    return view('app');
})->where('vue', '[\/\w\.-]*')->name('app');

Route::get('/app/{vue?}', function () {
    return view('app');
})->where('vue', '[\/\w\.-]*')->name('app.dashboard');

// Fallback route
Route::fallback(function () {
    abort(404);
});
