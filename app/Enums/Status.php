<?php

namespace App\Enums;

enum Status: string
{
    case Inactive = 'Inactive';
    case Active = 'Active';

    /**
     * Determine whether the status is active.
     */
    public function isActive(): bool
    {
        return $this === self::Active;
    }

    

    /**
     * Get the backing values of every case.
     *
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}