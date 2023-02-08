<?php

namespace App\Constants;

class User
{
    const DNI = 'DNI';
    const CE = 'CE';
    const PAS = 'PAS';

    public static $document_type = [
        self::DNI,
        self::CE,
        self::PAS
    ];
}
