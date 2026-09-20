<?php

declare(strict_types=1);

namespace Modules\School\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validasi dan normalisasi nomor telepon seluler Indonesia.
 * Mendukung format: 08xxxxxxxxxx, +628xxxxxxxxx, atau 628xxxxxxxxx.
 */
class ValidPhoneNumber implements ValidationRule
{
    /**
     * Jalankan validasi nomor telepon.
     *
     * @param  Closure(string): void  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        if (! is_string($value)) {
            $fail('Nomor telepon harus berupa teks angka yang valid.');

            return;
        }

        $cleanNumber = preg_replace('/[^0-9+]/', '', $value);

        if (! $this->isValidIndonesianPhoneNumber((string) $cleanNumber)) {
            $fail('Nomor telepon tidak valid. Gunakan format standar Indonesia (contoh: 08123456789 atau +628123456789).');
        }
    }

    /**
     * Verifikasi pola nomor seluler operator telekomunikasi Indonesia.
     */
    private function isValidIndonesianPhoneNumber(string $number): bool
    {
        $patterns = [
            '/^08[1-9][0-9]{6,11}$/',     // 08xxxxxxxxxx (10-13 digit)
            '/^628[1-9][0-9]{6,11}$/',    // 628xxxxxxxxx (11-14 digit)
            '/^\+628[1-9][0-9]{6,11}$/',  // +628xxxxxxxxx (12-15 digit)
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $number)) {
                return true;
            }
        }

        return false;
    }
}
