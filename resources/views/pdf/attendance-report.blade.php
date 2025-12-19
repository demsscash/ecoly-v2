<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #1a365d;
            padding-bottom: 15px;
        }
        .school-name {
            font-size: 18pt;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 14pt;
            font-weight: bold;
            margin-top: 10px;
            color: #2d3748;
        }
        .period {
            font-size: 9pt;
            color: #666;
            margin-top: 5px;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stat-row {
            display: table-row;
        }
        .stat-cell {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #e2e8f0;
            background-color: #f7fafc;
        }
        .stat-label {
            font-size: 8pt;
            color: #718096;
            text-transform: uppercase;
        }
        .stat-value {
            font-size: 16pt;
            font-weight: bold;
            color: #2d3748;
            margin-top: 5px;
        }
        .stat-desc {
            font-size: 8pt;
            color: #a0aec0;
            margin-top: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #1a365d;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
        }
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9pt;
        }
        tr:nth-child(even) {
            background-color: #f7fafc;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
        }
        .badge-success { background-color: #c6f6d5; color: #22543d; }
        .badge-error { background-color: #fed7d7; color: #742a2a; }
        .badge-warning { background-color: #feebc8; color: #744210; }
        .badge-info { background-color: #bee3f8; color: #2c5282; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8pt;
            color: #a0aec0;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <div class="school-name">{{ $school['name'] }}</div>
        <div class="report-title">Rapport d'Assiduité</div>
        <div class="period">
            Période : {{ $period['start'] }} au {{ $period['end'] }}
            @if($period['class'])
                - Classe : {{ $period['class'] }}
            @endif
        </div>
    </div>

    {{-- Statistics --}}
    <div class="stats-grid">
        <div class="stat-row">
            <div class="stat-cell">
                <div class="stat-label">Période</div>
                <div class="stat-value">{{ $stats['total_days'] }}</div>
                <div class="stat-desc">jours</div>
            </div>
            <div class="stat-cell">
                <div class="stat-label">Taux de présence</div>
                <div class="stat-value" style="color: #22543d;">{{ $stats['attendance_rate'] }}%</div>
                <div class="stat-desc">{{ $stats['present_count'] }} présents</div>
            </div>
            <div class="stat-cell">
                <div class="stat-label">Absences</div>
                <div class="stat-value" style="color: #742a2a;">{{ $stats['absent_count'] }}</div>
                <div class="stat-desc">{{ $stats['justified_count'] }} justifiées</div>
            </div>
            <div class="stat-cell">
                <div class="stat-label">Retards</div>
                <div class="stat-value" style="color: #744210;">{{ $stats['late_count'] + $stats['left_early_count'] }}</div>
                <div class="stat-desc">total</div>
            </div>
        </div>
    </div>

    {{-- By Class if applicable --}}
    @if(!empty($stats['by_class']))
        <h3 style="margin-top: 25px; margin-bottom: 10px; color: #2d3748;">Assiduité par Classe</h3>
        <table>
            <thead>
                <tr>
                    <th>Classe</th>
                    <th style="text-align: right;">Total</th>
                    <th style="text-align: right;">Présents</th>
                    <th style="text-align: right;">Taux</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stats['by_class'] as $classData)
                    <tr>
                        <td><strong>{{ $classData['class']->name }}</strong></td>
                        <td style="text-align: right;">{{ $classData['total'] }}</td>
                        <td style="text-align: right;">{{ $classData['present'] }}</td>
                        <td style="text-align: right;">
                            <span class="badge {{ $classData['rate'] >= 90 ? 'badge-success' : ($classData['rate'] >= 75 ? 'badge-warning' : 'badge-error') }}">
                                {{ number_format($classData['rate'], 1) }}%
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Detailed Records --}}
    @if(!empty($attendances))
        <h3 style="margin-top: 25px; margin-bottom: 10px; color: #2d3748;">Détail des Absences et Retards</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Élève</th>
                    <th>Classe</th>
                    <th>Statut</th>
                    <th>Justification</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->date->format('d/m/Y') }}</td>
                        <td>{{ $attendance->student->full_name }}</td>
                        <td>{{ $attendance->student->class?->name ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $attendance->status === 'absent' ? 'badge-error' : ($attendance->status === 'justified' ? 'badge-info' : 'badge-warning') }}">
                                {{ $attendance->getStatusLabel() }}
                            </span>
                        </td>
                        <td>{{ Str::limit($attendance->justification_note ?? '-', 50) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Footer --}}
    <div class="footer">
        Généré le {{ now()->format('d/m/Y à H:i') }} - {{ $school['name'] }}
    </div>
</body>
</html>
