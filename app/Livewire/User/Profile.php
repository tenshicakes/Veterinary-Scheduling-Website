<?php
namespace App\Livewire\User;

use Livewire\Component;
use App\Models\Info;
use App\Models\Appointment;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\User;
#[Layout('layouts.usermaster')]
class Profile extends Component
{
    use WithFileUploads; 

    public $activeTab = 'profile'; 

    // Profile Information variables
    public $fullname;
    public $email;
    public $phone_number;
    public $address;
    
    // Photo Upload variable
    public $new_photo;

    // Security variables
    public $current_password;
    public $new_password;
    public $new_password_confirmation; 

    // --- MY PETS VARIABLES ---
    public $isPetModalOpen = false;
    public $isHistoryModalOpen = false;

    // Pet Form variables
    public $pet_infoID = null;
    public $petname = '';
    public $petspecies = '';
    public $petbreed = '';
    public $petimage; 
    public $existing_petimage; 

    // Medical History variables
    public $petHistory = [];
    public $historyPetName = '';





    public function mount()
    {
        $user = Auth::user();
        $this->fullname = $user->fullname;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number;
        $this->address = $user->address;
        $this->activeTab = request()->query('tab', 'profile');
    }






    //Update profile picture
    public function updateProfile()
    {
        $user = Auth::user();

        $this->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:users_table,email,' . $user->userID . ',userID',
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        if ($this->new_photo) {
            $this->validate(['new_photo' => 'image|max:2048']); // Max 2MB

            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $path = $this->new_photo->store('profile_photos', 'public');
            $user->profile_image = $path;
        }


        $emailChanged = $user->email !== $this->email;
        $user->fullname = $this->fullname;
        $user->email = $this->email;
        $user->phone_number = $this->phone_number;
        $user->address = $this->address;
        
        // Do the verification again if email changed
        if ($emailChanged) {
            $user->email_verified_at = null;
            $user->sendEmailVerificationNotification();
        }
        
        $user->save();

        $this->new_photo = null; 
        
        $message = 'Profile updated successfully!';
        if ($emailChanged) {
            $message .= ' Email changed - please verify your new email address.';
        }
        session()->flash('success_profile', $message);
    }







    //Update password
    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The current password provided is incorrect.');
            return;
        }

        $user->password = Hash::make($this->new_password);
        $user->save();

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('success_password', 'Password updated successfully!');
    }







    //Send SMTP email verification

    public function resendVerificationEmail()
    {
        $user = Auth::user();
        
        if ($user->hasVerifiedEmail()) {
            $this->addError('email', 'Email is already verified.');
            return;
        }
        
        $user->sendEmailVerificationNotification();
        session()->flash('verification_sent', 'Verification link sent! Check your inbox.');
    }









    // --- MY PETS TAB ---

    //Add new pet
    public function openAddPetModal()
    {
        $this->reset(['pet_infoID', 'petname', 'petspecies', 'petbreed', 'petimage', 'existing_petimage']);
        $this->resetErrorBag();
        $this->isPetModalOpen = true;
    }

    //Edit pet 
    public function openEditPetModal($id)
    {
        $this->resetErrorBag();
        $pet = \App\Models\Info::where('infoID', $id)->where('userID', Auth::id())->firstOrFail();
        
        if ($pet) {
            $this->pet_infoID = $pet->infoID;
            $this->petname = $pet->petname;
            $this->petspecies = $pet->petspecies;
            $this->petbreed = $pet->petbreed;
            $this->existing_petimage = $pet->petimage;
            $this->petimage = null;
            $this->isPetModalOpen = true;
        }
    }

    public function closePetModal()
    {
        $this->isPetModalOpen = false;
    }

    public function savePet()
    {
        $this->validate([
            'petname' => 'required|string|max:255',
            'petspecies' => 'required|string|max:255',
            'petbreed' => 'nullable|string|max:255',
            'petimage' => 'nullable|image|max:2048'
        ]);


        $pet = $this->pet_infoID ? \App\Models\Info::find($this->pet_infoID) : new \App\Models\Info();
        
        //Upload pet profie
        if ($this->petimage) {
      
            if ($pet->petimage) {
                Storage::disk('public')->delete($pet->petimage);
            }
            $path = $this->petimage->store('pet_photos', 'public');
            $pet->petimage = $path;
        }

        $pet->userID = Auth::id();
        $pet->petname = $this->petname;
        $pet->petspecies = $this->petspecies;
        $pet->petbreed = $this->petbreed;
        
        if (!$this->pet_infoID) {
            $pet->is_archived = false; 
        }
        
        $pet->save();

        $this->isPetModalOpen = false;
        session()->flash('success_pet', 'Pet information saved successfully!');
    }


    public function archivePet($id)
    {
        \App\Models\Info::where('infoID', $id)->where('userID', Auth::id())->update(['is_archived' => true]);
        session()->flash('success_pet', 'Pet archived successfully.');
    }








    // --- MEDICAL HISTORY OF PETS ---

    public function openHistoryModal($id)
    {
        $pet = \App\Models\Info::where('infoID', $id)->where('userID', Auth::id())->first();
        
        if ($pet) {
            $this->historyPetName = $pet->petname;
            
            // only "completed" stats
            $this->petHistory = \App\Models\Appointment::select(
                    'appointment_table.*', 
                    'service_table.servicename'
                )
                ->leftJoin('service_table', 'appointment_table.serviceID', '=', 'service_table.serviceID')
                ->where('appointment_table.infoID', $id)
                ->where('appointment_table.status', 'Completed')
                ->orderBy('appointment_table.appointmentdate', 'desc')
                ->get();
            
            $this->isHistoryModalOpen = true;
        }
    }

    public function closeHistoryModal()
    {
        $this->isHistoryModalOpen = false;
    }






    public function render()
    {
        // get only active pets
        $activePets = \App\Models\Info::where('userID', Auth::id())
                                      ->where('is_archived', false)
                                      ->get();



        return view('livewire.user.profile', [
            'pets' => $activePets,
        ]);
    }
}