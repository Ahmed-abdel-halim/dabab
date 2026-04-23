<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index()
    {
        $ratings = Rating::with(['user', 'order'])->latest()->paginate(20);
        return view('admin.ratings.index', compact('ratings'));
    }

    public function destroy($id)
    {
        Rating::destroy($id);
        return back()->with('success', 'تم حذف التقييم!');
    }
}
