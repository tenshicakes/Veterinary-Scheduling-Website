<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\User\Home as UserHome;
use App\Livewire\Admin\Home as AdminHome;
use App\Livewire\Assistant\Home as AssistantHome;
use App\Livewire\Superadmin\Home as SuperadminHome;
use App\Livewire\LandingPage;

Route::get('/', LandingPage::class)->name('landing.page');


Route::middleware(['auth', \App\Http\Middleware\PreventBackHistory::class])->group(function () {
    Route::get('/user/home', UserHome::class)->name('user.home');
    Route::get('/admin/home', AdminHome::class)->name('admin.home');
    Route::get('/assistant/home', AssistantHome::class)->name('assistant.home');
    Route::get('/superadmin/home', SuperadminHome::class)->name('superadmin.home');
});