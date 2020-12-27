<?php

namespace App\Helper;

class Color
{
    /**
     * @return string
     * Cores dos gráficos (em grupos)
     */
    public static function makeRandomColor()
    {
        $color = [

            '#63E1A8',
            '#FFCE7E',
            '#FF8F60',
            '#E66160',
            '#73DBE7',
            '#8B97DA',
            '#E8B0CC',
            '#D0D4CC',
            '#F0CBAD',
            '#95CDC0'

        ];
        return $color[array_rand($color)];
    }
}
