<?php

namespace App\Livewire\Admin;

use App\Models\SchoolSetting;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
#[Title('Paramètres école - Ecoly')]
class SchoolSettings extends Component
{
    use WithFileUploads;

    public SchoolSetting $school;

    // Form fields
    public string $name_fr = '';
    public string $name_ar = '';
    public string $address_fr = '';
    public string $address_ar = '';
    public string $phone = '';
    public string $email = '';
    public string $academic_inspection = '';
    public string $school_code = '';
    public string $director_name_fr = '';
    public string $director_name_ar = '';

    // File uploads
    public $logo = null;
    public $stamp = null;
    public $signature = null;

    /**
     * Mount component with school settings.
     */
    public function mount(): void
    {
        $this->school = SchoolSetting::instance();
        
        $this->name_fr = $this->school->name_fr ?? '';
        $this->name_ar = $this->school->name_ar ?? '';
        $this->address_fr = $this->school->address_fr ?? '';
        $this->address_ar = $this->school->address_ar ?? '';
        $this->phone = $this->school->phone ?? '';
        $this->email = $this->school->email ?? '';
        $this->academic_inspection = $this->school->academic_inspection ?? '';
        $this->school_code = $this->school->school_code ?? '';
        $this->director_name_fr = $this->school->director_name_fr ?? '';
        $this->director_name_ar = $this->school->director_name_ar ?? '';
    }

    /**
     * Validation rules.
     */
    protected function rules(): array
    {
        return [
            'name_fr' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'address_fr' => 'nullable|string',
            'address_ar' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'academic_inspection' => 'nullable|string|max:255',
            'school_code' => 'nullable|string|max:50',
            'director_name_fr' => 'nullable|string|max:255',
            'director_name_ar' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'stamp' => 'nullable|image|max:2048',
            'signature' => 'nullable|image|max:2048',
        ];
    }

    /**
     * Save school settings.
     */
    public function save(): void
    {
        $this->validate();

        // Handle file uploads
        if ($this->logo) {
            if ($this->school->logo_path) {
                Storage::disk('public')->delete($this->school->logo_path);
            }
            $this->school->logo_path = $this->logo->store('school', 'public');
        }

        if ($this->stamp) {
            if ($this->school->stamp_path) {
                Storage::disk('public')->delete($this->school->stamp_path);
            }
            $this->school->stamp_path = $this->stamp->store('school', 'public');
        }

        if ($this->signature) {
            if ($this->school->signature_path) {
                Storage::disk('public')->delete($this->school->signature_path);
            }
            $this->school->signature_path = $this->signature->store('school', 'public');
        }

        // Update text fields
        $this->school->update([
            'name_fr' => $this->name_fr,
            'name_ar' => $this->name_ar,
            'address_fr' => $this->address_fr,
            'address_ar' => $this->address_ar,
            'phone' => $this->phone,
            'email' => $this->email,
            'academic_inspection' => $this->academic_inspection,
            'school_code' => $this->school_code,
            'director_name_fr' => $this->director_name_fr,
            'director_name_ar' => $this->director_name_ar,
        ]);

        // Reset file inputs
        $this->logo = null;
        $this->stamp = null;
        $this->signature = null;

        session()->flash('success', __('School settings saved successfully.'));
    }

    /**
     * Delete logo.
     */
    public function deleteLogo(): void
    {
        if ($this->school->logo_path) {
            Storage::disk('public')->delete($this->school->logo_path);
            $this->school->update(['logo_path' => null]);
        }
    }

    /**
     * Delete stamp.
     */
    public function deleteStamp(): void
    {
        if ($this->school->stamp_path) {
            Storage::disk('public')->delete($this->school->stamp_path);
            $this->school->update(['stamp_path' => null]);
        }
    }

    /**
     * Delete signature.
     */
    public function deleteSignature(): void
    {
        if ($this->school->signature_path) {
            Storage::disk('public')->delete($this->school->signature_path);
            $this->school->update(['signature_path' => null]);
        }
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.admin.school-settings');
    }
}
