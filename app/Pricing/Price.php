<?php

namespace App\Pricing;

use Illuminate\Support\Facades\DB;

class Price
{
    public static function symbol(string $symbol): float
    {
        return (float)DB::table('pricing')
            ->where('symbol', '=', $symbol)
            ->value('price');
    }

    public static function solToGmt($amount): float|int
    {
        return $amount * self::symbol('SOL') / self::symbol('GMT');
    }

    public static function gmtToSol(float|int $amount): float|int
    {
        return $amount * self::symbol('GMT') / self::symbol('SOL');
    }

    public static function solToGst(float $amount): float
    {
        return $amount * self::symbol('SOL') / self::symbol('GST');
    }

    public static function floorSol(int $quality = 1, int $level = 30): float|int
    {
        return DB::table('sneakers')
                ->where('quality', $quality)
                ->where('level', $level)
                ->where('updated_at', '>', now()->subHours(24))
                ->orderBy('price')
                ->limit(1)
                ->value('price') / 1000000;
    }

    public static function floorGmt(int $quality = 1, int $level = 30): float|int
    {
        return DB::table('sneakers')
                ->where('quality', $quality)
                ->where('level', $level)
                ->where('updated_at', '>', now()->subHours(24))
                ->orderBy('price')
                ->limit(1)
                ->value('price') / 100;
    }

    public static function gstToSol($amount): float|int
    {
        return $amount * self::symbol('GST') / self::symbol('SOL');
    }

    public static function aud(): float
    {
        return (1 / self::symbol('AUD') + 0.01) * 1.006;
    }

    public static function busd(): float
    {
        return self::symbol('BUSD') * (1 - 0.0015);
    }

    public static function gstToGmt($amount): float|int
    {
        return $amount * self::symbol('GST') / self::symbol('GMT');
    }
}
