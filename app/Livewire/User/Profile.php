<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

#[Layout('layouts.usermaster')]
class Profile extends Component
{
    use WithFileUploads; // This enables file uploading in Livewire

    public $activeTab = 'profile'; // Default tab

    // Profile Information Variables
    public $fullname;
    public $email;
    public $phone_number;
    public $address;
    
    // Photo Upload Variable
    public $new_photo;

    // Security Variables
    public $current_password;
    public $new_password;
    public $new_password_confirmation; // Must match 'new_password' for Laravel validation

    public function mount()
    {
        $user = Auth::user();
        $this->fullname = $user->fullname;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number;
        $this->address = $user->address;
    }

    public function updateProfile()
    {
        $user = Auth::user();

        $this->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:users_table,email,' . $user->userID . ',userID',
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        // 1. Handle Photo Upload if the user selected a new file
        if ($this->new_photo) {
            $this->validate(['new_photo' => 'image|max:2048']); // Max 2MB
            // Saves to storage/app/public/profile_photos and automatically generates a unique filename!
            $path = $this->new_photo->store('profile_photos', 'public'); 
            $user->profile_image = $path;
        }

        // 2. Save Text Data
        $user->fullname = $this->fullname;
        $user->email = $this->email;
        $user->phone_number = $this->phone_number;
        $user->address = $this->address;
        $user->save();

        $this->new_photo = null; // Clear the temporary upload
        session()->flash('success_profile', 'Profile updated successfully!');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Security Check: Verify the current password is correct
        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The current password provided is incorrect.');
            return;
        }

        $user->password = Hash::make($this->new_password);
        $user->save();

        // Clear the fields after success
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('success_password', 'Password updated successfully!');
    }

    public function render()
    {
        return view('livewire.user.profile');
    }
}