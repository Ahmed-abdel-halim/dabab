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

        return $this->successResponse($carWash->load('location'), 'تم حجز موعد الغسيل بنجاح');
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

        return $this->successResponse($carWashes, 'تم جلب مواعيد الغسيل');
    }

    public function getCarWash(Request $request, $id)
    {
        $carWash = CarWash::where('user_id', $request->user()->id)
            ->with('address')
            ->findOrFail($id);

        return $this->successResponse($carWash, 'تم جلب موعد الغسيل');
    }

    public function updateCarWash(Request $request, $id)
    {
        $carWash = CarWash::where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->findOrFail($id);

        $request->validate([
            'car_size' => 'nullable|in:small,large',
            'wash_type' => 'nullable|in:interior_exterior,exterior,interior',
            'scheduled_date' => 'nullable|date|after_or_equal:today',
            'scheduled_time' => 'nullable|date_format:H:i',
            'time_period' => 'nullable|in:before_lunch,early_evening,dinner_time,late_night',
            'location_id' => 'nullable|exists:user_locations,id',
        ]);

        $updateData = $request->only([
            'car_size', 'wash_type', 'scheduled_date', 'scheduled_time',
            'time_period', 'location_id'
        ]);

        if ($request->has('car_size') || $request->has('wash_type')) {
            $carSize = $request->car_size ?? $carWash->car_size;
            $washType = $request->wash_type ?? $carWash->wash_type;
            $updateData['cost'] = $this->calculateCost($carSize, $washType);
        }

        $carWash->update($updateData);

        return $this->successResponse($carWash->load('address'), 'تم تحديث موعد الغسيل');
    }

    public function cancelCarWash(Request $request, $id)
    {
        $carWash = CarWash::where('user_id', $request->user()->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->findOrFail($id);

        $carWash->update(['status' => 'cancelled']);

        return $this->successResponse($carWash, 'تم إلغاء موعد الغسيل');
    }

    private function calculateCost($carSize, $washType)
    {
        $baseCosts = [
            'small' => [
                'exterior' => 30,
                'interior' => 40,
                'interior_exterior' => 60,
            ],
            'large' => [
                'exterior' => 50,
                'interior' => 60,
                'interior_exterior' => 100,
            ],
        ];

        return $baseCosts[$carSize][$washType] ?? 50;
    }
}

