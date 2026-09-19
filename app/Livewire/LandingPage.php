<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


#[Layout('layouts.guest')] 
class LandingPage extends Component
{
    // Toggle state
    public $isLogin = true; 

    // Form fields
    public $fullname;
    public $email;
    public $password;

    public function toggleForm()
    {
        $this->isLogin = !$this->isLogin;
        $this->resetErrorBag();
    }

    public function authenticate()
    {
        $this->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            session()->regenerate();
            $role = Auth::user()->role;
            
            if ($role === 'Admin') return redirect()->route('admin.home');
            if ($role === 'Assistant') return redirect()->route('assistant.home');
            if ($role === 'Superadmin') return redirect()->route('superadmin.home');
            
            return redirect()->route('user.home');
        }

        $this->addError('email', 'These credentials do not match our records.');
    }

    public function register()
    {
        $this->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:users_table,email',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'fullname' => $this->fullname,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => 'User', // Default role for new sign-ups
        ]);

        Auth::login($user);
        return redirect()->route('user.home');
    }

    public function render()
    {
        return view('livewire.landing-page');
    }
}