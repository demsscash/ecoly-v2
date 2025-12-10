<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

#[Layout('layouts.guest')]
#[Title('Connexion - Ecoly')]
class Login extends Component
{
    #[Rule('required|email')]
    public string $email = '';

    #[Rule('required|min:8')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle login attempt.
     */
    public function login(): void
    {
        $this->validate();

        // Find user by email
        $user = User::where('email', $this->email)->first();

        // Check if user exists
        if (!$user) {
            $this->addError('email', __('These credentials do not match our records.'));
            return;
        }

        // Check if account is locked
        if ($user->isLocked()) {
            $minutes = now()->diffInMinutes($user->locked_until);
            $this->addError('email', __('Account locked. Try again in :minutes minutes.', ['minutes' => $minutes]));
            return;
        }

        // Check if account is active
        if (!$user->is_active) {
            $this->addError('email', __('Your account has been deactivated.'));
            return;
        }

        // Attempt authentication
        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $user->resetFailedAttempts();
            session()->regenerate();
            $this->redirect('/dashboard', navigate: true);
        } else {
            $user->incrementFailedAttempts();
            $this->addError('email', __('These credentials do not match our records.'));
        }
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.auth.login');
    }
}
