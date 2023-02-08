<?php

namespace App\Http\Controllers\Admin;

use App\Models\Business;
use App\Models\FeesRange;
use App\Constants\Message;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BusinessFeesRangeController extends Controller
{
    public function index($business_id)
    {
        $fees_ranges = FeesRange::where('business_id', $business_id)->paginate(50);

        return $this->successResponse($fees_ranges, 200);
    }

    public function store(Request $request, $business_id)
    {
        $data = $request->all();
        $data['business_id'] = $business_id;

        $fees_ranges_business = FeesRange::where('business_id', $business_id);

        if ($fees_ranges_business->count() > 0) {
            $valid_min_limit = $fees_ranges_business->where('max', ($data['min'] - 0.01))->count();

            if (!$valid_min_limit) {
                return $this->errorResponse(Message::INVALID_MIN_LIMIT, 409);
            }

            $valid_limits = FeesRange::where('business_id', $business_id)
            ->where(function ($query) use ($data) {
                $query->where('min', '>=', $data['min'])
                      ->orWhere('max', '>=', $data['max']);
            })
            ->count();

            if($valid_limits) {
                return $this->errorResponse(Message::INVALID_LIMITS, 409);
            }
        } else {
            if ($data['min'] != 0) {
                return $this->errorResponse(Message::INVALID_0_LIMIT, 409);
            }
        }

        $fees_range = FeesRange::create($data);

        return $this->successResponse([
            'fees_range' => $fees_range
        ], 200);
    }

    public function update($business_id, $id, Request $request)
    {
        $data = $request->all();

        $fee_range = FeesRange::where('id', $id)->where('business_id', $business_id)->first();

        $fee_range->update([
            "fee" => $data['fee']
        ]);

        return $this->successResponse([
            'fee_range' => $fee_range
        ], 200);
    }

    public function delete($business_id, $id)
    {
        $fee_range = Business::where('id', $business_id)->with(['fees_ranges' => function ($query) {
            $query->orderBy('max', 'desc');
        }])->first()->fees_ranges;

        if ($fee_range[0]->id != $id) {
            return $this->errorResponse(Message::INVALID_MAX_LIMIT, 409);
        }

        FeesRange::where('id', $fee_range[0]->id)->delete();

        return $this->successResponse([
            'fee_range' => $fee_range
        ], 200);
    }
}
