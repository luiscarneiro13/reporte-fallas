<?php

namespace App\Helpers;

class Operations
{
    static function roundUp($number)
    {
        return ceil($number * 100) / 100;
    }
}
