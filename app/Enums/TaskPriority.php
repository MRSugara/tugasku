<?php

namespace App\Enums;

/**
 * Prioritas tugas, dari rendah ke tinggi.
 */
enum TaskPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Rendah',
            self::Medium => 'Sedang',
            self::High => 'Tinggi',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Low => 'bg-gray-100 text-gray-600',
            self::Medium => 'bg-yellow-100 text-yellow-800',
            self::High => 'bg-red-100 text-red-700',
        };
    }
}
