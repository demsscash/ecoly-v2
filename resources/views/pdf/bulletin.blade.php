<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bulletin - {{ $student['full_name'] }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            width: 100%;
            border-bottom: 2px solid #1a365d;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .header-table {
            width: 100%;
        }
        
        .header-left {
            width: 30%;
            text-align: left;
            vertical-align: top;
        }
        
        .header-center {
            width: 40%;
            text-align: center;
            vertical-align: middle;
        }
        
        .header-right {
            width: 30%;
            text-align: right;
            vertical-align: top;
            direction: rtl;
        }
        
        .logo {
            width: 60px;
            height: 60px;
        }
        
        .school-name {
            font-size: 12px;
            font-weight: bold;
            color: #1a365d;
        }
        
        .school-name-ar {
            font-size: 12px;
            font-weight: bold;
            color: #1a365d;
            direction: rtl;
        }
        
        .bulletin-title {
            font-size: 18px;
            font-weight: bold;
            color: #1a365d;
            margin: 5px 0;
        }
        
        .bulletin-title-ar {
            font-size: 16px;
            font-weight: bold;
            color: #1a365d;
            direction: rtl;
        }
        
        .country-info {
            font-size: 9px;
            color: #666;
        }
        
        .student-section {
            width: 100%;
            margin-bottom: 15px;
            border: 1px solid #cbd5e0;
            background: #f8fafc;
        }
        
        .student-table {
            width: 100%;
        }
        
        .student-photo-cell {
            width: 80px;
            padding: 10px;
            vertical-align: top;
        }
        
        .student-photo {
            width: 70px;
            height: 90px;
            border: 1px solid #cbd5e0;
            object-fit: cover;
        }
        
        .student-info-cell {
            padding: 10px;
            vertical-align: top;
        }
        
        .info-row {
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            color: #4a5568;
            display: inline-block;
            width: 120px;
        }
        
        .info-label-ar {
            font-weight: bold;
            color: #4a5568;
            direction: rtl;
            display: inline-block;
            width: 80px;
            text-align: right;
        }
        
        .info-value {
            font-weight: 500;
        }
        
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .grades-table th {
            background: #1a365d;
            color: white;
            padding: 8px 5px;
            text-align: center;
            font-size: 9px;
            border: 1px solid #1a365d;
        }
        
        .grades-table th.subject-header {
            text-align: left;
            padding-left: 8px;
        }
        
        .grades-table td {
            padding: 6px 5px;
            border: 1px solid #cbd5e0;
            text-align: center;
            font-size: 9px;
        }
        
        .grades-table td.subject-cell {
            text-align: left;
            padding-left: 8px;
        }
        
        .grades-table td.appreciation-cell {
            text-align: left;
            font-size: 8px;
            padding-left: 5px;
        }
        
        .grades-table tr:nth-child(even) {
            background: #f7fafc;
        }
        
        .text-success { color: #22863a; font-weight: bold; }
        .text-error { color: #cb2431; font-weight: bold; }
        .font-bold { font-weight: bold; }
        
        .summary-section {
            width: 100%;
            margin-bottom: 15px;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .summary-table td {
            border: 1px solid #cbd5e0;
            padding: 10px;
            text-align: center;
        }
        
        .summary-label {
            font-size: 9px;
            color: #4a5568;
            margin-bottom: 3px;
        }
        
        .summary-label-ar {
            font-size: 9px;
            color: #4a5568;
            direction: rtl;
        }
        
        .summary-value {
            font-size: 16px;
            font-weight: bold;
        }
        
        .signatures-section {
            width: 100%;
            margin-top: 20px;
        }
        
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .signature-box {
            border: 1px solid #cbd5e0;
            padding: 10px;
            text-align: center;
            vertical-align: top;
            height: 80px;
            width: 33%;
        }
        
        .signature-title {
            font-weight: bold;
            font-size: 9px;
            margin-bottom: 5px;
        }
        
        .signature-title-ar {
            font-size: 8px;
            direction: rtl;
            color: #666;
        }
        
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 8px;
            color: #718096;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="header-left">
                    @if($school['logo'])
                        <img src="{{ public_path('storage/' . $school['logo']) }}" class="logo" alt="Logo">
                    @endif
                    <div class="school-name">{{ $school['name'] }}</div>
                    <div style="font-size: 9px;">{{ $school['address'] ?? '' }}</div>
                    <div style="font-size: 9px;">Tél: {{ $school['phone'] ?? '' }}</div>
                </td>
                <td class="header-center">
                    <div class="bulletin-title">BULLETIN DE NOTES</div>
                    <div class="bulletin-title-ar">كشف النقاط</div>
                    <div style="font-size: 11px; font-weight: bold; margin-top: 5px;">{{ $trimester['name'] }}</div>
                    <div style="font-size: 10px; direction: rtl;">{{ $trimester['name_ar'] ?? '' }}</div>
                    <div style="font-size: 10px; margin-top: 5px;">{{ $student['school_year'] }}</div>
                </td>
                <td class="header-right">
                    <div class="school-name-ar">{{ $school['name_ar'] ?? 'المدرسة' }}</div>
                    <div class="country-info" style="margin-top: 10px;">
                        الجمهورية الإسلامية الموريتانية
                    </div>
                    <div class="country-info">
                        شرف - إخاء - عدالة
                    </div>
                    <div class="country-info" style="margin-top: 5px; direction: ltr; text-align: right;">
                        République Islamique de Mauritanie
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Student Info --}}
    <div class="student-section">
        <table class="student-table">
            <tr>
                <td class="student-photo-cell">
                    @if($student['photo'])
                        <img src="{{ public_path('storage/' . $student['photo']) }}" class="student-photo" alt="Photo">
                    @else
                        <div class="student-photo" style="background: #e2e8f0; display: flex; align-items: center; justify-content: center;">
                            <span style="color: #a0aec0;">Photo</span>
                        </div>
                    @endif
                </td>
                <td class="student-info-cell">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 50%;">
                                <div class="info-row">
                                    <span class="info-label">Nom et Prénom:</span>
                                    <span class="info-value">{{ $student['full_name'] }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Date de naissance:</span>
                                    <span class="info-value">{{ $student['birth_date'] }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Lieu de naissance:</span>
                                    <span class="info-value">{{ $student['birth_place'] ?? '-' }}</span>
                                </div>
                            </td>
                            <td style="width: 50%; text-align: right; direction: rtl;">
                                <div class="info-row">
                                    <span class="info-label-ar">الاسم الكامل:</span>
                                    <span class="info-value">{{ $student['full_name_ar'] ?? $student['full_name'] }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label-ar">رقم التسجيل:</span>
                                    <span class="info-value" style="direction: ltr;">{{ $student['matricule'] }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label-ar">القسم:</span>
                                    <span class="info-value">{{ $student['class'] }}</span>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- Grades Table --}}
    <table class="grades-table">
        <thead>
            <tr>
                <th class="subject-header" style="width: 25%;">Matière / المادة</th>
                <th style="width: 10%;">Barème<br/>السلم</th>
                <th style="width: 12%;">Contrôle<br/>المراقبة</th>
                <th style="width: 12%;">Examen<br/>الامتحان</th>
                <th style="width: 12%;">Moyenne<br/>المعدل</th>
                <th style="width: 29%;">Appréciation / التقدير</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subjects as $subject)
                <tr>
                    <td class="subject-cell">
                        {{ $subject['name'] }}
                        @if($subject['name_ar'])
                            <br/><span style="direction: rtl; font-size: 8px; color: #666;">{{ $subject['name_ar'] }}</span>
                        @endif
                    </td>
                    <td>/{{ $subject['grade_base'] }}</td>
                    <td>{{ $subject['control'] !== null ? number_format($subject['control'], 2) : '-' }}</td>
                    <td>{{ $subject['exam'] !== null ? number_format($subject['exam'], 2) : '-' }}</td>
                    <td class="{{ $subject['average'] !== null ? ($subject['average'] >= ($subject['grade_base'] / 2) ? 'text-success' : 'text-error') : '' }}">
                        {{ $subject['average'] !== null ? number_format($subject['average'], 2) : '-' }}
                    </td>
                    <td class="appreciation-cell">{{ $subject['appreciation'] ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Summary --}}
    <div class="summary-section">
        <table class="summary-table">
            <tr>
                <td style="width: 33%; background: #edf2f7;">
                    <div class="summary-label">Moyenne Générale / المعدل العام</div>
                    <div class="summary-value {{ $summary['average'] !== null ? ($summary['average'] >= 10 ? 'text-success' : 'text-error') : '' }}">
                        {{ $summary['average'] !== null ? number_format($summary['average'], 2) . '/20' : '-' }}
                    </div>
                </td>
                <td style="width: 33%; background: #edf2f7;">
                    <div class="summary-label">Rang / الترتيب</div>
                    <div class="summary-value">
                        {{ $summary['rank'] ? $summary['rank'] . ' / ' . $summary['total_students'] : '-' }}
                    </div>
                </td>
                <td style="width: 34%; background: #edf2f7;">
                    <div class="summary-label">Mention / التقدير</div>
                    <div class="summary-value" style="font-size: 14px;">
                        {{ $summary['mention'] ?? '-' }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Observations --}}
    <div style="border: 1px solid #cbd5e0; padding: 10px; margin-bottom: 15px; min-height: 50px;">
        <div style="font-weight: bold; font-size: 9px; margin-bottom: 5px;">
            Observations du conseil de classe / ملاحظات مجلس القسم:
        </div>
        <div style="min-height: 30px;"></div>
    </div>

    {{-- Signatures --}}
    <div class="signatures-section">
        <table class="signatures-table">
            <tr>
                <td class="signature-box">
                    <div class="signature-title">Signature du Directeur</div>
                    <div class="signature-title-ar">توقيع المدير</div>
                    <div style="margin-top: 40px; font-size: 8px;">
                        Date: {{ $generated_at }}
                    </div>
                </td>
                <td class="signature-box">
                    <div class="signature-title">Cachet de l'établissement</div>
                    <div class="signature-title-ar">ختم المؤسسة</div>
                </td>
                <td class="signature-box">
                    <div class="signature-title">Signature du Parent</div>
                    <div class="signature-title-ar">توقيع ولي الأمر</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Footer --}}
    <div class="footer">
        {{ $school['name'] }} - {{ $school['address'] ?? '' }} - Tél: {{ $school['phone'] ?? '' }}
        <br/>
        Bulletin généré le {{ $generated_at }}
    </div>
</body>
</html>
