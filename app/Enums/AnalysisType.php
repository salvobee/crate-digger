<?php

namespace App\Enums;

enum AnalysisType: string
{
    case FETCH_INVENTORY = 'fetch-inventory';
    case ANALYZE_INVENTORY = 'analyze-inventory';

    static function values(): array
    {
        return collect(self::cases())->map(fn (\UnitEnum $type) => $type->value)->toArray();
    }

}
