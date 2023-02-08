<?php

namespace App\Http\Controllers\General;

use App\Helpers\SalaryAdvanceHelper;
use PDF;
use App\Models\Format;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SalaryAdvance;

class FormatController extends Controller
{
    public function getFormatHtml(Request $request)
    {
        $type = request()->type;

        if (!$type) {
            return $this->errorResponse("Debe agregar una variable type", 422);
        }

        $format = Format::where('type', $type)->first();

        if (!$format) {
            return $this->errorResponse("Formato inválido, los formatos válidos son: terms, contract, advance", 422);
        }

        return $this->successResponse($format->body, 200);
    }

    public function getFormatPdf(Request $request)
    {
        $type = request()->type;

        if (!$type) {
            return $this->errorResponse("Debe agregar una variable type", 422);
        }

        $format = Format::where('type', $type)->first();

        if (!$format) {
            return $this->errorResponse("Formato inválido, los formatos válidos son: terms, contract, advance", 422);
        }

        $basePath = "app/";
        $pdfName = "public/" . date('Y_m') . "/files/archivo_" . date('Y_m_d_H_i_s')  . ".pdf";

        if(file_exists(storage_path($basePath . $pdfName))){
            unlink(storage_path($basePath . $pdfName));
        }

        $headData = [
            "DATE" => date('d-m-Y')
        ];

        $head = str_replace("[FECHA]", date('d/m/Y'), $format->head);

        $pdf = PDF::loadHTML($head . $format->body)
        ->setOption('margin-top', '1.2cm')
        ->setOption('margin-right', '1.2cm')
        ->setOption('margin-bottom', '1.2cm')
        ->setOption('margin-left', '0.6cm');

        if ($request->salary_advance_id) {
            $salaryAdvance = SalaryAdvance::whereId($request->salary_advance_id)->first();
        }

        return SalaryAdvanceHelper::generatePdf($salaryAdvance->user, $type, 'pdf', $salaryAdvance);

        return $pdf->download('invoice.pdf');
    }
}
