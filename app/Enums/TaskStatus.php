<?php

namespace App\Enums;

/**
 * Status tugas. Nilainya hanya boleh salah satu dari tiga ini.
 */
enum TaskStatus: string
{
    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Done = 'done';

    /** Teks yang tampil ke pengguna. */
    public function label(): string
    {
        return match ($this) {
            self::Todo => 'Belum dikerjakan',
            self::InProgress => 'Sedang dikerjakan',
            self::Done => 'Selesai',
        };
    }

    /** Warna badge (kelas Tailwind). */
    public function color(): string
    {
        return match ($this) {
            self::Todo => 'bg-gray-100 text-gray-700',
            self::InProgress => 'bg-blue-100 text-blue-700',
            self::Done => 'bg-green-100 text-green-700',
        };
    }

    /** Status berikutnya saat tombol "Lanjut" ditekan: belum → sedang → selesai. */
    public function next(): ?self
    {
        return match ($this) {
            self::Todo => self::InProgress,
            self::InProgress => self::Done,
            self::Done => null,
        };
    }
}
