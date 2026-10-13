<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class LandingPage extends Component
{
    // Toggle state
    public $isLogin = true;

    public $fullname;

    public $email;

    public $password;

    public $phone_number;

    public $address;

    public $profile_image;

    // The toggle variable for the eye icon
    public $showPassword = false;

    // Rate limiting properties
    public $loginCooldown = 0;

    public $registerCooldown = 0;

    public function toggleForm()
    {
        $this->isLogin = ! $this->isLogin;
        $this->resetErrorBag();
    }

    // The method for the eye icon button
    public function togglePassword()
    {
        $this->showPassword = ! $this->showPassword;
    }

    public function authenticate()
    {
        $ip = request()->ip();
        $loginKey = 'login:'.$ip;
        $maxAttempts = 5;
        $decaySeconds = 600; // 10 minutes in seconds

        if (RateLimiter::tooManyAttempts($loginKey, $maxAttempts)) {
            $this->loginCooldown = RateLimiter::availableIn($loginKey);
            $this->addError('auth_error', 'Too many login attempts. Please try again in '.$this->loginCooldown.' seconds.');
            return;
        }

        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $this->email)->where('is_active', 1)->first();
        if (! $user) {
            RateLimiter::increment($loginKey, $decaySeconds);
            $this->addError('auth_error', 'Invalid email or password.');
            return;
        }

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password, 'is_active' => 1])) {
            RateLimiter::clear($loginKey);
            session()->regenerate();
            $role = Auth::user()->role;

            if ($role === 'Admin') return redirect()->route('admin.home');
            if ($role === 'Assistant') return redirect()->route('assistant.home');
            if ($role === 'Superadmin') return redirect()->route('superadmin.home');

            return redirect()->route('user.home');
        }

        RateLimiter::increment($loginKey, $decaySeconds);
        $this->addError('auth_error', 'Invalid email or password.');
    }

    public function register()
    {
        $ip = request()->ip();
        $registerKey = 'register:'.$ip;
        $maxAttempts = 2;
        $decaySeconds = 86400; // 24 hours in seconds

        if (RateLimiter::tooManyAttempts($registerKey, $maxAttempts)) {
            $this->registerCooldown = RateLimiter::availableIn($registerKey);
            $this->addError('register_error', 'Too many registration attempts. You can only register 2 times per day. Try again in '.$this->registerCooldown.' seconds.');
            return;
        }

        $this->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:users_table,email',
            'password' => 'required|min:8',
            'phone_number' => 'required|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'address' => 'nullable|string|max:255',
            'profile_image' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'fullname' => $this->fullname,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'phone_number' => $this->phone_number ?? '',
            'address' => $this->address ?? '',
            'profile_image' => $this->profile_image ?? '',
            'role' => 'User',
            'is_active' => true,
        ]);

        RateLimiter::increment($registerKey, $decaySeconds);

        $user->sendEmailVerificationNotification();
        session()->flash('verification_notice', 'Registration successful! Please check your email to verify your account.');

        $this->reset(['fullname', 'email', 'password', 'phone_number', 'address', 'profile_image']);
        $this->isLogin = true;
    }

    public function render()
    {
        $ip = request()->ip();
        
        
        $loginKey = 'login:'.$ip;
        if (RateLimiter::tooManyAttempts($loginKey, 5)) {
            $this->loginCooldown = RateLimiter::availableIn($loginKey);
        } else {
            $this->loginCooldown = 0;
        }

       
        $registerKey = 'register:'.$ip;
        if (RateLimiter::tooManyAttempts($registerKey, 2)) {
            $this->registerCooldown = RateLimiter::availableIn($registerKey);
        } else {
            $this->registerCooldown = 0;
        }

        return view('livewire.landing-page');
    }
}
