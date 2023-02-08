<?php

namespace App\Helpers;

class Functions
{
    public static function getValidatorMessage($validator)
    {
        $message = "";

        foreach ($validator->messages()->all() as $item => $value) {
            $message .= $message == "" ? $value : "\n$value";
        }

        return $message;
    }

    public static function getValidatorMessageOneLine($validator)
    {
        $message = "";

        foreach ($validator->messages()->all() as $item => $value) {
            $message .= $message == "" ? $value : " $value";
        }

        return $message;
    }

    public static function convertMonthName($month) {
        if ($month == '1') return "ENERO";
        if ($month == '2') return "FEBRERO";
        if ($month == '3') return "MARZO";
        if ($month == '4') return "ABRIL";
        if ($month == '5') return "MAYO";
        if ($month == '6') return "JUNIO";
        if ($month == '7') return "JULIO";
        if ($month == '8') return "AGOSTO";
        if ($month == '9') return "SEPTIEMBRE";
        if ($month == '10') return "OCTUBRE";
        if ($month == '11') return "NOVIEMBRE";
        if ($month == '12') return "DICIEMBRE";
    }

    public static function convertDayName($day) {
        if ($day == '1') return "LUNES";
        if ($day == '2') return "MARTES";
        if ($day == '3') return "MIÉRCOLES";
        if ($day == '4') return "JUEVES";
        if ($day == '5') return "VIERNES";
        if ($day == '6') return "SÁBADO";
        if ($day == '7') return "DOMINGO";
    }

    public static function maskEmail($email) {
        $explodeEmail = explode("@", $email);
        $emailMask    = $explodeEmail[0] ?? "";
        $domain       = $explodeEmail[1] ?? "";

        if (strlen($emailMask) > 4) {
            $emailMask = substr($emailMask, 0, 4) . str_repeat('*', strlen($emailMask) - 3);
        }

        if (strlen($domain) > 3) {
            $explodeDomain = explode(".", $domain);
            $domainLeft = $explodeDomain[0] ?? "";
            $domainRight = $explodeDomain[1] ?? "";

            if (strlen($domainLeft) > 3) {
                $domainMask = substr($domainLeft, 0, 3) . str_repeat('*', strlen($domainLeft) - 3) . "." . $domainRight;
            }
        }

        return $emailMask . "@" . $domainMask ?? null;
    }
}
