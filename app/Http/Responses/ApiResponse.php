<?php

namespace App\Http\Responses;

use stdClass;
use Carbon\Carbon;
use ReflectionClass;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

trait ApiResponse {
	protected function successResponse($data, $code, $message = "Success") {
		return response()->json([
            "message" => $message,
            "code" => $code,
            "data" => $data ?? new stdClass()
        ], $code);
	}

	protected function errorResponse($message, $code, $data = null) {
		return response()->json([
            'message' => $message,
            'code' => $code,
            'data' => $data ?? new stdClass()
        ], $code);
	}

    protected function excel($import, $name = null)
    {
        $name = self::getFileName($name, $import);
        return Excel::download($import, $name);
    }

    private static function getFileName($name, $import)
    {
        $name =  $name ? $name : (new ReflectionClass($import))->getShortName();
        $name = Str::slug($name . "_" . Carbon::now()->format('Y_m_d_h_i_s'));
        return $name . ".xlsx";
    }
}
