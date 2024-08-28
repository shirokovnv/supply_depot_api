<?php

namespace App\Enums;

enum DocumentType: string
{
    case Income = 'income'; // приход
    case Outcome = 'outcome'; // расход
    case Inventory = 'inventory'; // инвентаризация

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
