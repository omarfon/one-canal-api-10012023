<?php

namespace App\Constants;

class Fee
{
    const FEE = 'FEE';
    const IGV = 'IGV';
    const ITF = 'ITF';

    public static $types = [
        self::FEE,
        self::IGV,
        self::ITF
    ];
}
