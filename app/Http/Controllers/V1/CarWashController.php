<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\CarWash;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CarWashController extends Controller
{
    use ApiResponseTrait;

    public function createCarWash(Request $request)
    {
        $request->validate([
            'car_size' => 'required|in:small,large',
            'wash_type' => 'required|in:interior_exterior,exterior,interior',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_time' => 'required|date_format:H:i',
            'time_period' => 'required|exists:car_wash_periods,period_key,is_active,1',
            'location_id' => 'nullable|exists:user_locations,id',
        ]);

        $cost = $this->calculateCost($request->car_size, $request->wash_type);

        $carWash = CarWash::create([
            'user_id' => $request->user()->id,
            'car_size' => $request->car_size,
            'wash_type' => $request->wash_type,
            'scheduled_date' => $request->scheduled_date,
            'scheduled_time' => $request->scheduled_time,
            'time_period' => $request->time_period,
            'location_id' => $request->location_id,
            'cost' => $cost,
            'status' => 'pending',
        ]);

        return $this->successResponse($carWash->load('location'), __('messages.car_wash.created'));
    }

    public function getMyCarWashes(Request $request)
    {
        $status = $request->query('status', 'all');

        $query = CarWash::where('user_id', $request->user()->id)
            ->with('location');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $carWashes = $query->latest()->get();

        return $this->successResponse($carWashes, __('messages.car_wash.appointments_loaded'));
    }

    public function getCarWash(Request $request, $id)
    {
        $carWash = CarWash::where('user_id', $request->user()->id)
            ->with('location')
            ->findOrFail($id);

        return $this->successResponse($carWash, __('messages.car_wash.loaded'));
    }

    public function getAvailableDates(Request $request)

    {
        $dates = [];
        
        for ($i = 0; $i < 7; $i++) {
            $date = now()->addDays($i);
            $dayName = $date->format('l'); // Sunday, Monday, etc.
            
            $dates[] = [
                'date' => $date->format('Y-m-d'),
                'day_name' => __("messages.car_wash.days.{$dayName}"),
                'day_number' => $date->format('d'),
                'day_short' => __("messages.car_wash.days_short.{$dayName}"),
            ];
        }
        
        return $this->successResponse($dates, __('messages.car_wash.dates_loaded'));
    }

    public function getTimePeriods(Request $request)
    {
        $periodsData = \App\Models\CarWashPeriod::where('is_active', true)->get();
        
        $periods = [];
        foreach ($periodsData as $data) {
            $periods[] = [
                'period' => $data->period_key,
                'name' => __("messages.car_wash.periods.{$data->period_key}"),
                'time_range' => $data->time_range,
                'start_time' => $data->start_time,
                'end_time' => $data->end_time,
                'period_type' => $data->period_type,
            ];
        }
        
        return $this->successResponse($periods, __('messages.car_wash.periods_loaded'));
    }

    private function calculateCost($carSize, $washType)
    {
        return \App\Models\SystemSetting::getValue("car_wash_price_{$carSize}_{$washType}", 50);
    }
}
