<?php

namespace App\Livewire\Admin;

use App\Models\SchoolYear;
use App\Imports\StudentsImport as StudentsImporter;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class StudentsImport extends Component
{
    use WithFileUploads;

    public $file = null;
    public bool $showResults = false;
    public int $imported = 0;
    public int $skipped = 0;
    public array $importErrors = [];

    protected function rules(): array
    {
        return [
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ];
    }

    public function import(): void
    {
        $this->validate();

        $schoolYear = SchoolYear::where('is_active', true)->first();

        if (!$schoolYear) {
            $this->dispatch('toast', message: __('No active school year.'), type: 'error');
            return;
        }

        try {
            $importer = new StudentsImporter($schoolYear);
            Excel::import($importer, $this->file->getRealPath());

            $this->imported = $importer->getImported();
            $this->skipped = $importer->getSkipped();
            $this->importErrors = $importer->getErrors();
            $this->showResults = true;

            if ($this->imported > 0) {
                $this->dispatch('toast', message: __(':count student(s) imported successfully.', ['count' => $this->imported]), type: 'success');
            }
        } catch (\Exception $e) {
            $this->dispatch('toast', message: __('Import error: ') . $e->getMessage(), type: 'error');
        }

        $this->file = null;
    }

    public function downloadTemplate()
    {
        $headers = ['nom', 'prenom', 'nom_ar', 'prenom_ar', 'date_naissance', 'lieu_naissance', 'lieu_naissance_ar', 'genre', 'nationalite', 'nni', 'classe', 'tuteur', 'tuteur_ar', 'telephone', 'telephone_2', 'email', 'profession', 'adresse', 'adresse_ar', 'date_inscription', 'ecole_precedente'];
        $example = ['Sall', 'Mamadou', 'سال', 'محمد', '15/03/2015', 'Nouakchott', 'نواكشوط', 'M', 'Mauritanienne', '1234567890', '1A', 'Ahmed Sall', 'أحمد سال', '22334455', '', 'ahmed@email.com', 'Commerçant', '123 Rue', '', now()->format('d/m/Y'), ''];

        $callback = function () use ($headers, $example) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $headers, ';');
            fputcsv($file, $example, ';');
            fclose($file);
        };

        return response()->streamDownload($callback, 'modele_import_eleves.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function resetImport(): void
    {
        $this->reset(['file', 'showResults', 'imported', 'skipped', 'importErrors']);
    }

    public function render()
    {
        return view('livewire.admin.students-import');
    }
}
