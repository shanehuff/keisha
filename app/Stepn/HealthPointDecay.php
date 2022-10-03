<?php

namespace App\Stepn;

class HealthPointDecay
{
    protected array $decay = [
        // type (1: common...)
        // range: int|array
        // hp lost
        [
            1,
            1,
            0.75
        ],
        [
            1,
            2,
            0.56
        ],
        [
            1,
            3,
            0.47
        ],
        [
            1,
            4,
            0.42
        ],
        [
            1,
            5,
            0.38
        ],
        [
            1,
            6,
            0.36
        ],
        [
            1,
            7,
            0.33
        ],
        [
            1,
            8,
            0.32
        ],
        [
            1,
            9,
            0.3
        ],
        [
            1,
            10,
            0.29
        ],
        [
            1,
            11,
            0.28
        ],
        [
            1,
            12,
            0.27
        ],
        [
            1,
            13,
            0.26
        ],
        [
            1,
            14,
            0.25
        ],
        [
            1,
            [15, 16],
            0.24
        ],
        [
            1,
            [17, 18],
            0.23
        ],
        [
            1,
            [19, 20],
            0.22
        ],
        [
            1,
            [21, 22],
            0.21
        ],
        [
            1,
            [23, 25],
            0.2
        ],
        [
            1,
            [26, 29],
            0.19
        ],
        [
            1,
            [30, 33],
            0.18
        ],
        [
            1,
            [34, 38],
            0.17
        ],
        [
            1,
            [39, 44],
            0.16
        ],
        [
            1,
            [45, 52],
            0.15
        ],
        [
            1,
            [53, 62],
            0.14
        ],
        [
            1,
            [63, 75],
            0.13
        ],
        [
            1,
            [76, 92],
            0.12
        ],
        [
            1,
            [93, 114],
            0.11
        ],
        [
            1,
            [115, 9999],
            0.1
        ],
        [
            2,
            8,
            0.33
        ],
        [
            2,
            9,
            0.32
        ],
        [
            2,
            10,
            0.3
        ],
        [
            2,
            11,
            0.29
        ],
        [
            2,
            12,
            0.28
        ],
        [
            2,
            13,
            0.27
        ],
        [
            2,
            14,
            0.26
        ],
        [
            2,
            [15, 16],
            0.25
        ],
        [
            2,
            17,
            0.24
        ],
        [
            2,
            [18, 19],
            0.23
        ],
        [
            2,
            [20, 21],
            0.22
        ],
        [
            2,
            [22, 24],
            0.21
        ],
        [
            2,
            [25, 27],
            0.2
        ],
        [
            2,
            [28, 30],
            0.19
        ],
        [
            2,
            [31, 35],
            0.18
        ],
        [
            2,
            [36, 40],
            0.17
        ],
        [
            2,
            [41, 46],
            0.16
        ],
        [
            2,
            [47, 54],
            0.15
        ],
        [
            2,
            [55, 64],
            0.14
        ],
        [
            2,
            [65, 76],
            0.13
        ],
        [
            2,
            [77, 92],
            0.12
        ],
        [
            2,
            [93, 114],
            0.11
        ],
        [
            2,
            [115, 144],
            0.1
        ],
        [
            2,
            [145, 186],
            0.09
        ],
        [
            2,
            [187, 9999],
            0.08
        ],
    ];

    protected float $comfort = 1;

    protected int $quality = 1;

    public function __construct(float $comfort = 1, int $quality = 1)
    {
        $this->comfort = $comfort;
        $this->quality = $quality;
    }

    public function getDecaySpeed()
    {
        $found = collect($this->decay)
            ->filter(function ($item) {
                if ($this->quality === $item[0]) {
                    if (is_numeric($item[1])) {
                        return floor($item[1]) === floor($this->comfort);
                    }

                    return $item[1][0] <= $this->comfort && $this->comfort <= $item[1][1];
                }

                return false;
            })
            ->first();

        if ($found && is_array($found)) {
            return $found[2];
        }

        return null;
    }
}