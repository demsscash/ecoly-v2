<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: dejavusans, sans-serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #333;
        }
        
        .receipt-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #1a365d;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .school-name {
            font-size: 16pt;
            font-weight: bold;
            color: #1a365d;
        }
        
        .school-name-ar {
            font-size: 14pt;
            font-weight: bold;
            color: #1a365d;
            direction: rtl;
        }
        
        .receipt-title {
            font-size: 18pt;
            font-weight: bold;
            margin: 15px 0;
            color: #1a365d;
        }
        
        .receipt-number {
            font-size: 12pt;
            color: #666;
        }
        
        .info-section {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        
        .info-label {
            display: table-cell;
            width: 150px;
            font-weight: bold;
            color: #4a5568;
        }
        
        .info-value {
            display: table-cell;
        }
        
        .amount-section {
            text-align: center;
            padding: 20px;
            background: #edf2f7;
            border: 2px solid #1a365d;
            margin-bottom: 20px;
        }
        
        .amount-label {
            font-size: 12pt;
            color: #4a5568;
            margin-bottom: 5px;
        }
        
        .amount-value {
            font-size: 24pt;
            font-weight: bold;
            color: #22863a;
        }
        
        .amount-words {
            font-size: 10pt;
            font-style: italic;
            color: #666;
            margin-top: 5px;
        }
        
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .details-table th {
            background: #1a365d;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 10pt;
        }
        
        .details-table td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .signatures {
            margin-top: 40px;
            display: table;
            width: 100%;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 10px;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 40px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            width: 150px;
            margin: 0 auto;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9pt;
            color: #718096;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
        
        .arabic {
            direction: rtl;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        {{-- Header --}}
        <div class="header">
            <div class="school-name">{{ $school['name'] }}</div>
            <div class="school-name-ar arabic">{{ $school['name_ar'] ?? '' }}</div>
            <div style="font-size: 10pt; margin-top: 5px;">{{ $school['address'] ?? '' }} | Tél: {{ $school['phone'] ?? '' }}</div>
            
            <div class="receipt-title">REÇU DE PAIEMENT / وصل الدفع</div>
            <div class="receipt-number">N° {{ $payment['reference'] }}</div>
        </div>

        {{-- Student Info --}}
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Élève / التلميذ:</span>
                <span class="info-value"><strong>{{ $student['full_name'] }}</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Matricule / رقم التسجيل:</span>
                <span class="info-value">{{ $student['matricule'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Classe / القسم:</span>
                <span class="info-value">{{ $student['class'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Année scolaire:</span>
                <span class="info-value">{{ $student['school_year'] }}</span>
            </div>
        </div>

        {{-- Payment Details --}}
        <table class="details-table">
            <thead>
                <tr>
                    <th>Désignation</th>
                    <th style="text-align: right;">Montant (MRU)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ $payment['type_label'] }}
                        @if($payment['month'])
                            - {{ $payment['month_label'] }}
                        @endif
                    </td>
                    <td style="text-align: right;">{{ number_format($payment['amount_paid'], 0) }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Amount --}}
        <div class="amount-section">
            <div class="amount-label">Montant payé / المبلغ المدفوع</div>
            <div class="amount-value">{{ number_format($payment['amount_paid'], 0) }} MRU</div>
        </div>

        {{-- Payment Info --}}
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Mode de paiement:</span>
                <span class="info-value">{{ $payment['method_label'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date:</span>
                <span class="info-value">{{ $payment['paid_date'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Reçu par:</span>
                <span class="info-value">{{ $payment['received_by'] }}</span>
            </div>
        </div>

        {{-- Signatures --}}
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-title">Cachet de l'école</div>
                <div class="signature-line"></div>
            </div>
            <div class="signature-box">
                <div class="signature-title">Signature</div>
                <div class="signature-line"></div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            {{ $school['name'] }} - {{ $school['address'] ?? '' }}<br>
            Reçu généré le {{ $generated_at }}
        </div>
    </div>
</body>
</html>
