<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.adminmaster')]
class Profile extends Component
{
    use WithFileUploads;

    public $activeTab = 'profile';

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

    public $new_password_confirmation;

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
        $this->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:users_table,email,'.Auth::id().',userID',
            'phone_number' => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'address' => 'nullable|string|max:255',
            'new_photo' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();

        // Handle Photo Upload
        if ($this->new_photo) {
            // Delete old photo if exists
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $path = $this->new_photo->store('profile_photos', 'public');
            $user->profile_image = $path;
        }

        // Update text data
        $user->fullname = $this->fullname;
        $user->email = $this->email;
        $user->phone_number = $this->phone_number ?? '';
        $user->address = $this->address ?? '';
        $user->save();

        $this->new_photo = null;
        session()->flash('success_profile', 'Profile updated successfully!');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (! Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The current password provided is incorrect.');

            return;
        }

        $user->password = Hash::make($this->new_password);
        $user->save();

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('success_password', 'Password updated successfully!');
    }

    public function render()
    {
        return view('livewire.admin.profile');
    }
}
