<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Pemilih DPT - {{ $event->title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background: #ffffff; }
        .grid-container {
            display: flex;
            flex-wrap: wrap;
            padding: 5mm;
            gap: 5mm;
        }
        .voter-card {
            width: 85mm;
            height: 52mm;
            border: 1.5px dashed #4b5563;
            border-radius: 3mm;
            padding: 4mm 5mm;
            page-break-inside: avoid;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: #ffffff;
        }
        .card-header {
            text-align: center;
            border-bottom: 1.5px solid #2563eb;
            padding-bottom: 2mm;
        }
        .school-title { font-size: 7pt; font-weight: bold; color: #1e3a8a; text-transform: uppercase; }
        .event-title { font-size: 8.5pt; font-weight: bold; color: #111827; }
        .card-body {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 2mm 0;
        }
        .voter-meta { font-size: 7.5pt; line-height: 1.4; color: #1f2937; }
        .voter-name { font-size: 8.5pt; font-weight: bold; color: #1e40af; margin-bottom: 1mm; }
        .token-container {
            text-align: center;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 2mm;
            padding: 2mm 3mm;
        }
        .token-label { font-size: 6pt; color: #1e40af; font-weight: bold; text-transform: uppercase; }
        .token-code { font-family: monospace; font-size: 11pt; font-weight: 800; letter-spacing: 2px; color: #1e3a8a; }
        .card-footer {
            display: flex;
            justify-content: space-between;
            font-size: 6pt;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 1.5mm;
        }
    </style>
</head>
<body>
    <div class="grid-container">
        @foreach($voters as $voter)
        <div class="voter-card">
            <div class="card-header">
                <div class="school-title">{{ $school->name ?? 'PEMILIHAN RAYA KESISWAAN' }}</div>
                <div class="event-title">{{ $event->title }}</div>
            </div>

            <div class="card-body">
                <div class="voter-meta">
                    <div class="voter-name">{{ $voter->voter_name }}</div>
                    <div>ID: {{ $voter->voter_identifier }}</div>
                    <div>Kategori: {{ strtoupper($voter->voter_type) }}</div>
                </div>

                <div class="token-container">
                    <div class="token-label">TOKEN BILIK SUARA</div>
                    <div class="token-code">{{ $voter->token }}</div>
                </div>
            </div>

            <div class="card-footer">
                <div>Rahasia & Berlaku 1 Kali Pencoblosan</div>
                <div>Status DPT: Terverifikasi</div>
            </div>
        </div>
        @endforeach
    </div>
</body>
</html>
