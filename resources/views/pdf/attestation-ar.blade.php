<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            direction: rtl;
        }
        .header {
            border-bottom: 2px solid #1a365d;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header-top {
            width: 100%;
            margin-bottom: 8px;
        }
        .logo-cell {
            width: 15%;
            text-align: right;
            vertical-align: middle;
        }
        .logo {
            max-width: 65px;
            max-height: 65px;
        }
        .school-name-cell {
            width: 70%;
            text-align: center;
            vertical-align: middle;
        }
        .school-name {
            font-size: 16pt;
            font-weight: bold;
            color: #1a365d;
        }
        .country-info {
            font-size: 10pt;
            color: #666;
            margin-top: 4px;
        }
        .school-info {
            font-size: 9pt;
            color: #666;
            text-align: center;
            margin-top: 5px;
        }
        .reference {
            font-size: 9pt;
            color: #666;
            margin: 15px 0;
            text-align: left;
        }
        .title {
            text-align: center;
            margin: 25px 0;
        }
        .title-main {
            font-size: 16pt;
            font-weight: bold;
            text-decoration: underline;
            color: #1a365d;
        }
        .content {
            margin: 20px 0;
            line-height: 1.6;
            font-size: 10pt;
        }
        .student-info {
            background-color: #f8fafc;
            border: 2px solid #1a365d;
            padding: 12px;
            margin: 20px 0;
        }
        .info-row {
            padding: 4px 0;
            font-size: 10pt;
        }
        .info-label {
            font-weight: bold;
            color: #1a365d;
            display: inline-block;
            width: 32%;
        }
        .info-value {
            display: inline-block;
            width: 66%;
        }
        .closing {
            margin: 20px 0;
            line-height: 1.6;
            font-size: 10pt;
        }
        .date-location {
            text-align: left;
            margin: 30px 0 20px 0;
            font-size: 10pt;
        }
        .signatures {
            margin-top: 35px;
        }
        .signature-left {
            float: right;
            width: 48%;
            text-align: center;
            font-size: 10pt;
        }
        .signature-right {
            float: left;
            width: 48%;
            text-align: center;
            font-size: 10pt;
        }
        .signature-label {
            font-weight: bold;
            margin-bottom: 40px;
            color: #1a365d;
            font-size: 11pt;
        }
        .signature-img {
            max-width: 110px;
            max-height: 55px;
        }
        .stamp-img {
            max-width: 75px;
            max-height: 75px;
        }
        .note {
            margin-top: 70px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 9pt;
            color: #666;
            clear: both;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <table class="header-top" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 15%;"></td>
                <td class="school-name-cell">
                    <div class="school-name">{{ $school['name_ar'] ?? 'مدرسة' }}</div>
                    <div class="country-info">الجمهورية الإسلامية الموريتانية</div>
                    <div class="country-info" style="font-size: 9pt;">شرف - إخاء - عدالة</div>
                </td>
                <td class="logo-cell">
                    @if(!empty($school['logo_path']) && file_exists(public_path('storage/' . $school['logo_path'])))
                        <img src="{{ public_path('storage/' . $school['logo_path']) }}" class="logo" alt="شعار">
                    @endif
                </td>
            </tr>
        </table>
        <div class="school-info">
            @if(!empty($school['address_ar'])){{ $school['address_ar'] }}@endif
            @if(!empty($school['phone'])) • هاتف: {{ $school['phone'] }}@endif
        </div>
    </div>

    <div class="reference">{{ str_pad($student['id'], 6, '0', STR_PAD_LEFT) }}/{{ date('Y') }} :رقم</div>

    {{-- Title --}}
    <div class="title">
        <div class="title-main">شهادة التسجيل</div>
    </div>

    {{-- Content --}}
    <div class="content">
        <p>يشهد مدير <strong>{{ $school['name_ar'] ?? 'المؤسسة' }}</strong> أن:</p>
    </div>

    {{-- Student Info --}}
    <div class="student-info">
        <div class="info-row">
            <span class="info-label">الاسم الكامل:</span>
            <span class="info-value"><strong>{{ $student['full_name_ar'] ?: $student['full_name'] }}</strong></span>
        </div>

        <div class="info-row">
            <span class="info-label">تاريخ الازدياد:</span>
            <span class="info-value">{{ $student['birth_date'] }}</span>
        </div>

        @if(!empty($student['birth_place_ar']) || !empty($student['birth_place']))
        <div class="info-row">
            <span class="info-label">مكان الازدياد:</span>
            <span class="info-value">{{ $student['birth_place_ar'] ?: $student['birth_place'] }}</span>
        </div>
        @endif

        @if(!empty($student['nni']))
        <div class="info-row">
            <span class="info-label">الرقم الوطني:</span>
            <span class="info-value">{{ $student['nni'] }}</span>
        </div>
        @endif

        <div class="info-row">
            <span class="info-label">رقم التسجيل:</span>
            <span class="info-value"><strong>{{ $student['matricule'] }}</strong></span>
        </div>

        <div class="info-row">
            <span class="info-label">القسم:</span>
            <span class="info-value"><strong>{{ $student['class'] }}</strong></span>
        </div>

        <div class="info-row">
            <span class="info-label">السنة الدراسية:</span>
            <span class="info-value"><strong>{{ $student['school_year'] }}</strong></span>
        </div>

        @if(!empty($student['guardian_name_ar']) || !empty($student['guardian_name']))
        <div class="info-row">
            <span class="info-label">اسم الولي:</span>
            <span class="info-value">{{ $student['guardian_name_ar'] ?: $student['guardian_name'] }}</span>
        </div>
        @endif
    </div>

    {{-- Closing --}}
    <div class="closing">
        <p>مسجل في مؤسستنا بصفة قانونية للسنة الدراسية 
        <strong>{{ $student['school_year'] }}</strong>.</p>
        <p style="margin-top: 8px;">سلمت هذه الشهادة للمعني بالأمر لتكون حجة عند الاقتضاء.</p>
    </div>

    {{-- Date --}}
    <div class="date-location">
        نواكشوط، {{ date('d/m/Y') }}
    </div>

    {{-- Signatures --}}
    <div class="signatures">
        <div class="signature-left">
            <div style="margin-bottom: 10px;">ختم المؤسسة</div>
            @if(!empty($school['stamp_path']) && file_exists(public_path('storage/' . $school['stamp_path'])))
                <img src="{{ public_path('storage/' . $school['stamp_path']) }}" class="stamp-img" alt="ختم">
            @endif
        </div>
        
        <div class="signature-right">
            <div class="signature-label">المدير</div>
            @if(!empty($school['director_name_ar']))
                <div style="margin-bottom: 8px;">{{ $school['director_name_ar'] }}</div>
            @endif
            @if(!empty($school['signature_path']) && file_exists(public_path('storage/' . $school['signature_path'])))
                <img src="{{ public_path('storage/' . $school['signature_path']) }}" class="signature-img" alt="توقيع">
            @endif
        </div>
    </div>

    {{-- Note --}}
    <div class="note">
        <strong>ملاحظة:</strong> هذه الشهادة صالحة فقط للسنة الدراسية الحالية.
    </div>
</body>
</html>
