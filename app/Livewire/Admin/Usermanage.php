<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

#[Layout('layouts.adminmaster')] 
class Usermanage extends Component
{

    public $search = '';
    public $roleFilter = '';

    public $isModalOpen = false;
    public $isEditMode = false;
    public $editUserId = null;
    
    public $fullname = '';
    public $email = '';
    public $phone_number = '';
    public $address = '';
    public $role = 'Assistant'; 
    public $password = '';

    public function openAddModal()
    {
        $this->resetErrorBag();
        $this->reset(['editUserId', 'fullname', 'email', 'phone_number', 'address', 'password']);
        $this->role = 'Assistant';
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function openEditModal($id)
    {
        $this->resetErrorBag();
        $user = User::where('userID', $id)->first();
        
        if ($user) {
            $this->editUserId = $user->userID;
            $this->fullname = $user->fullname;
            $this->email = $user->email;
            $this->phone_number = $user->phone_number;
            $this->address = $user->address;
            $this->role = $user->role;
            $this->password = ''; 
            
            $this->isEditMode = true;
            $this->isModalOpen = true;
        }
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    public function saveUser()
    {
        $rules = [
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users_table,email,' . ($this->isEditMode ? $this->editUserId : 'NULL') . ',userID',
            'role' => 'required|in:User,Assistant,Admin',
            'phone_number' => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'address' => 'nullable|string|max:500',
        ];

        
        if (!$this->isEditMode) {
            $rules['password'] = 'required|min:8';
        } else {
            $rules['password'] = 'nullable|min:8';
        }

        $this->validate($rules);

        if ($this->isEditMode) {
            $user = User::where('userID', $this->editUserId)->first();
            
            $user->fullname = $this->fullname;
            $user->email = $this->email;
            $user->phone_number = $this->phone_number;
            $user->address = $this->address;
            $user->role = $this->role;

            
            if (!empty($this->password)) {
                $user->password = Hash::make($this->password);
            }
            
            $user->save();
            session()->flash('success_user', 'Account updated successfully.');
        } else {
            User::create([
                'fullname' => $this->fullname,
                'email' => $this->email,
                'phone_number' => $this->phone_number,
                'address' => $this->address,
                'role' => $this->role,
                'password' => Hash::make($this->password),
                'is_active' => true, 
            ]);
            session()->flash('success_user', 'New account created successfully.');
        }

        $this->closeModal();
    }

    public function toggleActiveStatus($id)
    {
        $user = User::where('userID', $id)->first();
        if ($user && $user->role !== 'Superadmin') {
            $user->update(['is_active' => !$user->is_active]);
            $status = $user->is_active ? 'activated' : 'deactivated';
            session()->flash('success_user', "Account has been {$status}.");
        }
    }

    public function render()
    {
        $query = User::where('role', '!=', 'Superadmin');

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('fullname', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->roleFilter)) {
            $query->where('role', $this->roleFilter);
        }

        
        $users = $query->orderBy('role', 'asc')->orderBy('fullname', 'asc')->get();

        return view('livewire.admin.usermanage', [
            'users' => $users
        ]);
    }
}