<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Belajar (Raport) - {{ $student->full_name }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 10pt; line-height: 1.4; color: #111827; margin: 15mm; }
        .header { text-align: center; border-bottom: 2px solid #111827; padding-bottom: 8px; margin-bottom: 15px; }
        .school-name { font-size: 14pt; font-weight: bold; text-transform: uppercase; }
        .raport-title { font-size: 11pt; font-weight: bold; margin-top: 4px; }
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 9pt; }
        .meta-table td { padding: 3px 0; vertical-align: top; }
        .score-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 8.5pt; }
        .score-table th, .score-table td { border: 1px solid #374151; padding: 5px 6px; }
        .score-table th { background: #f3f4f6; text-align: center; font-weight: bold; }
        .text-center { text-align: center; }
        .attendance-box { width: 45%; border-collapse: collapse; font-size: 8.5pt; margin-bottom: 20px; }
        .attendance-box th, .attendance-box td { border: 1px solid #374151; padding: 4px 6px; }
        .attendance-box th { background: #f3f4f6; text-align: left; }
        .signature-table { width: 100%; margin-top: 25px; font-size: 9pt; }
        .signature-table td { text-align: center; width: 33%; vertical-align: top; }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">{{ $school->name ?? 'SEKOLAH PUSAT KEUNGGULAN' }}</div>
        <div class="raport-title">LAPORAN HASIL BELAJAR PESERTA DIDIK (KURIKULUM MERDEKA)</div>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 15%;">Nama Peserta Didik</td><td style="width: 2%;">:</td><td style="width: 33%;"><b>{{ $student->full_name }}</b></td>
            <td style="width: 15%;">Kelas / Rombel</td><td style="width: 2%;">:</td><td style="width: 33%;">{{ $study_group->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>NISN / NIS</td><td>:</td><td>{{ $student->nisn ?? '-' }} / {{ $student->nis ?? '-' }}</td>
            <td>Fase / Semester</td><td>:</td><td>Fase E/F - {{ $semester->name ?? 'Semester Ganjil' }}</td>
        </tr>
        <tr>
            <td>Program Keahlian</td><td>:</td><td>{{ $student->department?->name ?? '-' }}</td>
            <td>Tahun Ajaran</td><td>:</td><td>{{ $academic_year->name ?? date('Y') . '/' . (date('Y') + 1) }}</td>
        </tr>
    </table>

    <table class="score-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Mata Pelajaran</th>
                <th style="width: 8%;">Nilai Akhir</th>
                <th style="width: 6%;">Predikat</th>
                <th style="width: 56%;">Capaian Kompetensi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($report_cards as $index => $card)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $card->subject?->name ?? '-' }}</td>
                <td class="text-center"><b>{{ number_format((float) $card->final_score, 0) }}</b></td>
                <td class="text-center">{{ $card->letter_grade ?? '-' }}</td>
                <td style="font-size: 8pt;">
                    <div>• {{ $card->achieved_competency }}</div>
                    @if(!empty($card->improved_competency))
                    <div style="margin-top: 2px; color: #4b5563;">• {{ $card->improved_competency }}</div>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Belum ada data nilai raport untuk semester ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="font-weight: bold; font-size: 9pt; margin-bottom: 4px;">Ketidakhadiran :</div>
    <table class="attendance-box">
        <tr><td style="width: 70%;">Sakit (S)</td><td class="text-center" style="width: 30%;">{{ $attendance_summary['sick'] ?? 0 }} hari</td></tr>
        <tr><td>Izin (I)</td><td class="text-center">{{ $attendance_summary['permission'] ?? 0 }} hari</td></tr>
        <tr><td>Tanpa Keterangan (A)</td><td class="text-center">{{ $attendance_summary['absent'] ?? 0 }} hari</td></tr>
    </table>

    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>Orang Tua / Wali Peserta Didik
                <div style="margin-top: 55px;">( .................................................. )</div>
            </td>
            <td>
                <br>Wali Kelas,
                <div style="margin-top: 55px; font-weight: bold;">( {{ $study_group->homeroomTeacher?->full_name ?? '..................................................' }} )</div>
                <div>NIP. {{ $study_group->homeroomTeacher?->nip ?? '....................................' }}</div>
            </td>
            <td>
                {{ $school->city ?? 'Kota' }}, {{ date('d F Y') }}<br>Kepala Satuan Pendidikan,
                <div style="margin-top: 55px; font-weight: bold; text-decoration: underline;">{{ $school->principal_name ?? '..................................................' }}</div>
                <div>NIP. {{ $school->principal_nip ?? '....................................' }}</div>
            </td>
        </tr>
    </table>
</body>
</html>
