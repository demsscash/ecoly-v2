<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            direction: ltr;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #1a365d;
            padding-bottom: 10px;
        }
        .school-name {
            font-size: 16pt;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 5px;
        }
        .school-name-ar {
            font-size: 14pt;
            font-weight: bold;
            color: #1a365d;
            direction: rtl;
            margin-bottom: 5px;
        }
        .school-info {
            font-size: 8pt;
            color: #666;
        }
        .title {
            background-color: #1a365d;
            color: white;
            padding: 8px;
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin: 15px 0;
        }
        .filters {
            background-color: #f3f4f6;
            padding: 8px;
            margin-bottom: 12px;
            border-radius: 5px;
            font-size: 8pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8pt;
        }
        th {
            background-color: #1a365d;
            color: white;
            padding: 6px 4px;
            text-align: left;
        }
        td {
            padding: 5px 4px;
            border-bottom: 1px solid #e5e7eb;
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
        .text-warning {
            color: #f59e0b;
        }
        .summary {
            margin-top: 15px;
            border: 2px solid #1a365d;
            padding: 12px;
            background-color: #f8fafc;
        }
        .summary-row {
            padding: 4px 0;
            font-size: 10pt;
        }
        .summary-total {
            font-size: 12pt;
            font-weight: bold;
            border-top: 2px solid #1a365d;
            padding-top: 8px;
            margin-top: 8px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 7pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
        }
        .badge-success {
            background-color: #dcfce7;
            color: #16a34a;
        }
        .badge-warning {
            background-color: #fef3c7;
            color: #f59e0b;
        }
        .badge-error {
            background-color: #fee2e2;
            color: #dc2626;
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
        RAPPORT FINANCIER PAR CLASSE / التقرير المالي حسب الصف
    </div>

    {{-- Filters --}}
    <div class="filters">
        <strong>Année scolaire:</strong> {{ $year->name ?? '-' }}
        • <strong>Date:</strong> {{ now()->format('d/m/Y H:i') }}
    </div>

    {{-- Table --}}
    <table>
        <thead>
            <tr>
                <th>Classe</th>
                <th class="text-center">Élèves</th>
                <th class="text-right">Total Dû</th>
                <th class="text-right">Collecté</th>
                <th class="text-right">Solde</th>
                <th class="text-center">Payés</th>
                <th class="text-center">Partiels</th>
                <th class="text-center">En attente</th>
                <th class="text-center">Taux</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report as $item)
                <tr>
                    <td class="font-bold">{{ $item['class']->name }}</td>
                    <td class="text-center">{{ $item['students_count'] }}</td>
                    <td class="text-right">{{ number_format($item['total_due'], 0) }}</td>
                    <td class="text-right text-success">{{ number_format($item['total_paid'], 0) }}</td>
                    <td class="text-right {{ $item['balance'] > 0 ? 'text-error' : 'text-success' }}">
                        {{ number_format($item['balance'], 0) }}
                    </td>
                    <td class="text-center">
                        <span class="badge badge-success">{{ $item['paid_count'] }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-warning">{{ $item['partial_count'] }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-error">{{ $item['pending_count'] }}</span>
                    </td>
                    <td class="text-center {{ $item['collection_rate'] >= 80 ? 'text-success' : ($item['collection_rate'] >= 50 ? 'text-warning' : 'text-error') }}">
                        <strong>{{ number_format($item['collection_rate'], 1) }}%</strong>
                    </td>
                </tr>
            @endforeach
            @php
                $totalStudents = collect($report)->sum('students_count');
                $totalPaidCount = collect($report)->sum('paid_count');
                $totalPartialCount = collect($report)->sum('partial_count');
                $totalPendingCount = collect($report)->sum('pending_count');
                $overallRate = $totalDue > 0 ? ($totalPaid / $totalDue) * 100 : 0;
            @endphp
            <tr style="background-color: #e5e7eb; font-weight: bold;">
                <td>TOTAL</td>
                <td class="text-center">{{ $totalStudents }}</td>
                <td class="text-right">{{ number_format($totalDue, 0) }}</td>
                <td class="text-right text-success">{{ number_format($totalPaid, 0) }}</td>
                <td class="text-right text-error">{{ number_format($totalBalance, 0) }}</td>
                <td class="text-center">{{ $totalPaidCount }}</td>
                <td class="text-center">{{ $totalPartialCount }}</td>
                <td class="text-center">{{ $totalPendingCount }}</td>
                <td class="text-center {{ $overallRate >= 80 ? 'text-success' : ($overallRate >= 50 ? 'text-warning' : 'text-error') }}">
                    {{ number_format($overallRate, 1) }}%
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Summary --}}
    <div class="summary">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 50%; border: none; padding: 5px;">
                    <div class="summary-row">
                        <strong>Nombre total d'élèves:</strong> {{ $totalStudents }}
                    </div>
                    <div class="summary-row">
                        <strong>Total des frais:</strong> {{ number_format($totalDue, 0) }} MRU
                    </div>
                    <div class="summary-row">
                        <strong>Montant collecté:</strong> <span class="text-success">{{ number_format($totalPaid, 0) }} MRU</span>
                    </div>
                </td>
                <td style="width: 50%; border: none; padding: 5px;">
                    <div class="summary-row">
                        <strong>Élèves à jour:</strong> <span class="text-success">{{ $totalPaidCount }}</span>
                    </div>
                    <div class="summary-row">
                        <strong>Paiements partiels:</strong> <span class="text-warning">{{ $totalPartialCount }}</span>
                    </div>
                    <div class="summary-row">
                        <strong>En attente:</strong> <span class="text-error">{{ $totalPendingCount }}</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border: none; padding-top: 10px;">
                    <div class="summary-total" style="text-align: center;">
                        <span>Solde total restant / المبلغ المتبقي:</span>
                        <span class="text-error" style="margin-left: 10px;">{{ number_format($totalBalance, 0) }} MRU</span>
                        <span style="margin-left: 20px;">Taux de recouvrement:</span>
                        <span class="{{ $overallRate >= 80 ? 'text-success' : ($overallRate >= 50 ? 'text-warning' : 'text-error') }}" style="margin-left: 10px;">
                            {{ number_format($overallRate, 1) }}%
                        </span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Footer --}}
    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }} • {{ $school->name_fr ?? 'École' }}
    </div>
</body>
</html>
