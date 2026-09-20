<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Tanda Pelajar - {{ $student->full_name }}</title>
    <style>
        @page {
            size: 85.6mm 53.98mm;
            margin: 0;
        }
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #ffffff;
            -webkit-print-color-adjust: exact;
        }
        .card {
            width: 85.6mm;
            height: 53.98mm;
            position: relative;
            box-sizing: border-box;
            background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
            color: #ffffff;
            padding: 4mm 5mm;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .card::after {
            content: '';
            position: absolute;
            top: -20mm;
            right: -20mm;
            width: 50mm;
            height: 50mm;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }
        .header {
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 2mm;
        }
        .school-title {
            font-size: 8pt;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .card-label {
            font-size: 6pt;
            color: #93c5fd;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .body-section {
            display: flex;
            gap: 4mm;
            align-items: center;
            margin-top: 2mm;
        }
        .photo {
            width: 18mm;
            height: 24mm;
            background: #e2e8f0;
            border-radius: 2mm;
            border: 1.5px solid #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 6pt;
            text-align: center;
            flex-shrink: 0;
        }
        .info {
            flex: 1;
            font-size: 6.5pt;
            line-height: 1.35;
        }
        .student-name {
            font-size: 8pt;
            font-weight: 700;
            color: #60a5fa;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 48mm;
        }
        .info-row {
            display: flex;
            margin-top: 0.8mm;
        }
        .info-label {
            width: 14mm;
            color: #94a3b8;
        }
        .info-val {
            font-weight: 600;
            color: #f8fafc;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 5pt;
            color: #94a3b8;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 1.5mm;
        }
        .barcode-box {
            font-family: monospace;
            font-size: 6pt;
            letter-spacing: 1px;
            color: #ffffff;
            background: rgba(0, 0, 0, 0.3);
            padding: 1mm 2mm;
            border-radius: 1mm;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <div>
                <div class="school-title">{{ $school->name ?? 'SMK PUSAT KEUNGGULAN' }}</div>
                <div class="card-label">KARTU TANDA PELAJAR RESMI</div>
            </div>
        </div>

        <div class="body-section">
            <div class="photo">FOTO<br>2x3</div>
            <div class="info">
                <div class="student-name">{{ $student->full_name }}</div>
                <div class="info-row">
                    <span class="info-label">NISN</span>
                    <span class="info-val">: {{ $student->nisn ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">NIS</span>
                    <span class="info-val">: {{ $student->nis ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Kelas/Komp</span>
                    <span class="info-val">: {{ $student->studyGroups?->first()?->name ?? ($student->department?->code ?? '-') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">TTL</span>
                    <span class="info-val">: {{ $student->place_of_birth ?? '-' }}, {{ $student->date_of_birth ? date('d/m/Y', strtotime((string) $student->date_of_birth)) : '-' }}</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <div>Berlaku Selama Menjadi Siswa Aktif</div>
            <div class="barcode-box">*{{ $student->nisn ?? $student->id }}*</div>
        </div>
    </div>
</body>
</html>
