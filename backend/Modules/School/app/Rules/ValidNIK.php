<?php

declare(strict_types=1);

namespace Modules\School\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validasi Nomor Induk Kependudukan (NIK) Indonesia sesuai standar Dukcapil.
 * Memverifikasi panjang 16 digit, kode provinsi, kota, kecamatan, dan tanggal lahir.
 */
class ValidNIK implements ValidationRule
{
    /**
     * Jalankan validasi NIK.
     *
     * @param  Closure(string): void  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        if (! is_string($value) || ! preg_match('/^\d{16}$/', $value)) {
            $fail('Nomor Induk Kependudukan (NIK) harus berupa 16 digit angka.');

            return;
        }

        if (! $this->isValidNIKStructure($value)) {
            $fail('Nomor Induk Kependudukan (NIK) tidak valid.');
        }
    }

    /**
     * Verifikasi struktur wilayah dan tanggal lahir pada NIK.
     */
    private function isValidNIKStructure(string $nik): bool
    {
        $province = (int) substr($nik, 0, 2);
        $regency = (int) substr($nik, 2, 2);
        $district = (int) substr($nik, 4, 2);
        $birthDate = substr($nik, 6, 6);

        if ($province < 1 || $province > 94) {
            return false;
        }

        if ($regency < 1 || $regency > 99) {
            return false;
        }

        if ($district < 1 || $district > 99) {
            return false;
        }

        $rawDay = (int) substr($birthDate, 0, 2);
        $month = (int) substr($birthDate, 2, 2);

        // Pada NIK, tanggal lahir perempuan ditambah 40 (1-31 untuk laki-laki, 41-71 untuk perempuan)
        $day = $rawDay > 40 ? $rawDay - 40 : $rawDay;

        if ($day < 1 || $day > 31) {
            return false;
        }

        if ($month < 1 || $month > 12) {
            return false;
        }

        return true;
    }
}
