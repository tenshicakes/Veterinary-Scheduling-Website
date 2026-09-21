<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

    // The toggle variable for the eye icon
    public $showPassword = false;

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
        $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            session()->regenerate();
            $role = Auth::user()->role;

            if ($role === 'Admin') {
                return redirect()->route('admin.home');
            }
            if ($role === 'Assistant') {
                return redirect()->route('assistant.home');
            }
            if ($role === 'Superadmin') {
                return redirect()->route('superadmin.home');
            }

            return redirect()->route('user.home');
        }

        $this->addError('auth_error', 'Invalid email or password.');
    }

    public function register()
    {
        $this->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:users_table,email',
            'password' => 'required|min:6',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'fullname' => $this->fullname,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'phone_number' => $this->phone_number ?? '',
            'address' => $this->address ?? '',
            'role' => 'User',
        ]);

        Auth::login($user);

        return redirect()->route('user.home');
    }

    public function render()
    {
        return view('livewire.landing-page');
    }
}
