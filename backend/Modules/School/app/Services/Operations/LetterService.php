<?php

declare(strict_types=1);

namespace Modules\School\Services\Operations;

use Modules\School\Models\Administration\Letter;

class LetterService
{
    /**
     * Hasilkan saran nomor surat otomatis sesuai klasifikasi kode arsip tata usaha.
     */
    public function generateSuggestedNumber(int $schoolId, string $type = 'outgoing', string $category = 'general'): string
    {
        $year = (int) date('Y');
        $month = (int) date('n');
        $romanMonth = $this->getRomanMonth($month);

        $codeMap = config('school.operations.letter_classification_codes', [
            'invitation' => '005',
            'assignment' => '090',
            'decree' => '421.5',
            'certificate' => '421.3',
            'circular' => '005',
            'general' => '420',
        ]);

        $classificationCode = $codeMap[$category] ?? '420';

        $count = Letter::where('school_id', $schoolId)
            ->whereYear('created_at', $year)
            ->count();

        $prefix = $type === 'outgoing' ? 'TU-OUT' : 'TU-IN';

        return sprintf('%03d/%s/%s/%s/%d', $count + 1, $prefix, $classificationCode, $romanMonth, $year);
    }

    /**
     * Konversi angka bulan kalender ke angka romawi.
     */
    private function getRomanMonth(int $month): string
    {
        $romans = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        return $romans[$month] ?? 'I';
    }

    /**
     * Simpan surat masuk / keluar baru.
     */
    public function createLetter(array $data, int $schoolId): Letter
    {
        return Letter::create(array_merge($data, [
            'school_id' => $schoolId,
            'status' => $data['status'] ?? 'registered',
            'disposition_status' => $data['disposition_status'] ?? 'pending',
        ]));
    }

    /**
     * Catat arahan disposisi pimpinan atas surat.
     */
    public function updateDisposition(Letter $letter, array $data): Letter
    {
        $letter->update($data);

        return $letter;
    }
}
