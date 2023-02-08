<?php

namespace App\Http\Controllers\Admin;

use App\Models\Import;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImportController extends Controller
{
    public function index(Request $request)
    {
        $imports = Import::filterByColumns($request->all())->with('admin')->orderBy('id', 'desc')->paginate();

        return $this->successResponse($imports, 200);
    }

    public function file($filename)
    {
        $file = storage_path("app/public/import_logs/$filename");
        return (new \Illuminate\Http\Response(file_get_contents($file), 200))->header('Content-Type', 'application/json');
    }
}
