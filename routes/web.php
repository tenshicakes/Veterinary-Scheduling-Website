<?php

use App\Http\Middleware\PreventBackHistory;
// ADMIN PAGES
use App\Livewire\Admin\Appointments as AdminAppointments;
use App\Livewire\Admin\Clinicmanage as AdminClinicmanage;
use App\Livewire\Admin\Home as AdminHome;
use App\Livewire\Admin\Profile as AdminProfile;
use App\Livewire\Admin\Usermanage as AdminUsermanage;

// ASSISTANT PAGES
use App\Livewire\Assistant\Appointments as AssistantAppointments;
use App\Livewire\Assistant\Home as AssistantHome;
use App\Livewire\Assistant\Profile as AssistantProfile;

use App\Livewire\LandingPage;

// SUPERADMIN PAGES
use App\Livewire\Superadmin\Home as SuperadminHome;
use App\Livewire\Superadmin\Appointments as SuperadminAppointments;
use App\Livewire\Superadmin\Clinicmanage as SuperadminClinicmanage;
use App\Livewire\Superadmin\Profile as SuperadminProfile;
use App\Livewire\Superadmin\Usermanage as SuperadminUsermanage;

// USER PAGES
use App\Livewire\User\Appointment as UserAppointment;
use App\Livewire\User\Home as UserHome;
use App\Livewire\User\Profile as UserProfile;
use App\Livewire\User\Services as UserServices;


use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingPage::class)->name('login');

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

// ALL AUTHENTICATED ROUTES
Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    
    // --- USER ONLY ROUTES ---
    Route::middleware(['role:User'])->group(function () {
        Route::get('/user/home', UserHome::class)->name('user.home');
        Route::get('/user/appointment', UserAppointment::class)->name('user.appointment')->middleware('verified');
        Route::get('/user/profile', UserProfile::class)->name('user.profile');
        Route::get('/user/services', UserServices::class)->name('user.services');
    });

    // --- ASSISTANT ONLY ROUTES ---
    Route::middleware(['role:Assistant'])->group(function () {
        Route::get('/assistant/home', AssistantHome::class)->name('assistant.home');
        Route::get('/assistant/appointments', AssistantAppointments::class)->name('assistant.appointments');
        Route::get('/assistant/profile', AssistantProfile::class)->name('assistant.profile');
    });

    // --- ADMIN ONLY ROUTES ---
    Route::middleware(['role:Admin'])->group(function () {
        Route::get('/admin/home', AdminHome::class)->name('admin.home');
        Route::get('/admin/appointments', AdminAppointments::class)->name('admin.appointments');
        Route::get('/admin/profile', AdminProfile::class)->name('admin.profile');
        Route::get('/admin/clinicmanage', AdminClinicmanage::class)->name('admin.clinicmanage');
        Route::get('/admin/usermanage', AdminUsermanage::class)->name('admin.usermanage');
    });

    // --- SUPERADMIN ONLY ROUTES ---
    Route::middleware(['role:Superadmin'])->group(function () {
        Route::get('/superadmin/home', SuperadminHome::class)->name('superadmin.home');
        Route::get('/superadmin/appointments', SuperadminAppointments::class)->name('superadmin.appointments');
        Route::get('/superadmin/clinicmanage', SuperadminClinicmanage::class)->name('superadmin.clinicmanage');
        Route::get('/superadmin/profile', SuperadminProfile::class)->name('superadmin.profile');
        Route::get('/superadmin/usermanage', SuperadminUsermanage::class)->name('superadmin.usermanage');
    });

});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/?logout=1')->header('Clear-Site-Data', '"cache", "cookies", "storage", "executionContexts"');
})->name('logout');