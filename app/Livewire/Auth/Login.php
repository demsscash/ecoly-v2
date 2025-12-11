<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;

#[Layout('layouts.guest')]
#[Title('Connexion - Ecoly')]
class Login extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|min:6')]
    public string $password = '';

    public bool $remember = false;

    public function login()
    {
        $this->validate();

        $user = User::where('email', $this->email)->first();

        if (!$user) {
            $this->addError('email', __('These credentials do not match our records.'));
            return;
        }

        // Check if account is locked
        if ($user->isLocked()) {
            $minutes = $user->lockMinutesRemaining();
            $this->addError('email', __('Account locked. Try again in :minutes minutes.', ['minutes' => $minutes]));
            return;
        }

        // Check if account is active
        if (!$user->is_active) {
            $this->addError('email', __('Your account has been deactivated.'));
            return;
        }

        // Verify password
        if (!Hash::check($this->password, $user->password)) {
            $user->incrementLoginAttempts();
            $this->addError('email', __('These credentials do not match our records.'));
            return;
        }

        // Success - reset attempts and login
        $user->resetLoginAttempts();
        
        Auth::login($user, $this->remember);
        
        session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
