<?php

namespace App\Http\Controllers\Admin;

use App\Models\Format;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DocumentController extends Controller
{
    public function get($type)
    {
        $format = Format::where('type', $type)->first();

        if (!$format) {
            return $this->errorResponse("Formato no encontrado", 422);
        }

        return $this->successResponse($format, 200);
    }

    public function update(Request $request, $type)
    {
        $data = $request->only(['body']);

        if ($data['body'] == '') {
            return $this->errorResponse("El texto no puede estar vacíos", 422);
        }

        $format = Format::where('type', $type)->first();

        if (!$format) {
            return $this->errorResponse("Formato no encontrado", 422);
        }

        $format->update($data);

        return $this->successResponse([
            'format' => $format->fresh()
        ], 200);
    }
}
