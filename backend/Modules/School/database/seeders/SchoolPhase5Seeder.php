<?php

declare(strict_types=1);

namespace Modules\School\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\School\Models\Communication\WhatsAppLog;
use Modules\School\Models\Facilities\Asset;
use Modules\School\Models\Facilities\MaintenanceTicket;
use Modules\School\Models\Facilities\Room;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Integration\KatampiConfig;
use Modules\School\Models\Student\Student;

class SchoolPhase5Seeder extends Seeder
{
    public function run(): void
    {
        $school = School::first();
        if (!$school) {
            return;
        }

        $teachers = Staff::where('school_id', $school->id)->get();
        $students = Student::where('school_id', $school->id)->get();
        $reporter = $teachers->first();

        // 1. Rooms (Sarana & Prasarana Ruangan)
        $labRpl = Room::firstOrCreate(
            ['code' => 'LAB-RPL-01', 'school_id' => $school->id],
            [
                'name' => 'Lab Komputer Rekayasa Perangkat Lunak 1',
                'type' => 'laboratory',
                'capacity' => 36,
                'floor' => 2,
                'condition' => 'baik',
                'description' => 'Laboratorium coding & pengembangan web/mobile dengan 36 unit workstation.',
            ]
        );

        $labTkj = Room::firstOrCreate(
            ['code' => 'LAB-TKJ-01', 'school_id' => $school->id],
            [
                'name' => 'Lab Jaringan Komputer & Fiber Optic',
                'type' => 'laboratory',
                'capacity' => 36,
                'floor' => 2,
                'condition' => 'baik',
                'description' => 'Laboratorium infrastruktur jaringan, routing Mikrotik/Cisco, dan fusion splicer FO.',
            ]
        );

        $bengkelOto = Room::firstOrCreate(
            ['code' => 'WRK-OTO-01', 'school_id' => $school->id],
            [
                'name' => 'Bengkel Teknik Kendaraan Ringan Otomotif',
                'type' => 'workshop',
                'capacity' => 30,
                'floor' => 1,
                'condition' => 'rusak_ringan',
                'description' => 'Bengkel praktikum chasis, kelistrikan bodi, dan mesin kendaraan bermotor.',
            ]
        );

        $ruangKelas = Room::firstOrCreate(
            ['code' => 'CLS-X-PPLG', 'school_id' => $school->id],
            [
                'name' => 'Ruang Teori X-PPLG-1',
                'type' => 'classroom',
                'capacity' => 36,
                'floor' => 1,
                'condition' => 'baik',
                'description' => 'Ruang kelas teori reguler lengkap dengan smart projector & AC.',
            ]
        );

        $perpus = Room::firstOrCreate(
            ['code' => 'LIB-01', 'school_id' => $school->id],
            [
                'name' => 'Perpustakaan Graha Cendekia',
                'type' => 'library',
                'capacity' => 80,
                'floor' => 1,
                'condition' => 'baik',
                'description' => 'Perpustakaan digital dan fisik dengan koleksi buku vokasi & literatur umum.',
            ]
        );

        $aula = Room::firstOrCreate(
            ['code' => 'AULA-UTM', 'school_id' => $school->id],
            [
                'name' => 'Auditorium Utama & Hall Serbaguna',
                'type' => 'hall',
                'capacity' => 400,
                'floor' => 3,
                'condition' => 'baik',
                'description' => 'Gedung serbaguna untuk upacara tertutup, seminar nasional, dan wisuda kelulusan.',
            ]
        );

        // 2. Assets (Sarpras Inventaris Barang)
        $pcWorkstation = Asset::firstOrCreate(
            ['code' => 'AST-PC-RPL-01', 'school_id' => $school->id],
            [
                'room_id' => $labRpl->id,
                'name' => 'PC Workstation Core i7 / 16GB / SSD NVMe',
                'category' => 'elektronik',
                'quantity' => 36,
                'condition' => 'baik',
                'brand_model' => 'Dell OptiPlex 7090 MT',
                'acquisition_year' => 2025,
                'source_of_fund' => 'BOS Kinerja Vokasi',
                'notes' => '36 unit siap pakai untuk ujian KataMPI CBT dan praktikum pemrograman.',
            ]
        );

        $switchCisco = Asset::firstOrCreate(
            ['code' => 'AST-SW-TKJ-01', 'school_id' => $school->id],
            [
                'room_id' => $labTkj->id,
                'name' => 'Switch Manageable Catalyst 24-Port Gigabit',
                'category' => 'elektronik',
                'quantity' => 2,
                'condition' => 'baik',
                'brand_model' => 'Cisco Catalyst 2960-X',
                'acquisition_year' => 2024,
                'source_of_fund' => 'DAK Fisik SMK',
                'notes' => 'Switch backbone jaringan laboratorium TKJ.',
            ]
        );

        $balancingMachine = Asset::firstOrCreate(
            ['code' => 'AST-MCH-OTO-01', 'school_id' => $school->id],
            [
                'room_id' => $bengkelOto->id,
                'name' => 'Mesin Digital Wheel Balancing & Tyre Changer',
                'category' => 'alat_praktik',
                'quantity' => 1,
                'condition' => 'rusak_ringan',
                'brand_model' => 'Corghi EM9280 Italy',
                'acquisition_year' => 2023,
                'source_of_fund' => 'BOS Reguler',
                'notes' => 'Display kalibrasi kadang bergeser 5 gram, butuh kalibrasi ulang teknisi.',
            ]
        );

        $projector = Asset::firstOrCreate(
            ['code' => 'AST-PRJ-CLS-01', 'school_id' => $school->id],
            [
                'room_id' => $ruangKelas->id,
                'name' => 'Proyektor Epson EB-X500 3600 Lumens',
                'category' => 'elektronik',
                'quantity' => 1,
                'condition' => 'rusak_ringan',
                'brand_model' => 'Epson EB-X500',
                'acquisition_year' => 2024,
                'source_of_fund' => 'Komite Sekolah',
                'notes' => 'Lampu mulai berkedip setelah pemakaian lebih dari 2 jam.',
            ]
        );

        $bookshelf = Asset::firstOrCreate(
            ['code' => 'AST-FRN-LIB-01', 'school_id' => $school->id],
            [
                'room_id' => $perpus->id,
                'name' => 'Rak Buku Dua Muka Plat Besi Powder Coating 5 Tingkat',
                'category' => 'mebeler',
                'quantity' => 8,
                'condition' => 'baik',
                'brand_model' => 'Lion Steel LS-200',
                'acquisition_year' => 2025,
                'source_of_fund' => 'BOS Reguler',
                'notes' => 'Rak penataan referensi kurikulum merdeka.',
            ]
        );

        // 3. Maintenance Tickets (Tiket Perbaikan Sarpras)
        if ($reporter) {
            MaintenanceTicket::firstOrCreate(
                ['ticket_number' => 'TKT/2026/0001', 'school_id' => $school->id],
                [
                    'room_id' => $ruangKelas->id,
                    'asset_id' => $projector->id,
                    'reporter_staff_id' => $reporter->id,
                    'issue_description' => 'Lampu proyektor ruang X-PPLG sering padam mendadak setelah 15-20 menit dan indikator filter menyala orange.',
                    'priority' => 'tinggi',
                    'status' => 'in_progress',
                    'cost' => 0,
                ]
            );

            MaintenanceTicket::firstOrCreate(
                ['ticket_number' => 'TKT/2026/0002', 'school_id' => $school->id],
                [
                    'room_id' => $bengkelOto->id,
                    'asset_id' => $balancingMachine->id,
                    'reporter_staff_id' => $reporter->id,
                    'issue_description' => 'Hasil balancing ban sepeda motor & mobil menunjukkan deviasi 5-10 gram dari standar pabrik, butuh kalibrasi vendor.',
                    'priority' => 'sedang',
                    'status' => 'open',
                    'cost' => 0,
                ]
            );

            MaintenanceTicket::firstOrCreate(
                ['ticket_number' => 'TKT/2026/0003', 'school_id' => $school->id],
                [
                    'room_id' => $labRpl->id,
                    'asset_id' => null,
                    'reporter_staff_id' => $reporter->id,
                    'issue_description' => 'AC unit sisi timur mengeluarkan angin tidak dingin dan air menetes ke lantai lab komputer RPL.',
                    'priority' => 'rendah',
                    'status' => 'resolved',
                    'resolved_at' => now()->subDays(5),
                    'resolution_notes' => 'Pipa drainase dibersihkan dan penambahan freon R32 selesai dilakukan oleh CV Mandiri Teknik.',
                    'cost' => 385000,
                ]
            );
        }

        // 4. KataMPI CBT Integration Config
        KatampiConfig::updateOrCreate(
            ['school_id' => $school->id],
            [
                'cbt_url' => 'http://ja-katampi:8787',
                'api_key' => 'katampi-edusis-cbt-secret-token-2026',
                'is_active' => true,
                'last_sync_at' => now()->subHours(2),
                'sync_stats' => [
                    'total_exams' => 12,
                    'total_grades_synced' => 432,
                    'last_exam_synced' => 'Sumatif Harian 2: Algoritma & Struktur Data',
                    'last_batch_count' => 36,
                ],
            ]
        );

        // 5. WhatsApp Gateway Logs
        $std1 = $students->first();
        $std2 = $students->count() > 1 ? $students->get(1) : $std1;

        WhatsAppLog::firstOrCreate(
            ['recipient_phone' => '6281234567890', 'message_type' => 'attendance_alert', 'school_id' => $school->id],
            [
                'student_id' => $std1?->id,
                'recipient_name' => 'Bapak Joko Santoso (Wali Murid)',
                'content' => "Yth. Bapak Joko Santoso, wali dari {$std1?->full_name} (X-PPLG-1),\n\nKami menginformasikan bahwa ananda tercatat TERLAMBAT pada absensi pagi tanggal " . now()->isoFormat('D MMMM Y') . ".\n\nMohon pendampingan agar ananda dapat hadir tepat waktu. Terima kasih.\nSMKN 1 EduSIS",
                'status' => 'sent',
                'sent_at' => now()->subHours(4),
                'error_message' => null,
            ]
        );

        WhatsAppLog::firstOrCreate(
            ['recipient_phone' => '6281987654321', 'message_type' => 'attendance_alert', 'school_id' => $school->id],
            [
                'student_id' => $std2?->id,
                'recipient_name' => 'Ibu Siti Aminah (Wali Murid)',
                'content' => "Yth. Ibu Siti Aminah, wali dari {$std2?->full_name} (X-PPLG-1),\n\nKami menginformasikan bahwa ananda tercatat ALPA (Tanpa Keterangan) pada hari " . now()->isoFormat('D MMMM Y') . ".\n\nMohon segera menghubungi wali kelas untuk konfirmasi. Terima kasih.\nSMKN 1 EduSIS",
                'status' => 'sent',
                'sent_at' => now()->subHours(3),
                'error_message' => null,
            ]
        );

        WhatsAppLog::firstOrCreate(
            ['recipient_phone' => '6285211223344', 'message_type' => 'announcement', 'school_id' => $school->id],
            [
                'student_id' => null,
                'recipient_name' => 'Seluruh Orang Tua / Komite Sekolah',
                'content' => "Pemberitahuan SMKN 1 EduSIS:\n\nPelaksanaan Ujian Penilaian Sumatif Berbasis Komputer (KataMPI CBT) akan dimulai hari Senin depan. Mohon pastikan peserta didik membawa kartu ujian dan menjaga kesehatan.\n\nKepala Sekolah SMKN 1 EduSIS",
                'status' => 'sent',
                'sent_at' => now()->subDays(1),
                'error_message' => null,
            ]
        );

        WhatsAppLog::firstOrCreate(
            ['recipient_phone' => '6287700998877', 'message_type' => 'general', 'school_id' => $school->id],
            [
                'student_id' => $std1?->id,
                'recipient_name' => 'Bapak Joko Santoso',
                'content' => "Yth. Orang Tua {$std1?->full_name},\n\nRekapitulasi rapor tengah semester ganjil tahun ajaran 2026/2027 telah terbit dan dapat diunduh melalui portal EduSIS: https://edusis.smkn1.sch.id/portal/rapor\n\nTerima kasih atas kerja samanya.",
                'status' => 'sent',
                'sent_at' => now()->subDays(2),
                'error_message' => null,
            ]
        );
    }
}
