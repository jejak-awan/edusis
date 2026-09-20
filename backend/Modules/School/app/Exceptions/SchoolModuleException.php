<?php

declare(strict_types=1);

namespace Modules\School\Exceptions;

use Exception;

/**
 * Representasi Exception domain terstandarisasi untuk modul School.
 * Menyediakan kode status HTTP dan format pesan kesalahan yang konsisten.
 */
class SchoolModuleException extends Exception
{
    /**
     * Exception saat entitas data tidak ditemukan (HTTP 404).
     */
    public static function notFound(string $entity, int|string $id): self
    {
        return new self("Entitas {$entity} dengan ID {$id} tidak ditemukan.", 404);
    }

    /**
     * Exception saat akses ditolak / otorisasi gagal (HTTP 403).
     */
    public static function unauthorized(string $action = ''): self
    {
        $message = 'Anda tidak memiliki hak akses untuk melakukan tindakan ini.';
        if ($action !== '') {
            $message = "Anda tidak memiliki hak akses untuk: {$action}.";
        }

        return new self($message, 403);
    }

    /**
     * Exception saat validasi aturan domain gagal (HTTP 422).
     */
    public static function validation(string $message): self
    {
        return new self($message, 422);
    }

    /**
     * Exception saat terjadi pelanggaran aturan bisnis (HTTP 409).
     * Contoh: pemilih mencoba mencoblos dua kali, jadwal bentrok pada ruangan yang sama.
     */
    public static function businessRule(string $message): self
    {
        return new self($message, 409);
    }

    /**
     * Exception saat terjadi kegagalan integritas basis data (HTTP 500).
     */
    public static function dataIntegrity(string $message): self
    {
        return new self($message, 500);
    }
}
