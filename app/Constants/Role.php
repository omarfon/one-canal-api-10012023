<?php

namespace App\Constants;

class Role
{
    const ADMIN = 'admin';
    const EMPLOYEE = 'employee';

    public static $role = [
        self::ADMIN,
        self::EMPLOYEE
    ];
}
