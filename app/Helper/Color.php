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
            '#EC2500',
            '#ECE100',
            '#EC9800',
            '#9EDE00',
        ];
        return $color[array_rand($color)];
    }
}
