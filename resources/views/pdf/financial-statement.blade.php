<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: dejavusans, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            width: 100%;
            margin-bottom: 20px;
        }
        
        .header td {
            vertical-align: middle;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }
        
        .school-info {
            text-align: center;
        }
        
        .school-name {
            font-size: 14pt;
            font-weight: bold;
            color: #1a365d;
        }
        
        .school-name-ar {
            font-size: 12pt;
            font-weight: bold;
            color: #1a365d;
            direction: rtl;
        }
        
        .title-box {
            background: #1a365d;
            color: white;
            text-align: center;
            padding: 12px;
            margin-bottom: 20px;
        }
        
        .title {
            font-size: 16pt;
            font-weight: bold;
        }
        
        .title-ar {
            font-size: 14pt;
            direction: rtl;
            margin-top: 3px;
        }
        
        .section {
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #1a365d;
            border-bottom: 2px solid #1a365d;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        
        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }
        
        .info-table td {
            padding: 5px 10px;
            vertical-align: top;
        }
        
        .info-label {
            font-weight: bold;
            color: #4a5568;
            width: 120px;
        }
        
        .payments-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .payments-table th {
            background: #edf2f7;
            padding: 10px 8px;
            text-align: left;
            font-size: 9pt;
            border: 1px solid #cbd5e0;
            color: #4a5568;
        }
        
        .payments-table th.text-right {
            text-align: right;
        }
        
        .payments-table td {
            padding: 8px;
            border: 1px solid #cbd5e0;
            font-size: 9pt;
        }
        
        .payments-table tr:nth-child(even) {
            background: #f8fafc;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-success {
            color: #22863a;
        }
        
        .text-error {
            color: #cb2431;
        }
        
        .font-bold {
            font-weight: bold;
        }
        
        .summary-box {
            border: 2px solid #1a365d;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .summary-table {
            width: 100%;
        }
        
        .summary-table td {
            padding: 8px 15px;
        }
        
        .summary-label {
            font-weight: bold;
            color: #4a5568;
        }
        
        .summary-value {
            text-align: right;
            font-size: 12pt;
            font-weight: bold;
        }
        
        .total-row {
            background: #edf2f7;
            border-top: 2px solid #1a365d;
        }
        
        .status-paid {
            background: #c6f6d5;
            color: #22543d;
            padding: 3px 10px;
            border-radius: 3px;
            font-weight: bold;
        }
        
        .status-partial {
            background: #fef3c7;
            color: #92400e;
            padding: 3px 10px;
            border-radius: 3px;
            font-weight: bold;
        }
        
        .status-pending {
            background: #fed7d7;
            color: #c53030;
            padding: 3px 10px;
            border-radius: 3px;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 8pt;
            color: #718096;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
        
        .signatures {
            width: 100%;
            margin-top: 40px;
        }
        
        .signatures td {
            width: 50%;
            text-align: center;
            padding: 10px;
            vertical-align: top;
        }
        
        .signature-title {
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 40px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            width: 150px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <table class="header">
        <tr>
            <td style="width: 70px;">
                @if($school['logo'])
                    <img src="{{ public_path('storage/' . $school['logo']) }}" class="logo" alt="Logo">
                @else
                    <div style="width: 60px; height: 60px; background: #1a365d; text-align: center; line-height: 60px; color: white; font-weight: bold; font-size: 24pt;">E</div>
                @endif
            </td>
            <td class="school-info">
                <div class="school-name">{{ $school['name'] }}</div>
                <div class="school-name-ar">{{ $school['name_ar'] }}</div>
                <div style="font-size: 9pt; color: #666; margin-top: 3px;">{{ $school['address'] }} • Tél: {{ $school['phone'] }}</div>
            </td>
            <td style="width: 70px; text-align: right;">
                @if($school['logo'])
                    <img src="{{ public_path('storage/' . $school['logo']) }}" class="logo" alt="Logo">
                @else
                    <div style="width: 60px; height: 60px; background: #1a365d; text-align: center; line-height: 60px; color: white; font-weight: bold; font-size: 24pt;">E</div>
                @endif
            </td>
        </tr>
    </table>

    {{-- Title --}}
    <div class="title-box">
        <div class="title">SITUATION FINANCIÈRE</div>
        <div class="title-ar">الوضعية المالية</div>
    </div>

    {{-- Student Info --}}
    <div class="section">
        <div class="section-title">Informations de l'élève / معلومات التلميذ</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Nom complet:</td>
                <td><strong>{{ $student['full_name'] }}</strong></td>
                <td class="info-label">Matricule:</td>
                <td>{{ $student['matricule'] }}</td>
            </tr>
            <tr>
                <td class="info-label">Classe:</td>
                <td>{{ $student['class'] }}</td>
                <td class="info-label">Année scolaire:</td>
                <td>{{ $student['school_year'] }}</td>
            </tr>
            <tr>
                <td class="info-label">Tuteur:</td>
                <td>{{ $student['guardian_name'] }}</td>
                <td class="info-label">Téléphone:</td>
                <td>{{ $student['guardian_phone'] }}</td>
            </tr>
        </table>
    </div>

    {{-- Payments Detail --}}
    <div class="section">
        <div class="section-title">Détail des paiements / تفاصيل المدفوعات</div>
        <table class="payments-table">
            <thead>
                <tr>
                    <th style="width: 30%;">Désignation</th>
                    <th class="text-right" style="width: 17%;">Montant (MRU)</th>
                    <th class="text-right" style="width: 17%;">Payé (MRU)</th>
                    <th class="text-right" style="width: 17%;">Reste (MRU)</th>
                    <th style="width: 19%;">Statut</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                    <tr>
                        <td>
                            <strong>{{ $payment->getTypeLabel() }}</strong>
                            @if($payment->month)
                                <br><span style="color: #718096;">{{ $payment->getMonthLabel() }}</span>
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($payment->amount, 0) }}</td>
                        <td class="text-right text-success">{{ number_format($payment->amount_paid, 0) }}</td>
                        <td class="text-right {{ $payment->balance > 0 ? 'text-error' : 'text-success' }}">{{ number_format($payment->balance, 0) }}</td>
                        <td>
                            @if($payment->status === 'paid')
                                <span class="status-paid">Payé</span>
                            @elseif($payment->status === 'partial')
                                <span class="status-partial">Partiel</span>
                            @else
                                <span class="status-pending">En attente</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Summary --}}
    <div class="summary-box">
        <table class="summary-table">
            <tr>
                <td class="summary-label">Total dû / المبلغ الإجمالي:</td>
                <td class="summary-value">{{ number_format($summary['total_due'], 0) }} MRU</td>
            </tr>
            <tr>
                <td class="summary-label">Total payé / المبلغ المدفوع:</td>
                <td class="summary-value text-success">{{ number_format($summary['total_paid'], 0) }} MRU</td>
            </tr>
            <tr class="total-row">
                <td class="summary-label" style="font-size: 12pt;">Solde restant / المتبقي:</td>
                <td class="summary-value {{ $summary['balance'] > 0 ? 'text-error' : 'text-success' }}" style="font-size: 14pt;">
                    {{ number_format($summary['balance'], 0) }} MRU
                </td>
            </tr>
        </table>
    </div>

    {{-- Signatures --}}
    <table class="signatures">
        <tr>
            <td>
                <div class="signature-title">CACHET DE L'ÉTABLISSEMENT</div>
                <div class="signature-line"></div>
            </td>
            <td>
                <div class="signature-title">SIGNATURE DU RESPONSABLE</div>
                <div class="signature-line"></div>
            </td>
        </tr>
    </table>

    {{-- Footer --}}
    <div class="footer">
        Document généré le {{ $generated_at }} • {{ $school['name'] }}
    </div>
</body>
</html>
