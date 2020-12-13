<?php

namespace App\Helper;

class Color
{
    public static function makeRandomColor()
    {
        $color = [
            'blue',
            'red',
            'orange',
            'green',
            'brown',
            'yellow',
            'pink',
            'navy',
            'black'
        ];
        return $color[array_rand($color)];
    }
}
