<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::all();
        return view('admin.settings.pages.index', compact('pages'));
    }

    public function edit($id)
    {
        $page = Page::findOrFail($id);
        return view('admin.settings.pages.edit', compact('page'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'content_ar' => 'required',
            'content_en' => 'required',
        ]);

        $page = Page::findOrFail($id);
        $page->update($request->only('content_ar', 'content_en'));

        return redirect()->route('admin.settings.pages.index')->with('success', 'تم تحديث الصفحة بنجاح!');
    }
}
