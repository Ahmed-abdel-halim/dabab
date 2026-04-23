<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAgentProfile;
use Illuminate\Http\Request;

class AgentController extends Controller
{
    public function index(Request $request)
    {
        $query = DeliveryAgentProfile::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })->orWhere('national_id_number', 'like', "%{$search}%");
        }

        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $agents = $query->latest()->paginate(10)->withQueryString();

        return view('admin.agents.index', compact('agents'));
    }

    public function show($id)
    {
        $agent = DeliveryAgentProfile::with(['user', 'vehicle', 'bankDetails', 'documents'])->findOrFail($id);
        return view('admin.agents.show', compact('agent'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:active,pending,rejected,suspended',
            'admin_comment' => 'nullable|string'
        ]);

        $agent = DeliveryAgentProfile::findOrFail($id);
        $agent->status = $request->status;
        if ($request->has('admin_comment')) {
            $agent->admin_comment = $request->admin_comment;
        }
        $agent->save();

        return back()->with('success', 'تم تحديث حالة المندوب بنجاح!');
    }
}
