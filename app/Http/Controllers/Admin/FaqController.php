<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::all();
        return view('admin.settings.faqs.index', compact('faqs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'question_ar' => 'required',
            'answer_ar' => 'required',
            'question_en' => 'required',
            'answer_en' => 'required',
        ]);

        Faq::create($request->all());

        return back()->with('success', 'تم إضافة السؤال بنجاح!');
    }

    public function update(Request $request, $id)
    {
        $faq = Faq::findOrFail($id);
        $faq->update($request->all());

        return back()->with('success', 'تم تحديث السؤال بنجاح!');
    }

    public function destroy($id)
    {
        Faq::destroy($id);
        return back()->with('success', 'تم حذف السؤال!');
    }
}
