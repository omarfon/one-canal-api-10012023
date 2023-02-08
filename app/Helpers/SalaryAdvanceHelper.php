<?php

namespace App\Helpers;

use PDF;
use App\Models\Fee;
use App\Models\User;
use App\Models\Format;
use App\Models\Business;
use App\Models\FeesRange;
use App\Helpers\Functions;
use App\Models\SalaryAdvance;
use Illuminate\Support\Facades\Auth;
use Luecano\NumeroALetras\NumeroALetras;

class SalaryAdvanceHelper
{
    public static function getPeriodName()
    {
        $period_name = Functions::convertMonthName(date('m')) . "-" . date('Y');

        return $period_name;
    }

    public static function getAmountTransfer($amount, $currency_included = true)
    {
        $fees = SalaryAdvanceHelper::getFeesAmount($amount, false);
        $amount = $amount - $fees;

        $amount = number_format($amount, 2, '.', '');

        if ($currency_included) {
            $amount = "S/ " . $amount;
        }

        return $amount;
    }

    public static function getFeesAmount($amount, $currency_included = true, $user = null)
    {
        $user = $user ?? User::whereId(Auth::user()->id)->first();
        $business = Business::whereId($user->business_id)->first();
        $fees_ranges = FeesRange::where('business_id', $business->id)->get();

        $fees = Fee::all();
        $fees_amount = 0;

        foreach ($fees as $key => $fee) {
            switch ($fee->type) {
                case 'FEE':
                    foreach ($fees_ranges as $key => $fee) {
                        if($amount >= $fee->min && $amount <= $fee->max) {
                            $fees_amount += $fee->fee;
                            $fees_amount += ($fee->fee * ($fees->where('type', 'IGV')->first()->value / 100));
                        }
                    }
                    break;

                case 'ITF':
                    $fees_amount += ($amount - $fees_amount) * ($fee->value / 100);
                    break;
            }
        }

        $fees_amount = number_format($fees_amount, 2, '.', '');

        if ($currency_included) {
            $fees_amount = "S/ " . $fees_amount;
        }

        return $fees_amount;
    }

    public static function addCurrency($amount)
    {
        $currency = "S/ ";

        return $currency . number_format($amount, 2, '.', '');
    }

    public static function getAvailableSalary($salary, $reliability)
    {
        $percentage_days = date('d') / date('t');

        $available_salary = $percentage_days * ($reliability / 100) * $salary;

        $available_salary = number_format($available_salary, 2, '.', '');

        return $available_salary;
    }

    public static function availabilityDays()
    {
        $last_day_month = date("t", strtotime(date('Y-m-d')));
        $actual_day = date('d');

        if ($actual_day >= ($last_day_month - 2)) {
            return false;
        }

        return true;
    }

    public static function periodLimitUser($user_id, $period_name)
    {
        if (SalaryAdvance::where('user_id', $user_id)->where('period_name', $period_name)->first()) {
            return false;
        }

        return true;
    }

