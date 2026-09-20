<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Induk Siswa - {{ $student->full_name }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 11px; color: #1f2937; line-height: 1.5; margin: 20px; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #111827; padding-bottom: 12px; }
        .school-name { font-size: 16px; font-weight: 700; text-transform: uppercase; margin-bottom: 4px; }
        .title { font-size: 13px; font-weight: 600; text-decoration: underline; margin-bottom: 2px; }
        .subtitle { font-size: 10px; color: #4b5563; }
        .section-title { font-weight: 700; font-size: 11px; background: #f3f4f6; padding: 6px 8px; margin-top: 15px; margin-bottom: 8px; border-left: 3px solid #2563eb; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 4px 6px; vertical-align: top; }
        .label { width: 160px; color: #4b5563; }
        .separator { width: 10px; text-align: center; }
        .value { color: #111827; font-weight: 500; }
        .photo-box { width: 90px; height: 120px; border: 1px solid #9ca3af; text-align: center; line-height: 120px; font-size: 9px; color: #9ca3af; }
        .footer { margin-top: 35px; width: 100%; }
        .signature-table { width: 100%; }
        .signature-table td { text-align: center; width: 50%; }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">{{ $school->name ?? 'INSTITUSI PENDIDIKAN' }}</div>
        <div class="title">LEMBAR BUKU INDUK SISWA</div>
        <div class="subtitle">Nomor Induk Siswa Nasional: {{ $student->nisn ?? '-' }} | NPSN: {{ $school->npsn ?? '-' }}</div>
    </div>

    <table style="margin-bottom: 10px;">
        <tr>
            <td style="width: 100px;">
                <div class="photo-box">Pas Foto 3x4</div>
            </td>
            <td>
                <div class="section-title" style="margin-top: 0;">A. KETERANGAN PRIBADI SISWA</div>
                <table>
                    <tr><td class="label">Nama Lengkap</td><td class="separator">:</td><td class="value">{{ $student->full_name }}</td></tr>
                    <tr><td class="label">Nama Panggilan / Alias</td><td class="separator">:</td><td class="value">{{ $student->nickname ?? '-' }}</td></tr>
                    <tr><td class="label">Jenis Kelamin</td><td class="separator">:</td><td class="value">{{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                    <tr><td class="label">NISN / NIS</td><td class="separator">:</td><td class="value">{{ $student->nisn ?? '-' }} / {{ $student->nis ?? '-' }}</td></tr>
                    <tr><td class="label">NIK</td><td class="separator">:</td><td class="value">{{ $student->nik ?? '-' }}</td></tr>
                    <tr><td class="label">Tempat, Tanggal Lahir</td><td class="separator">:</td><td class="value">{{ $student->place_of_birth ?? '-' }}, {{ $student->date_of_birth ? date('d F Y', strtotime((string) $student->date_of_birth)) : '-' }}</td></tr>
                    <tr><td class="label">Agama</td><td class="separator">:</td><td class="value">{{ $student->religion ?? '-' }}</td></tr>
                    <tr><td class="label">Alamat Tempat Tinggal</td><td class="separator">:</td><td class="value">{{ $student->address ?? '-' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="section-title">B. KETERANGAN ORANG TUA / WALI</div>
    <table>
        <tr><td class="label">Nama Ayah Kandung</td><td class="separator">:</td><td class="value">{{ $student->parent?->father_name ?? '-' }}</td></tr>
        <tr><td class="label">NIK Ayah</td><td class="separator">:</td><td class="value">{{ $student->parent?->father_nik ?? '-' }}</td></tr>
        <tr><td class="label">Pekerjaan Ayah</td><td class="separator">:</td><td class="value">{{ $student->parent?->father_occupation ?? '-' }}</td></tr>
        <tr><td class="label">Nama Ibu Kandung</td><td class="separator">:</td><td class="value">{{ $student->parent?->mother_name ?? '-' }}</td></tr>
        <tr><td class="label">NIK Ibu</td><td class="separator">:</td><td class="value">{{ $student->parent?->mother_nik ?? '-' }}</td></tr>
        <tr><td class="label">Pekerjaan Ibu</td><td class="separator">:</td><td class="value">{{ $student->parent?->mother_occupation ?? '-' }}</td></tr>
        <tr><td class="label">No. Telepon / WhatsApp</td><td class="separator">:</td><td class="value">{{ $student->parent?->mother_phone ?? ($student->parent?->father_phone ?? '-') }}</td></tr>
    </table>

    <div class="section-title">C. KETERANGAN PENDIDIKAN & ROMBEL</div>
    <table>
        <tr><td class="label">Kompetensi Keahlian / Jurusan</td><td class="separator">:</td><td class="value">{{ $student->department?->name ?? '-' }}</td></tr>
        <tr><td class="label">Rombongan Belajar (Kelas)</td><td class="separator">:</td><td class="value">{{ $student->studyGroups?->first()?->name ?? '-' }}</td></tr>
        <tr><td class="label">Tahun Masuk</td><td class="separator">:</td><td class="value">{{ $student->entry_year ?? date('Y') }}</td></tr>
        <tr><td class="label">Status Peserta Didik</td><td class="separator">:</td><td class="value">{{ strtoupper($student->status ?? 'ACTIVE') }}</td></tr>
    </table>

    <div class="footer">
        <table class="signature-table">
            <tr>
                <td></td>
                <td>
                    <p>{{ $school->city ?? 'Kota' }}, {{ date('d F Y') }}<br>Kepala Satuan Pendidikan,</p>
                    <div style="margin-top: 60px; font-weight: bold; text-decoration: underline;">
                        {{ $school->principal_name ?? '................................................' }}
                    </div>
                    <div>NIP. {{ $school->principal_nip ?? '....................................' }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
