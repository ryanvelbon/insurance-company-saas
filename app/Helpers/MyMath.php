<?php

namespace App\Helpers;

class MyMath
{
    /**
     *
     * Rounds up a value to the specified degree of accuracy.
     *
     * Only works with numeric values greater than 0.
     *
     * @param  num   $x   number to be rounded (original value)
     * @param  int   $sf  significant figures (degree of accuracy)
     * @return int
     */
    public static function roundup($x, $sf = 1): int {

        $rf = 10**(floor(log10($x))-($sf-1)); // rounding factor

        return ceil($x/$rf)*$rf;
    }
}