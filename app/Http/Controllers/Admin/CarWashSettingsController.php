<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarWashPeriod;
use Illuminate\Support\Facades\DB;

class CarWashSettingsController extends Controller
{
    public function index()
    {
        $prices = DB::table('system_settings')->where('group', 'car_wash_prices')->get();

        if ($prices->isEmpty()) {
            $defaults = [
                ['key' => 'car_wash_price_small_exterior', 'value' => '30', 'display_name' => 'سعر غسيل خارجي (سيارة صغيرة)'],
                ['key' => 'car_wash_price_small_interior', 'value' => '40', 'display_name' => 'سعر غسيل داخلي (سيارة صغيرة)'],
                ['key' => 'car_wash_price_small_interior_exterior', 'value' => '60', 'display_name' => 'سعر غسيل داخلي وخارجي (سيارة صغيرة)'],
                ['key' => 'car_wash_price_large_exterior', 'value' => '50', 'display_name' => 'سعر غسيل خارجي (سيارة كبيرة)'],
                ['key' => 'car_wash_price_large_interior', 'value' => '60', 'display_name' => 'سعر غسيل داخلي (سيارة كبيرة)'],
                ['key' => 'car_wash_price_large_interior_exterior', 'value' => '100', 'display_name' => 'سعر غسيل داخلي وخارجي (سيارة كبيرة)'],
            ];

            foreach ($defaults as $item) {
                $item['group'] = 'car_wash_prices';
                $item['created_at'] = now();
                $item['updated_at'] = now();
                DB::table('system_settings')->updateOrInsert(['key' => $item['key']], $item);
            }
            
            $prices = DB::table('system_settings')->where('group', 'car_wash_prices')->get();
        }

        $periods = CarWashPeriod::all();

        return view('admin.settings.carwash', compact('periods', 'prices'));
    }

    public function updatePrices(Request $request)
    {
        $data = $request->except('_token');
        
        foreach ($data as $key => $value) {
            DB::table('system_settings')->where('key', $key)->update(['value' => $value]);
        }

        return back()->with('success', 'تم تحديث الأسعار بنجاح!');
    }

    public function togglePeriod($id)
    {
        $period = CarWashPeriod::findOrFail($id);
        $period->is_active = !$period->is_active;
        $period->save();

        return back()->with('success', 'تم تغيير حالة الفترة بنجاح!');
    }

    public function storePeriod(Request $request)
    {
        $request->validate([
            'period_key' => 'required|string|unique:car_wash_periods,period_key',
            'time_range' => 'required|string',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'period_type' => 'required|string',
        ]);

        CarWashPeriod::create($request->all());

        return back()->with('success', 'تمت إضافة الفترة بنجاح!');
    }
}
