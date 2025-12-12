<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            direction: ltr;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #1a365d;
            padding-bottom: 10px;
        }
        .school-name {
            font-size: 18pt;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 5px;
        }
        .school-name-ar {
            font-size: 16pt;
            font-weight: bold;
            color: #1a365d;
            direction: rtl;
            margin-bottom: 5px;
        }
        .school-info {
            font-size: 9pt;
            color: #666;
        }
        .title {
            background-color: #1a365d;
            color: white;
            padding: 10px;
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            margin: 20px 0;
        }
        .filters {
            background-color: #f3f4f6;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #1a365d;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 9pt;
        }
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9pt;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: bold;
        }
        .text-error {
            color: #dc2626;
        }
        .text-success {
            color: #16a34a;
        }
        .summary {
            margin-top: 20px;
            border: 2px solid #1a365d;
            padding: 15px;
            background-color: #f8fafc;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 11pt;
        }
        .summary-total {
            font-size: 14pt;
            font-weight: bold;
            border-top: 2px solid #1a365d;
            padding-top: 10px;
            margin-top: 10px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <div class="school-name">{{ $school->name_fr ?? 'École' }}</div>
        @if($school->name_ar)
            <div class="school-name-ar">{{ $school->name_ar }}</div>
        @endif
        <div class="school-info">
            {{ $school->address_fr ?? '' }}
            @if($school->phone) • Tél: {{ $school->phone }} @endif
        </div>
    </div>

    {{-- Title --}}
    <div class="title">
        RAPPORT DES IMPAYÉS / تقرير المتأخرات
    </div>

    {{-- Filters --}}
    <div class="filters">
        <strong>Année scolaire:</strong> {{ $year->name ?? '-' }}
        @if($class)
            • <strong>Classe:</strong> {{ $class->name }}
        @endif
        • <strong>Date:</strong> {{ now()->format('d/m/Y H:i') }}
    </div>

    {{-- Table --}}
    <table>
        <thead>
            <tr>
                <th>Élève</th>
                <th>Classe</th>
                <th class="text-right">Total Dû</th>
                <th class="text-right">Payé</th>
                <th class="text-right">Solde</th>
                <th class="text-center">Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report as $item)
                <tr>
                    <td>
                        <div class="font-bold">{{ $item['student']->full_name }}</div>
                        <div style="font-size: 8pt; color: #666;">{{ $item['student']->matricule }}</div>
                    </td>
                    <td>{{ $item['student']->class?->name ?? '-' }}</td>
                    <td class="text-right">{{ number_format($item['total_due'], 0) }}</td>
                    <td class="text-right text-success">{{ number_format($item['total_paid'], 0) }}</td>
                    <td class="text-right text-error font-bold">{{ number_format($item['balance'], 0) }}</td>
                    <td class="text-center">{{ $item['status'] === 'partial' ? 'Partiel' : 'En attente' }}</td>
                </tr>
            @endforeach
            <tr style="background-color: #e5e7eb; font-weight: bold;">
                <td colspan="2">TOTAL</td>
                <td class="text-right">{{ number_format($totalDue, 0) }}</td>
                <td class="text-right text-success">{{ number_format($totalPaid, 0) }}</td>
                <td class="text-right text-error">{{ number_format($totalBalance, 0) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    {{-- Summary --}}
    <div class="summary">
        <div class="summary-row">
            <span>Nombre d'élèves en retard:</span>
            <span class="font-bold">{{ count($report) }}</span>
        </div>
        <div class="summary-row">
            <span>Total des créances:</span>
            <span class="font-bold">{{ number_format($totalDue, 0) }} MRU</span>
        </div>
        <div class="summary-row">
            <span>Montant collecté:</span>
            <span class="font-bold text-success">{{ number_format($totalPaid, 0) }} MRU</span>
        </div>
        <div class="summary-row summary-total">
            <span>Solde total restant / المبلغ المتبقي:</span>
            <span class="text-error">{{ number_format($totalBalance, 0) }} MRU</span>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }} • {{ $school->name_fr ?? 'École' }}
    </div>
</body>
</html>
