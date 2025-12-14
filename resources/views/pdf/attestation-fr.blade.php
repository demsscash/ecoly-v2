<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            line-height: 1.4;
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
            text-align: left;
            vertical-align: middle;
        }
        .logo {
            max-width: 70px;
            max-height: 70px;
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
            font-size: 9pt;
            color: #666;
            margin-top: 5px;
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
            text-align: justify;
        }
        .student-info {
            background-color: #f8fafc;
            border: 2px solid #1a365d;
            padding: 15px;
            margin: 20px 0;
        }
        .info-row {
            padding: 5px 0;
            font-size: 10pt;
        }
        .info-label {
            font-weight: bold;
            color: #1a365d;
            display: inline-block;
            width: 35%;
        }
        .info-value {
            display: inline-block;
            width: 63%;
        }
        .closing {
            margin: 20px 0;
            line-height: 1.6;
        }
        .date-location {
            text-align: right;
            margin: 25px 0 15px 0;
            font-style: italic;
        }
        .signatures {
            margin-top: 30px;
        }
        .signature-left {
            float: left;
            width: 45%;
            text-align: center;
        }
        .signature-right {
            float: right;
            width: 45%;
            text-align: center;
        }
        .signature-label {
            font-weight: bold;
            margin-bottom: 40px;
            color: #1a365d;
            font-size: 11pt;
        }
        .signature-img {
            max-width: 120px;
            max-height: 60px;
        }
        .stamp-img {
            max-width: 80px;
            max-height: 80px;
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
                <td class="logo-cell">
                    @if(!empty($school['logo_path']) && file_exists(public_path('storage/' . $school['logo_path'])))
                        <img src="{{ public_path('storage/' . $school['logo_path']) }}" class="logo" alt="Logo">
                    @endif
                </td>
                <td class="school-name-cell">
                    <div class="school-name">{{ $school['name_fr'] ?? 'École' }}</div>
                    <div class="country-info">République Islamique de Mauritanie</div>
                    <div class="country-info" style="font-size: 8pt;">Honneur - Fraternité - Justice</div>
                </td>
                <td style="width: 15%;"></td>
            </tr>
        </table>
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
    </div>

    {{-- Content --}}
    <div class="content">
        <p>Le Directeur de <strong>{{ $school['name_fr'] ?? 'l\'établissement' }}</strong> 
        atteste par la présente que :</p>
    </div>

    {{-- Student Info --}}
    <div class="student-info">
        <div class="info-row">
            <span class="info-label">Nom et Prénom :</span>
            <span class="info-value"><strong>{{ $student['full_name'] }}</strong></span>
        </div>

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

        <div class="info-row">
            <span class="info-label">Année scolaire :</span>
            <span class="info-value"><strong>{{ $student['school_year'] }}</strong></span>
        </div>

        @if(!empty($student['guardian_name']))
        <div class="info-row">
            <span class="info-label">Nom du tuteur :</span>
            <span class="info-value">{{ $student['guardian_name'] }}</span>
        </div>
        @endif
    </div>

    {{-- Closing --}}
    <div class="closing">
        <p>Est régulièrement inscrit(e) dans notre établissement pour l'année scolaire 
        <strong>{{ $student['school_year'] }}</strong>.</p>
        <p style="margin-top: 10px;">Cette attestation est délivrée pour servir et valoir ce que de droit.</p>
    </div>

    {{-- Date --}}
    <div class="date-location">
        Fait à Nouakchott, le {{ now()->locale('fr')->isoFormat('D MMMM Y') }}
    </div>

    {{-- Signatures --}}
    <div class="signatures">
        <div class="signature-left">
            <div style="margin-bottom: 10px; font-size: 10pt;">Cachet de l'établissement</div>
            @if(!empty($school['stamp_path']) && file_exists(public_path('storage/' . $school['stamp_path'])))
                <img src="{{ public_path('storage/' . $school['stamp_path']) }}" class="stamp-img" alt="Cachet">
            @endif
        </div>
        
        <div class="signature-right">
            <div class="signature-label">Le Directeur</div>
            @if(!empty($school['director_name_fr']))
                <div style="margin-bottom: 10px;">{{ $school['director_name_fr'] }}</div>
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
