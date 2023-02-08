<?php

namespace App\Http\Controllers\Admin;

use App\Models\Statistic;
use App\Http\Controllers\Controller;

class StatisticsController extends Controller
{
    public function get($chart)
    {
        $data = [];

        $last_7_days = date('Y-m-d', strtotime('-6 days'));

        switch ($chart) {
            case 'login':
                $items =
                $data['data'] = Statistic::where('type', 'login')->where('date', '>=', $last_7_days)->orderBy('date', 'asc')->get(['date', 'tag', 'value']);
                $data['max'] = $data['data']->max('value') * 1.05;
                break;

            case 'salary_advances_amounts':
                $data['data'] = Statistic::where('type', 'salary-advances-amounts')->where('date', '>=', $last_7_days)->orderBy('date', 'asc')->get(['date', 'tag', 'value']);
                $data['max'] = $data['data']->max('value') * 1.05;
                $data['sum'] = $data['data']->sum('value');
                break;

            case 'salary_advances_numbers':
                $data['data'] = Statistic::where('type', 'salary-advances-numbers')->where('date', '>=', $last_7_days)->orderBy('date', 'asc')->get(['date', 'tag', 'value']);
                $data['max'] = $data['data']->max('value') * 1.05;
                $data['sum'] = $data['data']->sum('value');
                break;

            default:
                return $this->errorResponse("Gráfico no soportado", 409);
                break;
        }

        return $this->successResponse($data, 200);
    }
}
