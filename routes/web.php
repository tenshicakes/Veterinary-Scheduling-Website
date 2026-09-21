<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Livewire\User\Home as UserHome;
use App\Livewire\User\Appointment as UserAppointment;


use App\Livewire\Admin\Home as AdminHome;

use App\Livewire\Assistant\Home as AssistantHome;
use App\Livewire\Assistant\Appointments as AssistantAppointments;

use App\Livewire\Superadmin\Home as SuperadminHome;


use App\Livewire\LandingPage;

Route::get('/', LandingPage::class)->name('landing.page');


Route::middleware(['auth', \App\Http\Middleware\PreventBackHistory::class])->group(function () {
    Route::get('/user/home', UserHome::class)->name('user.home');
    Route::get('/user/appointment', UserAppointment::class)->name('user.appointment');

    Route::get('/admin/home', AdminHome::class)->name('admin.home');

    Route::get('/assistant/home', AssistantHome::class)->name('assistant.home');
    Route::get('/assistant/appointments', AssistantAppointments::class)->name('assistant.appointments');
    
    Route::get('/superadmin/home', SuperadminHome::class)->name('superadmin.home');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    
    return redirect('/');
})->name('logout');