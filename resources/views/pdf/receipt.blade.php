<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: dejavusans, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }
        
        .receipt-border {
            border: 3px solid #1a365d;
            padding: 15px;
            margin: 5px;
        }
        
        .header {
            width: 100%;
            margin-bottom: 15px;
        }
        
        .header td {
            vertical-align: middle;
        }
        
        .logo-cell {
            width: 60px;
        }
        
        .logo {
            width: 55px;
            height: 55px;
            object-fit: contain;
        }
        
        .school-info {
            text-align: center;
            padding: 0 10px;
        }
        
        .school-name {
            font-size: 13pt;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 2px;
        }
        
        .school-name-ar {
            font-size: 12pt;
            font-weight: bold;
            color: #1a365d;
            direction: rtl;
        }
        
        .school-contact {
            font-size: 8pt;
            color: #666;
            margin-top: 3px;
        }
        
        .receipt-title-box {
            background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
            background: #1a365d;
            color: white;
            text-align: center;
            padding: 10px;
            margin-bottom: 12px;
        }
        
        .receipt-title {
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 1px;
        }
        
        .receipt-title-ar {
            font-size: 12pt;
            direction: rtl;
            margin-top: 2px;
        }
        
        .receipt-ref {
            background: #f0f4f8;
            display: inline-block;
            padding: 3px 15px;
            font-size: 10pt;
            font-weight: bold;
            color: #1a365d;
            margin-top: 8px;
            border-radius: 3px;
        }
        
        .section {
            margin-bottom: 10px;
        }
        
        .section-title {
            font-size: 9pt;
            font-weight: bold;
            color: #1a365d;
            text-transform: uppercase;
            border-bottom: 1px solid #cbd5e0;
            padding-bottom: 3px;
            margin-bottom: 8px;
        }
        
        .info-grid {
            width: 100%;
        }
        
        .info-grid td {
            padding: 4px 8px;
            vertical-align: top;
        }
        
        .info-label {
            font-size: 8pt;
            color: #718096;
            text-transform: uppercase;
        }
        
        .info-value {
            font-size: 10pt;
            font-weight: 600;
            color: #1a1a1a;
        }
        
        .amount-box {
            background: #f0fff4;
            border: 2px solid #22863a;
            text-align: center;
            padding: 12px;
            margin: 12px 0;
        }
        
        .amount-label {
            font-size: 9pt;
            color: #276749;
            margin-bottom: 5px;
        }
        
        .amount-value {
            font-size: 22pt;
            font-weight: bold;
            color: #22863a;
        }
        
        .amount-currency {
            font-size: 12pt;
            color: #276749;
        }
        
        .payment-detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        .payment-detail-table th {
            background: #edf2f7;
            padding: 8px;
            text-align: left;
            font-size: 9pt;
            color: #4a5568;
            border: 1px solid #cbd5e0;
        }
        
        .payment-detail-table td {
            padding: 8px;
            border: 1px solid #cbd5e0;
            font-size: 10pt;
        }
        
        .signatures {
            width: 100%;
            margin-top: 15px;
        }
        
        .signatures td {
            width: 50%;
            text-align: center;
            padding: 8px;
            vertical-align: top;
        }
        
        .signature-box {
            border: 1px dashed #cbd5e0;
            padding: 10px;
            min-height: 50px;
        }
        
        .signature-title {
            font-size: 8pt;
            font-weight: bold;
            color: #4a5568;
            margin-bottom: 35px;
        }
        
        .footer {
            margin-top: 12px;
            text-align: center;
            font-size: 7pt;
            color: #a0aec0;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
        
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 60pt;
            color: rgba(34, 134, 58, 0.08);
            font-weight: bold;
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="receipt-border">
        {{-- Header with Logo --}}
        <table class="header">
            <tr>
                <td class="logo-cell">
                    @if($school['logo'] ?? null)
                        <img src="{{ public_path('storage/' . $school['logo']) }}" class="logo" alt="Logo">
                    @else
                        <div style="width: 55px; height: 55px; background: #1a365d; border-radius: 5px; text-align: center; line-height: 55px; color: white; font-weight: bold; font-size: 20pt;">E</div>
                    @endif
                </td>
                <td class="school-info">
                    <div class="school-name">{{ $school['name'] }}</div>
                    <div class="school-name-ar">{{ $school['name_ar'] }}</div>
                    <div class="school-contact">{{ $school['address'] }} • Tél: {{ $school['phone'] }}</div>
                </td>
                <td class="logo-cell" style="text-align: right;">
                    @if($school['logo'] ?? null)
                        <img src="{{ public_path('storage/' . $school['logo']) }}" class="logo" alt="Logo">
                    @else
                        <div style="width: 55px; height: 55px; background: #1a365d; border-radius: 5px; text-align: center; line-height: 55px; color: white; font-weight: bold; font-size: 20pt;">E</div>
                    @endif
                </td>
            </tr>
        </table>

        {{-- Receipt Title --}}
        <div class="receipt-title-box">
            <div class="receipt-title">REÇU DE PAIEMENT</div>
            <div class="receipt-title-ar">وصل الدفع</div>
            <div class="receipt-ref">N° {{ $payment['reference'] }}</div>
        </div>

        {{-- Student Information --}}
        <div class="section">
            <div class="section-title">Informations de l'élève</div>
            <table class="info-grid">
                <tr>
                    <td style="width: 50%;">
                        <div class="info-label">Nom complet</div>
                        <div class="info-value">{{ $student['full_name'] }}</div>
                    </td>
                    <td style="width: 25%;">
                        <div class="info-label">Matricule</div>
                        <div class="info-value">{{ $student['matricule'] }}</div>
                    </td>
                    <td style="width: 25%;">
                        <div class="info-label">Classe</div>
                        <div class="info-value">{{ $student['class'] }}</div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Payment Details --}}
        <div class="section">
            <div class="section-title">Détail du paiement</div>
            <table class="payment-detail-table">
                <tr>
                    <th style="width: 60%;">Désignation</th>
                    <th style="width: 20%; text-align: center;">Année</th>
                    <th style="width: 20%; text-align: right;">Montant</th>
                </tr>
                <tr>
                    <td>
                        <strong>{{ $payment['type_label'] }}</strong>
                        @if($payment['month'])
                            <br><span style="color: #718096; font-size: 9pt;">{{ $payment['month_label'] }}</span>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $student['school_year'] }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ number_format($payment['amount_paid'], 0) }} MRU</td>
                </tr>
            </table>
        </div>

        {{-- Amount Box --}}
        <div class="amount-box">
            <div class="amount-label">MONTANT TOTAL PAYÉ / المبلغ الإجمالي المدفوع</div>
            <div class="amount-value">{{ number_format($payment['amount_paid'], 0) }} <span class="amount-currency">MRU</span></div>
        </div>

        {{-- Payment Method --}}
        <div class="section">
            <table class="info-grid">
                <tr>
                    <td style="width: 33%;">
                        <div class="info-label">Mode de paiement</div>
                        <div class="info-value">{{ $payment['method_label'] }}</div>
                    </td>
                    <td style="width: 33%;">
                        <div class="info-label">Date de paiement</div>
                        <div class="info-value">{{ $payment['paid_date'] }}</div>
                    </td>
                    <td style="width: 34%;">
                        <div class="info-label">Reçu par</div>
                        <div class="info-value">{{ $payment['received_by'] }}</div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Signatures --}}
        <table class="signatures">
            <tr>
                <td>
                    <div class="signature-box">
                        <div class="signature-title">CACHET DE L'ÉTABLISSEMENT</div>
                    </div>
                </td>
                <td>
                    <div class="signature-box">
                        <div class="signature-title">SIGNATURE DU CAISSIER</div>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Footer --}}
        <div class="footer">
            Ce reçu est un document officiel • {{ $school['name'] }} • Généré le {{ $generated_at }}
        </div>
    </div>
</body>
</html>
