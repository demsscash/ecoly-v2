<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Attestation d'inscription</title>
    <style>
        @page {
            margin: 15mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9pt;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #1a365d;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        
        .header-top {
            width: 100%;
            margin-bottom: 8px;
        }
        
        .header-top td {
            vertical-align: middle;
            font-size: 8pt;
        }
        
        .logo {
            width: 60px;
            height: auto;
        }
        
        .school-names {
            margin: 8px 0;
        }
        
        .school-names td {
            width: 50%;
            font-size: 12pt;
            font-weight: bold;
            color: #1a365d;
        }
        
        .school-info {
            font-size: 8pt;
            color: #555;
        }
        
        .title {
            text-align: center;
            margin: 15px 0;
            padding: 8px;
            background-color: #1a365d;
            color: white;
        }
        
        .title td {
            font-size: 14pt;
            font-weight: bold;
            width: 50%;
        }
        
        .bilingual {
            width: 100%;
            border-collapse: collapse;
        }
        
        .bilingual td {
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }
        
        .bilingual td:first-child {
            border-right: 1px solid #ddd;
            text-align: left;
        }
        
        .bilingual td:last-child {
            text-align: right;
            direction: rtl;
        }
        
        .content-text {
            margin: 10px 0;
            line-height: 1.6;
        }
        
        .student-info {
            background-color: #f5f5f5;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        
        .student-info table {
            width: 100%;
            font-size: 9pt;
        }
        
        .student-info td {
            padding: 3px 5px;
            border-bottom: 1px solid #eee;
        }
        
        .student-info tr:last-child td {
            border-bottom: none;
        }
        
        .label {
            font-weight: bold;
            color: #555;
            width: 35%;
        }
        
        .value {
            width: 65%;
        }
        
        .closing {
            margin: 15px 0 10px 0;
            line-height: 1.6;
        }
        
        .footer {
            margin-top: 15px;
        }
        
        .footer-content {
            width: 100%;
        }
        
        .footer-content td {
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }
        
        .footer-content td:first-child {
            text-align: left;
        }
        
        .footer-content td:last-child {
            text-align: right;
            direction: rtl;
        }
        
        .signature-box {
            text-align: center;
            margin-top: 10px;
        }
        
        .signature-box img {
            max-width: 80px;
            max-height: 50px;
        }
        
        .signature-line {
            margin-top: 30px;
            border-top: 1px solid #333;
            width: 120px;
            margin-left: auto;
            margin-right: auto;
            padding-top: 3px;
            font-size: 7pt;
        }
        
        .note {
            margin-top: 15px;
            font-size: 7pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
        
        .note td {
            width: 50%;
            vertical-align: top;
            padding: 0 5px;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <table class="header-top" width="100%">
            <tr>
                <td width="30%" style="text-align: left;">
                    République Islamique de Mauritanie<br>
                    <small>Honneur - Fraternité - Justice</small>
                </td>
                <td width="40%" style="text-align: center;">
                    @if($school->logo_path && file_exists(storage_path('app/public/' . $school->logo_path)))
                        <img src="{{ storage_path('app/public/' . $school->logo_path) }}" class="logo">
                    @endif
                </td>
                <td width="30%" style="text-align: right;" dir="rtl">
                    الجمهورية الإسلامية الموريتانية<br>
                    <small>شرف - إخاء - عدالة</small>
                </td>
            </tr>
        </table>
        
        <table class="school-names" width="100%">
            <tr>
                <td style="text-align: left;">{{ $school->name_fr ?? 'École' }}</td>
                <td style="text-align: right;" dir="rtl">{{ $school->name_ar ?? 'مدرسة' }}</td>
            </tr>
        </table>
        
        <div class="school-info">
            @if($school->address_fr){{ $school->address_fr }} | @endif
            @if($school->phone)Tél: {{ $school->phone }} | @endif
            @if($school->email){{ $school->email }}@endif
        </div>
    </div>

    {{-- Title --}}
    <table class="title" width="100%">
        <tr>
            <td style="text-align: left;">ATTESTATION D'INSCRIPTION</td>
            <td style="text-align: right;" dir="rtl">شهادة التسجيل</td>
        </tr>
    </table>

    {{-- Content --}}
    <table class="bilingual">
        <tr>
            <td>
                <div class="content-text">
                    Le Directeur de l'établissement <strong>{{ $school->name_fr ?? 'École' }}</strong> 
                    atteste que l'élève ci-dessous désigné(e) est régulièrement inscrit(e) 
                    pour l'année scolaire <strong>{{ $student->schoolYear?->name }}</strong>.
                </div>
            </td>
            <td>
                <div class="content-text">
                    يشهد مدير مؤسسة <strong>{{ $school->name_ar ?? 'مدرسة' }}</strong>
                    أن التلميذ(ة) المذكور(ة) أدناه مسجل(ة) بصفة قانونية
                    للسنة الدراسية <strong>{{ $student->schoolYear?->name }}</strong>.
                </div>
            </td>
        </tr>
    </table>

    {{-- Student Info --}}
    <div class="student-info">
        <table width="100%">
            <tr>
                <td class="label">Matricule / رقم التسجيل</td>
                <td class="value">{{ $student->matricule }}</td>
            </tr>
            @if($student->nni)
            <tr>
                <td class="label">NNI / الرقم الوطني</td>
                <td class="value">{{ $student->nni }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Nom et Prénom / الاسم الكامل</td>
                <td class="value"><strong>{{ $student->full_name }}</strong> @if($student->first_name_ar)/ <span dir="rtl">{{ $student->full_name_ar }}</span>@endif</td>
            </tr>
            <tr>
                <td class="label">Date de naissance / تاريخ الازدياد</td>
                <td class="value">{{ $student->birth_date->format('d/m/Y') }}</td>
            </tr>
            @if($student->birth_place)
            <tr>
                <td class="label">Lieu de naissance / مكان الازدياد</td>
                <td class="value">{{ $student->birth_place }} @if($student->birth_place_ar)/ <span dir="rtl">{{ $student->birth_place_ar }}</span>@endif</td>
            </tr>
            @endif
            <tr>
                <td class="label">Sexe / الجنس</td>
                <td class="value">{{ $student->gender === 'male' ? 'Masculin / ذكر' : 'Féminin / أنثى' }}</td>
            </tr>
            <tr>
                <td class="label">Classe / القسم</td>
                <td class="value"><strong>{{ $student->class?->name ?? 'Non assigné' }}</strong></td>
            </tr>
            <tr>
                <td class="label">Date d'inscription / تاريخ التسجيل</td>
                <td class="value">{{ $student->enrollment_date->format('d/m/Y') }}</td>
            </tr>
        </table>
    </div>

    {{-- Closing --}}
    <table class="bilingual">
        <tr>
            <td>
                <div class="closing">
                    Cette attestation est délivrée à l'intéressé(e) pour servir et valoir ce que de droit.
                </div>
            </td>
            <td>
                <div class="closing">
                    سلمت هذه الشهادة للمعني(ة) بالأمر لتكون حجة عند الاقتضاء.
                </div>
            </td>
        </tr>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <table class="footer-content" width="100%">
            <tr>
                <td>
                    Fait à Nouakchott, le {{ $date->format('d/m/Y') }}<br><br>
                    <strong>Le Directeur</strong><br>
                    @if($school->director_name_fr){{ $school->director_name_fr }}@endif
                    
                    <div class="signature-box">
                        @if($school->signature_path && file_exists(storage_path('app/public/' . $school->signature_path)))
                            <img src="{{ storage_path('app/public/' . $school->signature_path) }}">
                        @endif
                        @if($school->stamp_path && file_exists(storage_path('app/public/' . $school->stamp_path)))
                            <img src="{{ storage_path('app/public/' . $school->stamp_path) }}">
                        @endif
                        @if(!$school->signature_path && !$school->stamp_path)
                            <div class="signature-line">Signature et cachet</div>
                        @endif
                    </div>
                </td>
                <td>
                    حرر بنواكشوط، في {{ $date->format('d/m/Y') }}<br><br>
                    <strong>المدير</strong><br>
                    @if($school->director_name_ar){{ $school->director_name_ar }}@endif
                    
                    <div class="signature-box">
                        <div class="signature-line">التوقيع والختم</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Note --}}
    <table class="note" width="100%">
        <tr>
            <td>
                <strong>N.B:</strong> Cette attestation n'est valable que pour l'année scolaire en cours.
            </td>
            <td dir="rtl">
                <strong>ملاحظة:</strong> هذه الشهادة صالحة فقط للسنة الدراسية الحالية.
            </td>
        </tr>
    </table>
</body>
</html>
