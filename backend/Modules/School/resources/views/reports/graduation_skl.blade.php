<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Lulus - {{ $student->full_name }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.6;
            margin: 25mm 20mm;
            color: #000000;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000000;
            padding-bottom: 12px;
            margin-bottom: 25px;
        }
        .header h3 { margin: 0; font-size: 13pt; text-transform: uppercase; }
        .header h2 { margin: 2px 0; font-size: 15pt; text-transform: uppercase; font-weight: bold; }
        .header p { margin: 0; font-size: 10pt; font-style: italic; }
        .title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .letter-number {
            text-align: center;
            font-size: 11pt;
            margin-bottom: 25px;
        }
        table {
            width: 100%;
            margin-bottom: 15px;
        }
        td {
            padding: 4px;
            vertical-align: top;
        }
        .statement {
            text-align: justify;
            text-indent: 30px;
            margin-bottom: 15px;
        }
        .result-box {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            font-size: 16pt;
            font-weight: bold;
            letter-spacing: 2px;
        }
        .signature-section {
            width: 100%;
            margin-top: 40px;
        }
        .signature-table {
            width: 100%;
        }
        .signature-table td {
            text-align: center;
            width: 50%;
        }
    </style>
</head>
<body>
    <div class="header">
        <h3>PEMERINTAH DAERAH PROVINSI</h3>
        <h3>DINAS PENDIDIKAN</h3>
        <h2>{{ strtoupper($school->name ?? 'SEKOLAH MENENGAH KEJURUAN') }}</h2>
        <p>{{ $school->address ?? 'Jalan Pendidikan No. 1' }} | NPSN: {{ $school->npsn ?? '-' }}</p>
    </div>

    <div class="title">SURAT KETERANGAN LULUS</div>
    <div class="letter-number">Nomor: 421.5 / {{ $student->id }} / SMK / {{ date('Y') }}</div>

    <p class="statement">
        Kepala {{ $school->name ?? 'SMK' }}, dengan ini menerangkan bahwa peserta didik di bawah ini:
    </p>

    <table>
        <tr><td style="width: 200px;">Nama Lengkap</td><td style="width: 10px;">:</td><td><b>{{ $student->full_name }}</b></td></tr>
        <tr><td>Tempat, Tanggal Lahir</td><td>:</td><td>{{ $student->place_of_birth ?? '-' }}, {{ $student->date_of_birth ? date('d F Y', strtotime((string) $student->date_of_birth)) : '-' }}</td></tr>
        <tr><td>Nomor Induk Siswa (NIS)</td><td>:</td><td>{{ $student->nis ?? '-' }}</td></tr>
        <tr><td>Nomor Induk Siswa Nasional (NISN)</td><td>:</td><td>{{ $student->nisn ?? '-' }}</td></tr>
        <tr><td>Program / Kompetensi Keahlian</td><td>:</td><td>{{ $student->department?->name ?? '-' }}</td></tr>
    </table>

    <p class="statement">
        Berdasarkan kriteria kelulusan dan hasil Rapat Pleno Dewan Pendidik tentang Penentuan Kelulusan Peserta Didik Tahun Ajaran {{ date('Y') - 1 }}/{{ date('Y') }}, yang bersangkutan dinyatakan:
    </p>

    <div class="result-box">
        *** L U L U S ***
    </div>

    <p class="statement">
        Surat Keterangan Lulus ini bersifat sementara dan berlaku sah sampai dengan diterbitkannya Ijazah resmi dari Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi.
    </p>

    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td style="width: 50%;">
                    <div style="width: 80px; height: 100px; border: 1px dashed #999; margin: auto; line-height: 100px; font-size: 8pt; color: #999;">Pas Foto 3x4</div>
                </td>
                <td style="width: 50%;">
                    <div>{{ $school->city ?? 'Kota' }}, {{ date('d F Y') }}</div>
                    <div>Kepala Satuan Pendidikan,</div>
                    <div style="margin-top: 60px; font-weight: bold; text-decoration: underline;">
                        {{ $school->principal_name ?? '......................................................' }}
                    </div>
                    <div>NIP. {{ $school->principal_nip ?? '................................................' }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
