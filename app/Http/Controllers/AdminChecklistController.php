<?php

namespace App\Http\Controllers;

use App\Models\FobiChecklistM;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;


class AdminChecklistController extends Controller
{
    public function index(Request $request)
    {
        // Paginasi untuk database kedua
        $checklistsBurungnesia = DB::connection('second')->table('checklists')->paginate(10, ['*'], 'burungnesia_page');

        // Paginasi untuk database ketiga
        $checklistsKupunesia = DB::connection('third')->table('checklists')->paginate(10, ['*'], 'kupunesia_page');

        return view('admin.checklists.index', compact('checklistsBurungnesia', 'checklistsKupunesia'));
    }

    public function show($id)
    {
        $checklist = FobiChecklistM::findOrFail($id);
        return view('admin.checklists.show', compact('checklist'));
    }

    public function create()
    {
        return view('admin.checklists.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'observer' => 'required|string',
            'additional_note' => 'nullable|string',
            'active' => 'required|boolean',
            'tgl_pengamatan' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'tujuan_pengamatan' => 'required|string',
            'completed' => 'required|boolean',
            'can_edit' => 'required|boolean',
        ]);

        FobiChecklistM::create($request->all());

        return redirect()->route('admin.checklists.index')->with('success', 'Checklist created successfully.');
    }

    public function edit($id)
    {
        $checklist = FobiChecklistM::findOrFail($id);
        return view('admin.checklists.edit', compact('checklist'));
    }

    public function update(Request $request, $id)
    {
        // Debug: Lihat data yang diterima
        \Log::info('Data yang diterima:', $request->all());

        $request->validate([
            'user_id' => 'nullable|integer',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'observer' => 'nullable|string',
            'additional_note' => 'nullable|string',
            'active' => 'nullable|boolean',
            'tgl_pengamatan' => 'nullable|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'tujuan_pengamatan' => 'nullable|string',
            'completed' => 'nullable|boolean',
            'can_edit' => 'nullable|boolean',
        ]);

        $checklist = FobiChecklistM::findOrFail($id);
        $checklist->update($request->all());

        // Debug: Konfirmasi update berhasil
        \Log::info('Checklist berhasil diupdate:', $checklist->toArray());

        return redirect()->route('admin.checklists.index')->with('success', 'Checklist updated successfully.');
    }
    public function destroy($id)
    {
        $checklist = FobiChecklistM::findOrFail($id);
        $checklist->delete();

        return redirect()->route('admin.checklists.index')->with('success', 'Checklist deleted successfully.');
    }
}
