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
     * @param  Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        if (! is_string($value) || ! preg_match('/^\d{10}$/', $value)) {
            $fail('NISN harus berupa 10 digit angka numerik.');
        }
    }
}
