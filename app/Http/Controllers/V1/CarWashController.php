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
            'time_period' => 'required|in:before_lunch,early_evening,dinner_time,late_night',
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

    public function cancelCarWash(Request $request, $id)
    {
        $carWash = CarWash::where('user_id', $request->user()->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->findOrFail($id);

        $carWash->update(['status' => 'cancelled']);

        return $this->successResponse($carWash, __('messages.car_wash.cancelled'));
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
        $periodsData = [
            'before_lunch' => [
                'time_range' => '11:00 - 13:00',
                'start_time' => '11:00',
                'end_time' => '13:00',
                'period_type' => 'afternoon',
            ],
            'early_evening' => [
                'time_range' => '17:30 - 19:30',
                'start_time' => '17:30',
                'end_time' => '19:30',
                'period_type' => 'evening',
            ],
            'dinner_time' => [
                'time_range' => '19:30 - 21:30',
                'start_time' => '19:30',
                'end_time' => '21:30',
                'period_type' => 'evening',
            ],
            'late_night' => [
                'time_range' => '21:30 - 00:30',
                'start_time' => '21:30',
                'end_time' => '00:30',
                'period_type' => 'evening',
            ],
        ];
        
        $periods = [];
        foreach ($periodsData as $period => $data) {
            $periods[] = [
                'period' => $period,
                'name' => __("messages.car_wash.periods.{$period}"),
                'time_range' => $data['time_range'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'period_type' => $data['period_type'],
            ];
        }
        
        return $this->successResponse($periods, __('messages.car_wash.periods_loaded'));
    }

    private function calculateCost($carSize, $washType)
    {
        $key = "car_wash_price_{$carSize}_{$washType}";
        
        $defaults = [
            'small_exterior' => 30,
            'small_interior' => 40,
            'small_interior_exterior' => 60,
            'large_exterior' => 50,
            'large_interior' => 60,
            'large_interior_exterior' => 100,
        ];

        return \App\Models\SystemSetting::getValue($key, $defaults["{$carSize}_{$washType}"] ?? 50);
    }
}
