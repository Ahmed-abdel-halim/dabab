<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderCategory;
use Illuminate\Http\Request;

class OrderCategoryController extends Controller
{
    public function index()
    {
        $categories = OrderCategory::all();
        return view('admin.settings.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'icon' => 'required|string',
            'fixed_price' => 'required|numeric|min:0',
        ]);

        OrderCategory::create([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
            'icon' => $request->icon,
            'fixed_price' => $request->fixed_price,
            'is_active' => true,
        ]);

        return back()->with('success', 'تم إضافة القسم الجديد بنجاح!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'fixed_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $category = OrderCategory::findOrFail($id);
        $category->update([
            'fixed_price' => $request->fixed_price,
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('success', 'تم تحديث القسم بنجاح!');
    }
}
