<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\SchoolYear;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class StudentsImport implements ToCollection, WithHeadingRow
{
    protected SchoolYear $schoolYear;
    protected array $errors = [];
    protected int $imported = 0;
    protected int $skipped = 0;

    public function __construct(SchoolYear $schoolYear)
    {
        $this->schoolYear = $schoolYear;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2;
            
            try {
                if (empty($row['nom']) || empty($row['prenom'])) {
                    $this->skipped++;
                    continue;
                }

                $class = null;
                if (!empty($row['classe'])) {
                    $class = SchoolClass::where('school_year_id', $this->schoolYear->id)
                        ->where('name', 'ilike', trim($row['classe']))
                        ->first();
                    
                    if (!$class) {
                        $this->errors[] = "Ligne {$rowNum}: Classe '{$row['classe']}' non trouvée.";
                        $this->skipped++;
                        continue;
                    }
                }

                if (!empty($row['nni'])) {
                    $nni = trim($row['nni']);
                    if (!preg_match('/^\d{10}$/', $nni)) {
                        $this->errors[] = "Ligne {$rowNum}: NNI '{$nni}' invalide (10 chiffres requis).";
                        $this->skipped++;
                        continue;
                    }
                    
                    if (Student::where('nni', $nni)->exists()) {
                        $this->errors[] = "Ligne {$rowNum}: NNI '{$nni}' déjà utilisé.";
                        $this->skipped++;
                        continue;
                    }
                }

                $birthDate = $this->parseDate($row['date_naissance'] ?? null);
                $enrollmentDate = $this->parseDate($row['date_inscription'] ?? null) ?? now();

                if (!$birthDate) {
                    $this->errors[] = "Ligne {$rowNum}: Date de naissance invalide.";
                    $this->skipped++;
                    continue;
                }

                Student::create([
                    'school_year_id' => $this->schoolYear->id,
                    'matricule' => Student::generateMatricule($this->schoolYear->id),
                    'first_name' => trim($row['prenom']),
                    'last_name' => trim($row['nom']),
                    'first_name_ar' => $row['prenom_ar'] ?? null,
                    'last_name_ar' => $row['nom_ar'] ?? null,
                    'birth_date' => $birthDate,
                    'birth_place' => $row['lieu_naissance'] ?? '',
                    'birth_place_ar' => $row['lieu_naissance_ar'] ?? null,
                    'gender' => $this->parseGender($row['genre'] ?? 'M'),
                    'nationality' => $row['nationalite'] ?? 'Mauritanienne',
                    'nni' => !empty($row['nni']) ? trim($row['nni']) : null,
                    'guardian_name' => $row['tuteur'] ?? '',
                    'guardian_name_ar' => $row['tuteur_ar'] ?? null,
                    'guardian_phone' => $row['telephone'] ?? '',
                    'guardian_phone_2' => $row['telephone_2'] ?? null,
                    'guardian_email' => $row['email'] ?? null,
                    'guardian_profession' => $row['profession'] ?? null,
                    'address' => $row['adresse'] ?? null,
                    'address_ar' => $row['adresse_ar'] ?? null,
                    'class_id' => $class?->id,
                    'enrollment_date' => $enrollmentDate,
                    'previous_school' => $row['ecole_precedente'] ?? null,
                    'status' => 'active',
                ]);

                $this->imported++;
            } catch (\Exception $e) {
                // Log detailed error for debugging (not exposed to user)
                Log::error("Import student error at row {$rowNum}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'row_data' => $row->toArray(),
                ]);

                // Show generic error message to user (no sensitive info leakage)
                $this->errors[] = "Ligne {$rowNum}: Erreur lors de l'import de cet élève. Vérifiez les données.";
                $this->skipped++;
            }
        }
    }

    protected function parseDate($value): ?Carbon
    {
        if (empty($value)) return null;

        try {
            if (is_numeric($value)) {
                return Carbon::createFromFormat('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d'));
            }
            
            $formats = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'd.m.Y'];
            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat($format, $value);
                } catch (\Exception $e) {
                    continue;
                }
            }
            
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function parseGender(?string $value): string
    {
        if (empty($value)) return 'male';
        $value = strtolower(trim($value));
        return in_array($value, ['f', 'feminin', 'féminin', 'female', 'fille', 'أنثى']) ? 'female' : 'male';
    }

    public function getErrors(): array { return $this->errors; }
    public function getImported(): int { return $this->imported; }
    public function getSkipped(): int { return $this->skipped; }
}
