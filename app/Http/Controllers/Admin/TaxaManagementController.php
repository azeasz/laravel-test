<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Taxa;
use App\Models\TaxaHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TaxaExport;
use App\Imports\TaxaImport;

class TaxaManagementController extends Controller
{
    public function index()
    {
        $taxa = Taxa::with('history')->paginate(20);
        return view('admin.taxas.index', compact('taxa'));
    }

    public function show($id)
{
    $taxa = Taxa::findOrFail($id);
        return view('admin.taxas.show', compact('taxa'));
    }

    public function create()
    {
        return view('admin.taxas.create');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $taxa = Taxa::create($request->all());

            // Log history
            TaxaHistory::create([
                'taxa_id' => $taxa->id,
                'action' => 'created',
                'changes' => json_encode($taxa->toArray()),
                'changed_by' => Auth::id()
            ]);

            DB::commit();
            return redirect()->route('admin.taxas.index')->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit(Taxa $taxa)
    {
        return view('admin.taxas.edit', compact('taxa'));
    }

    public function update(Request $request, Taxa $taxa)
    {
        DB::beginTransaction();
        try {
            $oldData = $taxa->toArray();
            $changes = array_diff_assoc($request->all(), $oldData);

            $taxa->update($request->all());

            // Log history jika ada perubahan
            if (!empty($changes)) {
                TaxaHistory::create([
                    'taxa_id' => $taxa->id,
                    'action' => 'updated',
                    'changes' => json_encode($changes),
                    'changed_by' => Auth::id()
                ]);
            }

            DB::commit();
            return redirect()->route('admin.taxas.index')->with('success', 'Data berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        try {
            Excel::import(new TaxaImport, $request->file('file'));
            return back()->with('success', 'Data berhasil diimpor');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function export()
    {
        return Excel::download(new TaxaExport, 'taxa.xlsx');
    }

    public function history(Taxa $taxa)
    {
        $history = TaxaHistory::where('taxa_id', $taxa->id)
            ->with('user')
            ->orderBy('changed_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal' => $item->changed_at,
                    'field' => $item->field_name,
                    'perubahan' => [
                        'old' => $item->old_value,
                        'new' => $item->new_value
                    ],
                    'user' => $item->user ? $item->user->name : 'System'
                ];
            });

        return view('admin.taxas.history', compact('taxa', 'history'));
    }

    // Tambahkan method untuk mencatat history secara manual jika diperlukan
    protected function recordHistory($taxa, $field, $oldValue, $newValue)
    {
        TaxaHistory::create([
            'taxa_id' => $taxa->id,
            'field_name' => $field,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'changed_at' => now(),
            'changed_by' => auth()->id()
        ]);
    }
}
