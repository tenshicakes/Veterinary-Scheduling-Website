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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', LandingPage::class)->name('landing.page')->name('login');

Route::middleware(['auth', PreventBackHistory::class])->group(function () {
    Route::get('/user/home', UserHome::class)->name('user.home');
    Route::get('/user/appointment', UserAppointment::class)->name('user.appointment');
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
