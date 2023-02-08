<?php

namespace App\Http\Controllers\Admin;

use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class ActivityController extends Controller
{
    public function me()
    {
        $data = Activity::where('user_id', Auth::user()->id)->limit(10)->orderBy('id', 'desc')->paginate(8);

        return $this->successResponse($data, 200);
    }
}