    public static function generatePdf($user, $type, $return = 'pdf', $salaryAdvance = null)
    {
        $replaceData = [];
        $formatter = new NumeroALetras();
        $format = Format::where('type', $type)->first();

        $headData = [
            "FECHA" => date('d/m/Y')
        ];

        if ($salaryAdvance) {
            $amount = explode(".", $salaryAdvance->amount);
        }

        switch ($type) {
            case 'terms':
                $typeSp = "términos_";
                break;

            case 'contract':
                $typeSp = "contrato_";
                $replaceData = [
                    // User data
                    "NOMBRE_USUARIO" => $user->names . " " . $user->surnames,
                    "TIPO_DOCUMENTO" => $user->document_type,
                    "NUMERO_DOCUMENTO" => $user->document_number,
                    "TELEFONO" => $user->phone,
                    "DIRECCION" => $user->address,
                    "CORREO" => $user->email,
                    "SALARIO" => "S/ " . $user->salary,
                    // Business data
                    "RUC_EMPRESA" => $user->business->ruc,
                    "NOMBRE_EMPRESA" => $user->business->name,
                    // Salary advance data
                    "FECHA_SOLICITUD" => $salaryAdvance ? date("d/m/Y", strtotime($salaryAdvance->created_at)) : "",
                    "DIA" => $salaryAdvance ? date("d", strtotime($salaryAdvance->created_at)) : "",
                    "MES_LETRAS" => $salaryAdvance ? Functions::convertMonthName(date("m", strtotime($salaryAdvance->created_at))) : "",
                    "AÑO" => $salaryAdvance ? date("Y", strtotime($salaryAdvance->created_at)) : "",
                    "MONTO_ADELANTO" => $salaryAdvance ? "S/ " . $salaryAdvance->amount : "",
                    "MONTO_LETRAS_DEPOSITO" => $salaryAdvance ? strtolower($formatter->toWords($amount[0], 2)) . " y " . $amount[1] . "/100 Soles" : "",
                    "COMISIONES_ADELANTO" => $salaryAdvance ? SalaryAdvanceHelper::getFeesAmount($salaryAdvance->amount, true, $salaryAdvance->user) : "",
                    "PERIODO" => $salaryAdvance ? $salaryAdvance->period_name : "",
                    "DEVOLUCION_ADELANTO" => $salaryAdvance ? date('t/m/Y', strtotime($salaryAdvance->created_at)) : "",
                    "BANCO_DEPOSITO" => $salaryAdvance ? $salaryAdvance->account->bank->name : "",
                    "CUENTA_DEPOSITO" => $salaryAdvance ? $salaryAdvance->account->number : "",
                    "CCI_DEPOSITO" => $salaryAdvance ? $salaryAdvance->account->cci : "",
                ];
                break;

            case 'advance':
                $typeSp = "";
                $replaceData = [
                    // User data
                    "NOMBRE_USUARIO" => $user->names . " " . $user->surnames,
                    "TIPO_DOCUMENTO" => $user->document_type,
                    "NUMERO_DOCUMENTO" => $user->document_number,
                    "TELEFONO" => $user->phone,
                    "DIRECCION" => $user->address,
                    "CORREO" => $user->email,
                    "SALARIO" => "S/ " . $user->salary,
                    // Business data
                    "RUC_EMPRESA" => $user->business->ruc,
                    "NOMBRE_EMPRESA" => $user->business->name,
                    // Salary advance data
                    "FECHA_SOLICITUD" => $salaryAdvance ? date("d/m/Y", strtotime($salaryAdvance->created_at)) : "",
                    "DIA" => $salaryAdvance ? date("d", strtotime($salaryAdvance->created_at)) : "",
                    "MES_LETRAS" => $salaryAdvance ? Functions::convertMonthName(date("m", strtotime($salaryAdvance->created_at))) : "",
                    "AÑO" => $salaryAdvance ? date("Y", strtotime($salaryAdvance->created_at)) : "",
                    "MONTO_ADELANTO" => $salaryAdvance ? "S/ " . $salaryAdvance->amount : "",
                    "MONTO_LETRAS_DEPOSITO" => $salaryAdvance ? strtolower($formatter->toWords($amount[0], 2)) . " y " . $amount[1] . "/100 Soles" : "",
                    "COMISIONES_ADELANTO" => $salaryAdvance ? SalaryAdvanceHelper::getFeesAmount($salaryAdvance->amount, true, $salaryAdvance->user) : "",
                    "PERIODO" => $salaryAdvance ? $salaryAdvance->period_name : "",
                    "DEVOLUCION_ADELANTO" => $salaryAdvance ? date('t/m/Y', strtotime($salaryAdvance->created_at)) : "",
                    "BANCO_DEPOSITO" => $salaryAdvance ? $salaryAdvance->account->bank->name : "",
                    "CUENTA_DEPOSITO" => $salaryAdvance ? $salaryAdvance->account->number : "",
                    "CCI_DEPOSITO" => $salaryAdvance ? $salaryAdvance->account->cci : "",
                ];
                break;
        }

        $head = self::replace($headData, $format->head);
        $body = self::replace($replaceData, $format->body);

        $fileName = strtolower($typeSp . Functions::convertMonthName(date("m")) . "_" . date('Y') . "_" . str_replace(" ", "_", $user->names . "_" . $user->surnames) . ".pdf");

        $basePath = "app/";
        $pdfName = "public/" . date('Y_m') . "/" . $type . "/" . $fileName;

        if(file_exists(storage_path($basePath . $pdfName))){
            unlink(storage_path($basePath . $pdfName));
        }

        if ($return == 'html') {
            return $body;
        }

        $pdf = PDF::loadHTML($head . $body)
        ->setOption('margin-top', '1.2cm')
        ->setOption('margin-right', '1.2cm')
        ->setOption('margin-bottom', '1.2cm')
        ->setOption('margin-left', '0.6cm')
        ->save(storage_path($basePath . $pdfName));

        return storage_path($basePath . $pdfName);
    }

    private static function replace($data, $body)
    {
        foreach ($data as $key => $item) {
            $body = str_replace("[" . $key . "]", $item, $body);
        }

        return $body;
    }
}
