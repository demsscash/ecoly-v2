<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Liste des élèves</title>
    <style>
        @page { margin: 10mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 8pt; line-height: 1.3; }
        .header { text-align: center; border-bottom: 2px solid #1a365d; padding-bottom: 8px; margin-bottom: 10px; }
        .header-top td { vertical-align: middle; font-size: 7pt; }
        .logo { width: 50px; height: auto; }
        .school-name { font-size: 12pt; font-weight: bold; color: #1a365d; margin: 5px 0; }
        .title { text-align: center; margin: 10px 0; font-size: 11pt; font-weight: bold; color: #1a365d; }
        .subtitle { text-align: center; font-size: 9pt; margin-bottom: 10px; color: #555; }
        table.students { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.students th, table.students td { border: 1px solid #ddd; padding: 4px 6px; text-align: left; }
        table.students th { background-color: #1a365d; color: white; font-weight: bold; font-size: 7pt; }
        table.students td { font-size: 7pt; }
        table.students tr:nth-child(even) { background-color: #f9f9f9; }
        .footer { margin-top: 15px; font-size: 7pt; color: #666; text-align: right; }
        .total { margin-top: 10px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-top" width="100%">
            <tr>
                <td width="30%" style="text-align: left;">République Islamique de Mauritanie<br><small>Honneur - Fraternité - Justice</small></td>
                <td width="40%" style="text-align: center;">
                    @if($school->logo_path && file_exists(storage_path('app/public/' . $school->logo_path)))
                        <img src="{{ storage_path('app/public/' . $school->logo_path) }}" class="logo">
                    @endif
                </td>
                <td width="30%" style="text-align: right;" dir="rtl">الجمهورية الإسلامية الموريتانية<br><small>شرف - إخاء - عدالة</small></td>
            </tr>
        </table>
        <div class="school-name">{{ $school->name_fr ?? 'École' }}</div>
    </div>

    <div class="title">LISTE DES ÉLÈVES</div>
    <div class="subtitle">Année scolaire : {{ $schoolYear->name }} @if($class) | Classe : {{ $class->name }} @endif</div>

    <table class="students">
        <thead>
            <tr>
                <th>#</th><th>Matricule</th><th>NNI</th><th>Nom</th><th>Prénom</th>
                <th>Date naiss.</th><th>Genre</th><th>Classe</th><th>Tuteur</th><th>Téléphone</th><th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student->matricule }}</td>
                    <td>{{ $student->nni ?? '-' }}</td>
                    <td>{{ $student->last_name }}</td>
                    <td>{{ $student->first_name }}</td>
                    <td>{{ $student->birth_date->format('d/m/Y') }}</td>
                    <td>{{ $student->gender === 'male' ? 'M' : 'F' }}</td>
                    <td>{{ $student->class?->name ?? '-' }}</td>
                    <td>{{ $student->guardian_name }}</td>
                    <td>{{ $student->guardian_phone }}</td>
                    <td>{{ $student->status === 'active' ? 'Actif' : $student->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">Total : {{ $students->count() }} élève(s)</div>
    <div class="footer">Généré le {{ $date->format('d/m/Y à H:i') }}</div>
</body>
</html>
