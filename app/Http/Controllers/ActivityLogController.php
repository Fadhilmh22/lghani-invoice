<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:120',
            'user_id' => 'nullable|integer|exists:users,id',
            'action' => 'nullable|in:login_success,login_failed,create,edit,delete,logout',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'per_page' => 'nullable|in:10,20,50,100',
        ]);

        if (!empty($validated['start_date']) && !empty($validated['end_date']) && $validated['end_date'] < $validated['start_date']) {
            return back()
                ->withErrors(['end_date' => 'Tanggal sampai harus sama atau setelah tanggal dari.'])
                ->withInput();
        }

        $query = ActivityLog::query()->orderByDesc('created_at')->orderByDesc('id');

        if (!empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($activityQuery) use ($search) {
                $activityQuery->where('user_name', 'like', '%' . $search . '%')
                    ->orWhere('user_email', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('subject_type', 'like', '%' . $search . '%')
                    ->orWhere('subject_id', 'like', '%' . $search . '%')
                    ->orWhere('ip_address', 'like', '%' . $search . '%');
            });
        }

        if (!empty($validated['user_id'])) {
            $selectedUser = User::findOrFail($validated['user_id']);
            $query->where(function ($activityQuery) use ($selectedUser) {
                $activityQuery->where('user_id', $selectedUser->id)
                    ->orWhere('user_email', $selectedUser->email);
            });
        }

        if (!empty($validated['action'])) {
            $query->where('action', $validated['action']);
        }

        if (!empty($validated['start_date'])) {
            $query->whereDate('created_at', '>=', $validated['start_date']);
        }

        if (!empty($validated['end_date'])) {
            $query->whereDate('created_at', '<=', $validated['end_date']);
        }

        $perPage = (int) ($validated['per_page'] ?? 20);
        $logs = $query->paginate($perPage)->withQueryString();
        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('activity_logs.index', compact('logs', 'users'));
    }
}
