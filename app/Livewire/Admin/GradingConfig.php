<?php

namespace App\Livewire\Admin;

use App\Models\GradingConfig as GradingConfigModel;
use App\Models\SchoolYear;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Pondération - Ecoly')]
class GradingConfig extends Component
{
    public ?int $selectedYearId = null;
    
    public int $control_weight = 40;
    public int $exam_weight = 60;
    public string $mention_excellent = '16.00';
    public string $mention_very_good = '14.00';
    public string $mention_good = '12.00';
    public string $mention_fairly_good = '10.00';
    public string $passing_grade = '10.00';

    public function mount(): void
    {
        $activeYear = SchoolYear::active();
        $this->selectedYearId = $activeYear?->id ?? SchoolYear::latest()->first()?->id;
        $this->loadConfig();
    }

    public function updatedSelectedYearId(): void
    {
        $this->loadConfig();
    }

    public function updatedControlWeight(): void
    {
        // Auto-adjust exam weight
        $this->exam_weight = 100 - $this->control_weight;
    }

    public function updatedExamWeight(): void
    {
        // Auto-adjust control weight
        $this->control_weight = 100 - $this->exam_weight;
    }

    private function loadConfig(): void
    {
        if (!$this->selectedYearId) {
            return;
        }

        $config = GradingConfigModel::firstOrNew(
            ['school_year_id' => $this->selectedYearId],
            [
                'control_weight' => 40,
                'exam_weight' => 60,
                'mention_excellent' => 16.00,
                'mention_very_good' => 14.00,
                'mention_good' => 12.00,
                'mention_fairly_good' => 10.00,
                'passing_grade' => 10.00,
            ]
        );

        $this->control_weight = $config->control_weight;
        $this->exam_weight = $config->exam_weight;
        $this->mention_excellent = (string) $config->mention_excellent;
        $this->mention_very_good = (string) $config->mention_very_good;
        $this->mention_good = (string) $config->mention_good;
        $this->mention_fairly_good = (string) $config->mention_fairly_good;
        $this->passing_grade = (string) $config->passing_grade;
    }

    public function save(): void
    {
        $this->validate([
            'control_weight' => 'required|integer|min:0|max:100',
            'exam_weight' => 'required|integer|min:0|max:100',
            'mention_excellent' => 'required|numeric|min:0|max:20',
            'mention_very_good' => 'required|numeric|min:0|max:20',
            'mention_good' => 'required|numeric|min:0|max:20',
            'mention_fairly_good' => 'required|numeric|min:0|max:20',
            'passing_grade' => 'required|numeric|min:0|max:20',
        ]);

        // Validate weights sum to 100
        if ($this->control_weight + $this->exam_weight !== 100) {
            $this->dispatch('toast', message: __('Control and exam weights must sum to 100%.'), type: 'error');
            return;
        }

        // Validate mention order
        if ($this->mention_excellent <= $this->mention_very_good ||
            $this->mention_very_good <= $this->mention_good ||
            $this->mention_good <= $this->mention_fairly_good) {
            $this->dispatch('toast', message: __('Mention thresholds must be in descending order.'), type: 'error');
            return;
        }

        GradingConfigModel::updateOrCreate(
            ['school_year_id' => $this->selectedYearId],
            [
                'control_weight' => $this->control_weight,
                'exam_weight' => $this->exam_weight,
                'mention_excellent' => $this->mention_excellent,
                'mention_very_good' => $this->mention_very_good,
                'mention_good' => $this->mention_good,
                'mention_fairly_good' => $this->mention_fairly_good,
                'passing_grade' => $this->passing_grade,
            ]
        );

        $this->dispatch('toast', message: __('Grading configuration saved successfully.'), type: 'success');
    }

    public function render()
    {
        $years = SchoolYear::orderByDesc('start_date')->get();

        return view('livewire.admin.grading-config', [
            'years' => $years,
        ]);
    }
}
