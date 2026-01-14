<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Utilisateurs - Ecoly')]
class Users extends Component
{
    use WithPagination;

    public ?int $editingId = null;

    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $role = 'teacher';

    public bool $showModal = false;
    public string $search = '';

    protected function rules(): array
    {
        $emailRule = $this->editingId
            ? "unique:users,email,{$this->editingId}"
            : 'unique:users,email';

        $rules = [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:255', $emailRule],
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,secretary,teacher',
        ];

        // Password required only for new users
        if (!$this->editingId) {
            $rules['password'] = 'required|string|min:8';
        } else {
            $rules['password'] = 'nullable|string|min:8';
        }

        return $rules;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $user = User::findOrFail($id);

        $this->editingId = $user->id;
        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->role = $user->role->value;
        $this->password = '';

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'role' => UserRole::from($this->role),
        ];

        if ($this->editingId) {
            $user = User::findOrFail($this->editingId);

            // Update password only if provided
            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }

            $user->update($data);
            $this->dispatch('toast', message: __('User updated successfully.'), type: 'success');
        } else {
            $data['password'] = Hash::make($this->password);
            User::create($data);
            $this->dispatch('toast', message: __('User created successfully.'), type: 'success');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function toggleActive(int $id): void
    {
        $user = User::findOrFail($id);

        // Prevent deactivating self
        if ($user->id === auth()->id()) {
            $this->dispatch('toast', message: __('You cannot deactivate your own account.'), type: 'error');
            return;
        }

        $user->update(['is_active' => !$user->is_active]);

        $message = $user->is_active
            ? __('User activated successfully.')
            : __('User deactivated successfully.');

        $this->dispatch('toast', message: $message, type: 'success');
    }

    /**
     * Reset user password securely with email notification.
     */
    public function resetPassword(int $id): void
    {
        $user = User::findOrFail($id);

        // Generate secure password and send email to user
        $user->resetPasswordSecurely();

        $this->dispatch('toast', message: __('Un nouveau mot de passe sécurisé a été envoyé à l\'utilisateur par email.'), type: 'success');
    }

    public function delete(int $id): void
    {
        $user = User::findOrFail($id);

        // Prevent deleting self
        if ($user->id === auth()->id()) {
            $this->dispatch('toast', message: __('You cannot delete your own account.'), type: 'error');
            return;
        }

        $user->delete();
        $this->dispatch('toast', message: __('User deleted successfully.'), type: 'success');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->first_name = '';
        $this->last_name = '';
        $this->email = '';
        $this->phone = '';
        $this->password = '';
        $this->role = 'teacher';
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'ilike', "%{$this->search}%")
                      ->orWhere('last_name', 'ilike', "%{$this->search}%")
                      ->orWhere('email', 'ilike', "%{$this->search}%");
                });
            })
            ->orderBy('first_name')
            ->paginate(15);

        return view('livewire.admin.users', [
            'users' => $users,
            'roles' => [
                'admin' => __('Admin'),
                'secretary' => __('Secretary'),
                'teacher' => __('Teacher'),
            ],
        ]);
    }
}
