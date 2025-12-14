<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1a365d;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .school-name {
            font-size: 14pt;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 3px;
        }
        .school-name-ar {
            font-size: 12pt;
            font-weight: bold;
            color: #1a365d;
            margin-bottom: 3px;
        }
        .school-info {
            font-size: 8pt;
            color: #666;
        }
        .reference {
            font-size: 8pt;
            color: #666;
            margin: 10px 0;
        }
        .title {
            text-align: center;
            margin: 15px 0;
        }
        .title-main {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            color: #1a365d;
            margin-bottom: 5px;
        }
        .title-ar {
            font-size: 12pt;
            font-weight: bold;
            color: #1a365d;
        }
        .content {
            margin: 12px 0;
            line-height: 1.4;
            font-size: 9.5pt;
        }
        .student-info {
            background-color: #f8fafc;
            border: 2px solid #1a365d;
            padding: 10px;
            margin: 12px 0;
        }
        .info-row {
            padding: 3px 0;
            font-size: 9pt;
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
            margin: 12px 0;
            font-size: 9.5pt;
        }
        .date-location {
            text-align: right;
            margin: 15px 0 10px 0;
            font-style: italic;
            font-size: 9pt;
        }
        .signatures {
            margin-top: 20px;
        }
        .signature-left {
            float: left;
            width: 48%;
            text-align: center;
            font-size: 9pt;
        }
        .signature-right {
            float: right;
            width: 48%;
            text-align: center;
            font-size: 9pt;
        }
        .signature-label {
            font-weight: bold;
            margin-bottom: 30px;
            color: #1a365d;
        }
        .signature-img {
            max-width: 100px;
            max-height: 50px;
        }
        .stamp-img {
            max-width: 70px;
            max-height: 70px;
        }
        .note {
            margin-top: 60px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            font-size: 8pt;
            color: #666;
            clear: both;
        }
        .arabic {
            direction: rtl;
            text-align: right;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <div class="school-name">{{ $school['name_fr'] ?? 'École' }}</div>
        @if(!empty($school['name_ar']))
            <div class="school-name-ar arabic">{{ $school['name_ar'] }}</div>
        @endif
        <div class="school-info">
            @if(!empty($school['address_fr'])){{ $school['address_fr'] }}@endif
            @if(!empty($school['phone'])) • Tél: {{ $school['phone'] }}@endif
            @if(!empty($school['email'])) • {{ $school['email'] }}@endif
        </div>
    </div>

    <div class="reference">N° {{ str_pad($student['id'], 6, '0', STR_PAD_LEFT) }}/{{ date('Y') }}</div>

    {{-- Title --}}
    <div class="title">
        <div class="title-main">ATTESTATION D'INSCRIPTION</div>
        <div class="title-ar arabic">شهادة التسجيل</div>
    </div>

    {{-- Content --}}
    <div class="content">
        Le Directeur de <strong>{{ $school['name_fr'] ?? 'l\'établissement' }}</strong> 
        atteste que l'élève ci-dessous est régulièrement inscrit(e) pour l'année scolaire 
        <strong>{{ $student['school_year'] }}</strong>.
    </div>

    {{-- Student Info --}}
    <div class="student-info">
        <div class="info-row">
            <span class="info-label">Nom et Prénom :</span>
            <span class="info-value"><strong>{{ $student['full_name'] }}</strong></span>
        </div>
        
        @if(!empty($student['full_name_ar']))
        <div class="info-row arabic">
            <span class="info-label">الاسم الكامل :</span>
            <span class="info-value"><strong>{{ $student['full_name_ar'] }}</strong></span>
        </div>
        @endif

        <div class="info-row">
            <span class="info-label">Date de naissance :</span>
            <span class="info-value">{{ $student['birth_date'] }}</span>
        </div>

        @if(!empty($student['birth_place']))
        <div class="info-row">
            <span class="info-label">Lieu de naissance :</span>
            <span class="info-value">{{ $student['birth_place'] }}</span>
        </div>
        @endif

        @if(!empty($student['nni']))
        <div class="info-row">
            <span class="info-label">N° National (NNI) :</span>
            <span class="info-value">{{ $student['nni'] }}</span>
        </div>
        @endif

        <div class="info-row">
            <span class="info-label">Matricule :</span>
            <span class="info-value"><strong>{{ $student['matricule'] }}</strong></span>
        </div>

        <div class="info-row">
            <span class="info-label">Classe :</span>
            <span class="info-value"><strong>{{ $student['class'] }}</strong></span>
        </div>

        @if(!empty($student['guardian_name']))
        <div class="info-row">
            <span class="info-label">Tuteur :</span>
            <span class="info-value">{{ $student['guardian_name'] }}</span>
        </div>
        @endif
    </div>

    {{-- Closing --}}
    <div class="closing">
        Cette attestation est délivrée pour servir et valoir ce que de droit.
    </div>

    {{-- Date --}}
    <div class="date-location">
        Nouakchott, le {{ now()->locale('fr')->isoFormat('D MMMM Y') }}
    </div>

    {{-- Signatures --}}
    <div class="signatures">
        <div class="signature-left">
            Cachet
            @if(!empty($school['stamp_path']) && file_exists(public_path('storage/' . $school['stamp_path'])))
                <br><img src="{{ public_path('storage/' . $school['stamp_path']) }}" class="stamp-img" alt="Cachet">
            @endif
        </div>
        
        <div class="signature-right">
            <div class="signature-label">Le Directeur</div>
            @if(!empty($school['director_name_fr']))
                <div style="margin-bottom: 5px; font-size: 9pt;">{{ $school['director_name_fr'] }}</div>
            @endif
            @if(!empty($school['signature_path']) && file_exists(public_path('storage/' . $school['signature_path'])))
                <img src="{{ public_path('storage/' . $school['signature_path']) }}" class="signature-img" alt="Signature">
            @endif
        </div>
    </div>

    {{-- Note --}}
    <div class="note">
        <strong>N.B:</strong> Cette attestation n'est valable que pour l'année scolaire en cours.
    </div>
</body>
</html>
