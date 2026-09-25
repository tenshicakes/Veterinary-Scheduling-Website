<?php

use App\Livewire\Admin\Home as AdminHome;
use App\Livewire\Assistant\Appointments as AssistantAppointments;
use App\Livewire\Assistant\Home as AssistantHome;
use App\Livewire\Superadmin\Home as SuperadminHome;
use App\Livewire\User\Appointment as UserAppointment;
use App\Livewire\User\Home as UserHome;
use App\Livewire\User\Profile as UserProfile;
use App\Livewire\User\Services as UserServices;
use App\Http\Middleware\PreventBackHistory;
use App\Livewire\LandingPage;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingPage::class)->name('landing.page')->name('login');

// Email verification routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/user/profile')->with('verified', 'Email verified successfully!');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('verification_sent', 'Verification link sent!');
})->middleware(['auth', 'throttle:30,1'])->name('verification.send');

Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    Route::get('/user/home', UserHome::class)->name('user.home');
    Route::get('/user/appointment', UserAppointment::class)->name('user.appointment')->middleware('verified');
    Route::get('/user/profile', UserProfile::class)->name('user.profile');
    Route::get('/user/services', UserServices::class)->name('user.services');

    Route::get('/admin/home', AdminHome::class)->name('admin.home');

    Route::get('/assistant/home', AssistantHome::class)->name('assistant.home');
    Route::get('/assistant/appointments', AssistantAppointments::class)->name('assistant.appointments');

    Route::get('/superadmin/home', SuperadminHome::class)->name('superadmin.home');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/?logout=1')->header('Clear-Site-Data', '"cache", "cookies", "storage", "executionContexts"');
})->name('logout');