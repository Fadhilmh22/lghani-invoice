<?php

namespace App\Http\Controllers;

use App\Models\RecycleBinEntry;
use App\Models\User;
use App\Services\RecycleBinService;
use Illuminate\Http\Request;

class RecycleBinController extends Controller
{
    public function index(Request $request, RecycleBinService $recycleBin)
    {
        $validated = $request->validate([
            'search' => 'nullable|string|max:120',
            'type' => 'nullable|in:' . implode(',', $recycleBin->types()),
            'deleted_by' => 'nullable|integer|exists:users,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'per_page' => 'nullable|in:10,20,50,100',
        ]);

        if (!empty($validated['start_date']) && !empty($validated['end_date']) && $validated['end_date'] < $validated['start_date']) {
            return back()
                ->withErrors(['end_date' => 'Tanggal sampai harus sama atau setelah tanggal dari.'])
                ->withInput();
        }

        $query = RecycleBinEntry::query()->orderByDesc('created_at')->orderByDesc('id');

        if (!empty($validated['search'])) {
            $search = $validated['search'];
            $query->where(function ($entryQuery) use ($search) {
                $entryQuery->where('label', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('resource_type', 'like', '%' . $search . '%')
                    ->orWhere('record_id', 'like', '%' . $search . '%')
                    ->orWhere('deleted_by_name', 'like', '%' . $search . '%')
                    ->orWhere('deleted_by_email', 'like', '%' . $search . '%');
            });
        }

        if (!empty($validated['type'])) {
            $query->where('resource_type', $validated['type']);
        }

        if (!empty($validated['deleted_by'])) {
            $query->where('deleted_by', $validated['deleted_by']);
        }

        if (!empty($validated['start_date'])) {
            $query->whereDate('created_at', '>=', $validated['start_date']);
        }

        if (!empty($validated['end_date'])) {
            $query->whereDate('created_at', '<=', $validated['end_date']);
        }

        $perPage = (int) ($validated['per_page'] ?? 20);
        $entries = $query->paginate($perPage)->withQueryString();
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $types = $recycleBin->types();
        $typeLabels = $recycleBin->typeLabels();

        return view('recycle_bin.index', compact('entries', 'users', 'types', 'typeLabels'));
    }

    public function restore($id, RecycleBinService $recycleBin)
    {
        $recycleBin->restore(RecycleBinEntry::findOrFail($id));
        return redirect()->route('recycle-bin.index')->with('success', 'Data berhasil dipulihkan.');
    }

    public function destroy($id, RecycleBinService $recycleBin)
    {
        $recycleBin->permanentlyDelete(RecycleBinEntry::findOrFail($id));
        return redirect()->route('recycle-bin.index')->with('success', 'Data dihapus permanen.');
    }

    public function empty(RecycleBinService $recycleBin)
    {
        $recycleBin->empty();
        return redirect()->route('recycle-bin.index')->with('success', 'Sampah berhasil dikosongkan.');
    }
}
