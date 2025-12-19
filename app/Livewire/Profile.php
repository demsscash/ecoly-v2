<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

#[Layout('layouts.app')]
#[Title('Mon Profil - Ecoly')]
class Profile extends Component
{
    use WithFileUploads;

    // Profile info
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public $photo;
    public ?string $current_photo_url = null;

    // Password change
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    // UI state
    public string $activeTab = 'profile';

    public function mount(): void
    {
        $user = auth()->user();
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->current_photo_url = $user->photo_path 
            ? Storage::url($user->photo_path) 
            : 'https://ui-avatars.com/api/?name=' . urlencode($user->first_name . ' ' . $user->last_name);
    }

    public function updateProfile(): void
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:2048',
        ]);

        $user = auth()->user();
        
        // Handle photo upload
        if ($this->photo) {
            // Delete old photo if exists
            if ($user->photo_path) {
                Storage::disk('public')->delete($user->photo_path);
            }
            
            $photoPath = $this->photo->store('profile-photos', 'public');
            $user->photo_path = $photoPath;
            $this->current_photo_url = Storage::url($photoPath);
        }

        // Update user info
        $user->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);

        if (isset($photoPath)) {
            $user->photo_path = $photoPath;
            $user->save();
        }

        $this->photo = null;
        
        $this->dispatch('toast', message: __('Profile updated successfully.'), type: 'success');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = auth()->user();

        // Verify current password
        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', __('Current password is incorrect.'));
            return;
        }

        // Update password
        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        // Reset fields
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        $this->dispatch('toast', message: __('Password updated successfully.'), type: 'success');
    }

    public function removePhoto(): void
    {
        $user = auth()->user();
        
        if ($user->photo_path) {
            Storage::disk('public')->delete($user->photo_path);
            $user->photo_path = null;
            $user->save();
            
            $this->current_photo_url = 'https://ui-avatars.com/api/?name=' . urlencode($user->first_name . ' ' . $user->last_name);
            
            $this->dispatch('toast', message: __('Photo removed successfully.'), type: 'success');
        }
    }

    public function render()
    {
        return view('livewire.profile');
    }
}
