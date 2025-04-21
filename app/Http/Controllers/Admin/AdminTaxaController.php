<?php
// app/Http/Controllers/Admin/AdminTaxaController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Taxa;
use App\Models\AdminTaxaHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\Csv\Reader;
use League\Csv\Writer;

class AdminTaxaController extends Controller
{
    public function index(Request $request)
    {
        $query = Taxa::query();
        
        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('scientific_name', 'like', "%{$request->search}%")
                  ->orWhere('taxon_rank', 'like', "%{$request->search}%")
                  ->orWhere('kingdom', 'like', "%{$request->search}%");
            });
        }

        // Sort
        $sortField = $request->sort ?? 'created_at';
        $sortDirection = $request->direction ?? 'desc';
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->per_page ?? 10;
        $taxa = $query->paginate($perPage);

        return view('admin.taxa.index', compact('taxa'));
    }

    public function create()
    {
        return view('admin.taxa.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'scientific_name' => 'required',
            'taxon_rank' => 'required',
            // Add other validation rules
        ]);

        $taxa = Taxa::create($validated);
        
        // Record history
        AdminTaxaHistory::create([
            'taxa_id' => $taxa->id,
            'action' => 'create',
            'changes' => json_encode($validated),
            'user_id' => Auth::id()
        ]);

        return redirect()->route('admin.taxa.index')->with('success', 'Taxa created successfully');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt'
        ]);

        $csv = Reader::createFromPath($request->file('csv_file')->getPathname(), 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $record) {
            $taxa = Taxa::updateOrCreate(
                ['scientific_name' => $record['scientific_name']],
                $record
            );

            AdminTaxaHistory::create([
                'taxa_id' => $taxa->id,
                'action' => 'import',
                'changes' => json_encode($record),
                'user_id' => Auth::id()
            ]);
        }

        return redirect()->back()->with('success', 'Import completed successfully');
    }

    public function export()
    {
        $taxa = Taxa::all();
        $csv = Writer::createFromString('');
        
        // Add headers
        $csv->insertOne(array_keys($taxa->first()->toArray()));
        
        // Add data
        foreach ($taxa as $record) {
            $csv->insertOne($record->toArray());
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="taxa-export.csv"',
        ];

        return response($csv->toString(), 200, $headers);
    }
}
