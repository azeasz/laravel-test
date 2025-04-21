<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index()
    {
        $regions = Region::withCount('overseers')->get();
        return view('admin.regions.index', compact('regions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:regions',
            'description' => 'nullable|string',
            'active' => 'boolean'
        ]);

        Region::create($request->all());
        return redirect()->route('admin.regions.index')->with('success', 'Region berhasil ditambahkan');
    }

    public function update(Request $request, Region $region)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:regions,code,' . $region->id,
            'description' => 'nullable|string',
            'active' => 'boolean'
        ]);

        $region->update($request->all());
        return redirect()->route('admin.regions.index')->with('success', 'Region berhasil diperbarui');
    }

    public function destroy(Region $region)
    {
        $region->delete();
        return redirect()->route('admin.regions.index')->with('success', 'Region berhasil dihapus');
    }
}
