<?php

declare(strict_types=1);

namespace Modules\School\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validasi Nomor Induk Siswa Nasional (NISN) standar Pusdatin Kemendikbudristek.
 * Memverifikasi panjang 10 digit numerik dan algoritma checksum.
 */
class ValidNISN implements ValidationRule
{
    /**
     * Jalankan validasi NISN.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): void  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        if (! is_string($value) || ! preg_match('/^\d{10}$/', $value)) {
            $fail('NISN harus berupa 10 digit angka.');
            return;
        }

        if (! $this->isValidNISNChecksum($value)) {
            $fail('Format angka kontrol (checksum) NISN tidak valid.');
        }
    }

    /**
     * Verifikasi bobot checksum 10-digit NISN.
     */
    private function isValidNISNChecksum(string $nisn): bool
    {
        $digits = str_split($nisn);
        $sum = 0;

        for ($i = 0; $i < 8; $i++) {
            $sum += ((int) $digits[$i]) * (9 - $i);
        }

        $checkDigit = $sum % 10;
        $expectedCheckDigit = ((int) $digits[8]) * 10 + ((int) $digits[9]);

        return $checkDigit === ($expectedCheckDigit % 10);
    }
}
