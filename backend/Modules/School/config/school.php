<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Identitas Modul & Lisensi
    |--------------------------------------------------------------------------
    */
    'name' => 'School',
    'slug' => 'school',
    'license_tier' => 'pro',

    /*
    |--------------------------------------------------------------------------
    | Strategi Multi-Tenancy
    |--------------------------------------------------------------------------
    */
    'tenancy' => [
        'header_key' => 'X-School-ID',
        'default_school_id' => 1,
        'auto_scope_models' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Ambang Batas Poin Disiplin Siswa (SISKA Engine)
    |--------------------------------------------------------------------------
    */
    'discipline' => [
        'sp1_threshold' => 30,  // Peringatan I & Teguran Wali Kelas
        'sp2_threshold' => 50,  // Peringatan II & Panggilan Orang Tua
        'sp3_threshold' => 100, // Peringatan III & Konferensi Kasus / Skorsing
    ],

    /*
    |--------------------------------------------------------------------------
    | Mesin Pemilihan Digital (E-Voting Engine)
    |--------------------------------------------------------------------------
    */
    'voting' => [
        'token_length' => 8,
        'token_charset' => '23456789ABCDEFGHJKLMNPQRSTUVWXYZ', // Tanpa karakter ambigu (0, 1, I, O)
        'kiosk_session_timeout' => 120, // Detik sebelum reset otomatis bilik suara
        'allow_abstain' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Toleransi Presensi Harian
    |--------------------------------------------------------------------------
    */
    'attendance' => [
        'late_tolerance_minutes' => 15,
        'default_check_in_start' => '06:30',
        'default_check_in_end' => '07:15',
        'auto_notify_parents_on_absent' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Format Penomoran Surat Dinas (Tata Usaha)
    |--------------------------------------------------------------------------
    */
    'letters' => [
        'number_format' => '{nomor}/{kode}/{instansi}/{bulan}/{tahun}',
        'reset_counter_annually' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Strategi Penyimpanan Berkas (Storage & Media)
    |--------------------------------------------------------------------------
    */
    'storage' => [
        'public_disk' => 'public',
        'private_disk' => 'local',
        'private_root' => 'private',
    ],

    /*
    |--------------------------------------------------------------------------
    | Sub-Fitur yang Tersedia
    |--------------------------------------------------------------------------
    */
    'features' => [
        'academic' => true,
        'students' => true,
        'staff' => true,
        'attendance' => true,
        'grades' => true,
        'counseling' => true,
        'discipline' => true,
        'letters' => true,
        'ppdb' => true,
        'facilities' => true,
        'voting' => true,
        'katampi' => true,
        'whatsapp' => true,
    ],
];
