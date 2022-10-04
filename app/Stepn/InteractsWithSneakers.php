<?php

namespace App\Stepn;

use App\Pricing\Price;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait InteractsWithSneakers
{
    public function getSneakers(): array
    {
        // Use gem lv2
        $gemPrice = Price::symbol('COMFORT2');
        $gemBoost = 8;
        $gemBoostPercentage = 0.7;
        $gmtPrice = Price::symbol('GMT');
        $solPrice = Price::symbol('SOL');

        $sneakers = DB::table('sneakers')
            ->selectRaw(sprintf(
                'otd, 
                level, 
                quality, 
                comfort,
                luck,
                efficiency,
                resilience,
                comfort_base,
                luck_base,
                efficiency_base,
                resilience_base,
                type, 
                (comfort+(comfort_base*%f+%d)*comfort_socket) as comfort_max, 
                price / 1000000 as price_sol, 
                IF( level<30, (price + (%3$f*1000000*comfort_socket))/1000000 + %4$f, (price + (%3$f*1000000*comfort_socket))/1000000 ) as price_max,
                (CASE
                    WHEN comfort < 1000 THEN 1.18
                    WHEN comfort >= 1000 AND comfort < 2000 THEN 1.67
                    WHEN comfort >= 2000 AND comfort < 3000 THEN 2.24
                    WHEN comfort >= 3000 AND comfort < 4000 THEN 2.16
                    WHEN comfort >= 4000 AND comfort < 5000 THEN 3.29
                    ELSE 3.71
                END)* %5$f / %6$f * 2 as daily_earn_sol
                ',
                $gemBoostPercentage,
                $gemBoost,
                $gemPrice,
                (new Level(29))->getTotalSol(),
                $gmtPrice,
                $solPrice
            ))
            ->where('otd', '>', 0)
            ->where('comfort', '>=', 280)
            ->where('level', '=', 30)
            ->orderByDesc('updated_at')
            ->orderByDesc('daily_earn_sol')
            ->orderByDesc('comfort_max')
            ->orderBy('price_max')
            ->get();

        $sneakers->map(function($sneaker) {
            $hp = new HealthPoint(
                78,
                $sneaker->quality,
                2
            );

            // 78 is HP to restore (best price)
            // 0.15 is %HP reduce for 1 Energy
            // 2 is minimum Energy spent daily
            // 0.2 is 20% of GST cost to repair sneakers after running
            $decay = (new HealthPointDecay($sneaker->comfort, $sneaker->quality))->getDecaySpeed();
            $sneaker->daily_expense_sol = $hp->getTotalInSol() / (78/($decay*2)) + 0.2 * $sneaker->daily_earn_sol;
            $sneaker->daily_roi = ($sneaker->daily_earn_sol - $sneaker->daily_expense_sol) / $sneaker->price_sol * 100;
            $sneaker->payback_period = Carbon::now()->addDays(100/$sneaker->daily_roi)->diffForHumans();
            $sneaker->apy = $sneaker->daily_roi * 365;
            $sneaker->daily_earn_gmt = Price::solToGmt($sneaker->daily_earn_sol);
            $sneaker->daily_expense_gmt = Price::solToGmt($sneaker->daily_expense_sol);
        });

        return $sneakers
            ->sortByDesc(function($sneaker){
                return $sneaker->daily_roi;
            })
            ->values()
            ->toArray();
    }
}