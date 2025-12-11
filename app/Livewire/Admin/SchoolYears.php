<?php

namespace App\Livewire\Admin;

use App\Models\SchoolYear;
use App\Models\Trimester;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Années scolaires - Ecoly')]
class SchoolYears extends Component
{
    public ?int $editingId = null;
    public string $name = '';
    public string $start_date = '';
    public string $end_date = '';
    public int $payment_months = 9;

    public bool $showModal = false;
    public bool $showArchived = false;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'payment_months' => 'required|integer|in:9,10',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $year = SchoolYear::findOrFail($id);
        
        $this->editingId = $year->id;
        $this->name = $year->name;
        $this->start_date = $year->start_date->format('Y-m-d');
        $this->end_date = $year->end_date->format('Y-m-d');
        $this->payment_months = $year->payment_months;
        
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'payment_months' => $this->payment_months,
        ];

        if ($this->editingId) {
            $year = SchoolYear::findOrFail($this->editingId);
            $year->update($data);
            $this->dispatch('toast', message: __('School year updated successfully.'), type: 'success');
        } else {
            $year = SchoolYear::create($data);
            $this->createTrimesters($year);
            $this->dispatch('toast', message: __('School year created successfully.'), type: 'success');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    private function createTrimesters(SchoolYear $year): void
    {
        $startDate = $year->start_date;
        
        for ($i = 1; $i <= 3; $i++) {
            $trimesterStart = $startDate->copy()->addMonths(($i - 1) * 3);
            $trimesterEnd = $trimesterStart->copy()->addMonths(3)->subDay();
            
            Trimester::create([
                'school_year_id' => $year->id,
                'name_fr' => "Trimestre $i",
                'name_ar' => $this->getTrimesterNameAr($i),
                'number' => $i,
                'start_date' => $trimesterStart,
                'end_date' => min($trimesterEnd, $year->end_date),
                'status' => 'closed',
            ]);
        }
    }

    private function getTrimesterNameAr(int $number): string
    {
        return match($number) {
            1 => 'الفصل الأول',
            2 => 'الفصل الثاني',
            3 => 'الفصل الثالث',
            default => 'الفصل',
        };
    }

    public function activate(int $id): void
    {
        $year = SchoolYear::findOrFail($id);
        $year->activate();
        $this->dispatch('toast', message: __('School year activated successfully.'), type: 'success');
    }

    public function deactivate(int $id): void
    {
        $year = SchoolYear::findOrFail($id);
        $year->update(['is_active' => false]);
        $this->dispatch('toast', message: __('School year deactivated successfully.'), type: 'success');
    }

    public function archive(int $id): void
    {
        $year = SchoolYear::findOrFail($id);
        $year->archive();
        $this->dispatch('toast', message: __('School year archived successfully.'), type: 'warning');
    }

    public function delete(int $id): void
    {
        $year = SchoolYear::findOrFail($id);
        
        if ($year->trimesters()->exists()) {
            $year->trimesters()->delete();
        }
        
        $year->delete();
        $this->dispatch('toast', message: __('School year deleted successfully.'), type: 'success');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->payment_months = 9;
    }

    public function render()
    {
        $years = $this->showArchived 
            ? SchoolYear::archived()->orderByDesc('start_date')->get()
            : SchoolYear::notArchived()->orderByDesc('start_date')->get();

        return view('livewire.admin.school-years', [
            'years' => $years,
        ]);
    }
}
