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
        }
        
        .logo {
            width: 50px;
            height: 50px;
        }
        
        .school-name {
            font-size: 11pt;
            font-weight: bold;
            color: #1a365d;
        }
        
        .arabic {
            font-family: dejavusans, sans-serif;
            direction: rtl;
            text-align: right;
        }
        
        .bulletin-title {
            font-size: 16pt;
            font-weight: bold;
            color: #1a365d;
        }
        
        .bulletin-title-ar {
            font-size: 14pt;
            font-weight: bold;
            color: #1a365d;
        }
        
        .student-section {
            width: 100%;
            margin-bottom: 15px;
            border: 1px solid #cbd5e0;
            background-color: #f8fafc;
        }
        
        .student-photo {
            width: 65px;
            height: 85px;
            border: 1px solid #cbd5e0;
        }
        
        .info-label {
            font-weight: bold;
            color: #4a5568;
            width: 110px;
        }
        
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .grades-table th {
            background-color: #1a365d;
            color: white;
            padding: 8px 4px;
            text-align: center;
            font-size: 9pt;
            border: 1px solid #1a365d;
        }
        
        .grades-table td {
            padding: 6px 4px;
            border: 1px solid #cbd5e0;
            text-align: center;
            font-size: 9pt;
        }
        
        .grades-table tr:nth-child(even) {
            background-color: #f7fafc;
        }
        
        .subject-cell {
            text-align: left !important;
            padding-left: 8px !important;
        }
        
        .appreciation-cell {
            text-align: left !important;
            font-size: 8pt;
        }
        
        .text-success { color: #22863a; font-weight: bold; }
        .text-error { color: #cb2431; font-weight: bold; }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .summary-table td {
            border: 1px solid #cbd5e0;
            padding: 10px;
            text-align: center;
            background-color: #edf2f7;
        }
        
        .summary-label {
            font-size: 9pt;
            color: #4a5568;
        }
        
        .summary-value {
            font-size: 14pt;
            font-weight: bold;
        }
        
        .observations-box {
            border: 1px solid #cbd5e0;
            padding: 10px;
            margin-bottom: 15px;
            min-height: 50px;
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
            height: 70px;
            width: 33%;
        }
        
        .signature-title {
            font-weight: bold;
            font-size: 9pt;
        }
        
        .signature-title-ar {
            font-size: 8pt;
            color: #666;
        }
        
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 8pt;
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
                        <img src="{{ public_path('storage/' . $school['logo']) }}" class="logo" alt="Logo"><br>
                    @endif
                    <span class="school-name">{{ $school['name'] }}</span><br>
                    <span style="font-size: 9pt;">{{ $school['address'] ?? '' }}</span><br>
                    <span style="font-size: 9pt;">Tél: {{ $school['phone'] ?? '' }}</span>
                </td>
                <td class="header-center">
                    <span class="bulletin-title">BULLETIN DE NOTES</span><br>
                    <span class="bulletin-title-ar arabic">كشف النقاط</span><br><br>
                    <span style="font-size: 11pt; font-weight: bold;">{{ $trimester['name'] }}</span><br>
                    <span style="font-size: 10pt;" class="arabic">{{ $trimester['name_ar'] ?? '' }}</span><br>
                    <span style="font-size: 10pt;">{{ $student['school_year'] }}</span>
                </td>
                <td class="header-right">
                    <span class="school-name arabic">{{ $school['name_ar'] ?? 'المدرسة' }}</span><br><br>
                    <span class="arabic" style="font-size: 9pt;">الجمهورية الإسلامية الموريتانية</span><br>
                    <span class="arabic" style="font-size: 9pt;">شرف - إخاء - عدالة</span><br><br>
                    <span style="font-size: 8pt;">République Islamique de Mauritanie</span>
                </td>
            </tr>
        </table>
    </div>

    {{-- Student Info --}}
    <div class="student-section">
        <table style="width: 100%;">
            <tr>
                <td style="width: 80px; padding: 10px; vertical-align: top;">
                    @if($student['photo'])
                        <img src="{{ public_path('storage/' . $student['photo']) }}" class="student-photo" alt="Photo">
                    @else
                        <div class="student-photo" style="background-color: #e2e8f0; text-align: center; line-height: 85px; color: #a0aec0;">Photo</div>
                    @endif
                </td>
                <td style="padding: 10px; vertical-align: top;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 50%; vertical-align: top;">
                                <table>
                                    <tr>
                                        <td class="info-label">Nom et Prénom:</td>
                                        <td><strong>{{ $student['full_name'] }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Date de naissance:</td>
                                        <td>{{ $student['birth_date'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Lieu de naissance:</td>
                                        <td>{{ $student['birth_place'] ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Matricule:</td>
                                        <td><strong>{{ $student['matricule'] }}</strong></td>
                                    </tr>
                                </table>
                            </td>
                            <td style="width: 50%; vertical-align: top;">
                                <table style="width: 100%;">
                                    <tr>
                                        <td class="arabic"><strong>{{ $student['full_name_ar'] ?? '' }}</strong></td>
                                        <td class="arabic" style="width: 80px; font-weight: bold; color: #4a5568;">:الاسم الكامل</td>
                                    </tr>
                                    <tr>
                                        <td class="arabic"><strong>{{ $student['class'] }}</strong></td>
                                        <td class="arabic" style="font-weight: bold; color: #4a5568;">:القسم</td>
                                    </tr>
                                    <tr>
                                        <td class="arabic">{{ $student['school_year'] }}</td>
                                        <td class="arabic" style="font-weight: bold; color: #4a5568;">:السنة الدراسية</td>
                                    </tr>
                                </table>
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
                <th style="width: 28%; text-align: left; padding-left: 8px;">Matière / المادة</th>
                <th style="width: 8%;">Barème</th>
                <th style="width: 11%;">Contrôle</th>
                <th style="width: 11%;">Examen</th>
                <th style="width: 11%;">Moyenne</th>
                <th style="width: 31%;">Appréciation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subjects as $subject)
                <tr>
                    <td class="subject-cell">
                        {{ $subject['name'] }}
                        @if($subject['name_ar'])
                            <br><span class="arabic" style="font-size: 8pt; color: #666;">{{ $subject['name_ar'] }}</span>
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
    <table class="summary-table">
        <tr>
            <td style="width: 33%;">
                <div class="summary-label">Moyenne Générale / المعدل العام</div>
                <div class="summary-value {{ $summary['average'] !== null ? ($summary['average'] >= 10 ? 'text-success' : 'text-error') : '' }}">
                    {{ $summary['average'] !== null ? number_format($summary['average'], 2) . '/20' : '-' }}
                </div>
            </td>
            <td style="width: 33%;">
                <div class="summary-label">Rang / الترتيب</div>
                <div class="summary-value">
                    {{ $summary['rank'] ? $summary['rank'] . ' / ' . $summary['total_students'] : '-' }}
                </div>
            </td>
            <td style="width: 34%;">
                <div class="summary-label">Mention / التقدير</div>
                <div class="summary-value" style="font-size: 12pt;">
                    {{ $summary['mention'] ?? '-' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Observations --}}
    <div class="observations-box">
        <strong style="font-size: 9pt;">Observations du conseil de classe / ملاحظات مجلس القسم:</strong>
        <br><br><br>
    </div>

    {{-- Signatures --}}
    <table class="signatures-table">
        <tr>
            <td class="signature-box">
                <span class="signature-title">Signature du Directeur</span><br>
                <span class="signature-title-ar arabic">توقيع المدير</span>
                <br><br><br>
                <span style="font-size: 8pt;">Date: {{ $generated_at }}</span>
            </td>
            <td class="signature-box">
                <span class="signature-title">Cachet de l'établissement</span><br>
                <span class="signature-title-ar arabic">ختم المؤسسة</span>
            </td>
            <td class="signature-box">
                <span class="signature-title">Signature du Parent</span><br>
                <span class="signature-title-ar arabic">توقيع ولي الأمر</span>
            </td>
        </tr>
    </table>

    {{-- Footer --}}
    <div class="footer">
        {{ $school['name'] }} - {{ $school['address'] ?? '' }} - Tél: {{ $school['phone'] ?? '' }}<br>
        Bulletin généré le {{ $generated_at }}
    </div>
</body>
</html>
