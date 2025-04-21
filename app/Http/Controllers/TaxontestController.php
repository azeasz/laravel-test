<?php

namespace App\Http\Controllers;

use App\Models\Taxontest;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TaxontestsExport;
use App\Exports\TaxontestExportSpeci;
use App\Imports\TaxontestsImport;
use Illuminate\Support\Facades\Session;



class TaxontestController extends Controller
{
    public function index()
    {
        $taxontests = Taxontest::paginate(10); // Menggunakan paginasi
        return view('admin.taxontests.index', compact('taxontests'));
    }

    public function apiIndex()
{
    $taxontests = Taxontest::paginate(10); // Menggunakan paginasi
    return response()->json($taxontests);
}


    public function create()
    {
        return view('admin.taxontests.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'taxonKey' => 'nullable|integer',
            'scientificName' => 'nullable|string',
            'acceptedTaxonKey' => 'nullable|integer',
            'acceptedScientificName' => 'nullable|string',
            'taxonRank' => 'nullable|string',
            'taxonomicStatus' => 'nullable|string',
            'domain' => 'nullable|string',
            'cnamedomain' => 'nullable|string',
            'superkingdom' => 'nullable|string',
            'cnamesuperkingdom' => 'nullable|string',
            'kingdom' => 'nullable|string',
            'cnameKingdom' => 'nullable|string',
            'kingdomKey' => 'nullable|integer',
            'subkingdom' => 'nullable|string',
            'cnamesubkingdom' => 'nullable|string',
            'superphylum' => 'nullable|string',
            'cnamesuperphylum' => 'nullable|string',
            'phylum' => 'nullable|string',
            'cnamePhylum' => 'nullable|string',
            'phylumKey' => 'nullable|integer',
            'subphylum' => 'nullable|string',
            'cnamesubphylum' => 'nullable|string',
            'superclass' => 'nullable|string',
            'cnamesuperclass' => 'nullable|string',
            'class' => 'nullable|string',
            'cnameClass' => 'nullable|string',
            'classKey' => 'nullable|integer',
            'subclass' => 'nullable|string',
            'cnamesubclass' => 'nullable|string',
            'superorder' => 'nullable|string',
            'cnamesuperorder' => 'nullable|string',
            'order' => 'nullable|string',
            'cnameOrder' => 'nullable|string',
            'orderKey' => 'nullable|integer',
            'suborder' => 'nullable|string',
            'cnamesuborder' => 'nullable|string',
            'superfamily' => 'nullable|string',
            'cnamesuperfamily' => 'nullable|string',
            'family' => 'nullable|string',
            'cnameFamily' => 'nullable|string',
            'familyKey' => 'nullable|integer',
            'subfamily' => 'nullable|string',
            'cnamesubfamily' => 'nullable|string',
            'supertribe' => 'nullable|string',
            'cnamesupertribe' => 'nullable|string',
            'tribe' => 'nullable|string',
            'cnametribe' => 'nullable|string',
            'subtribe' => 'nullable|string',
            'cnamesubtribe' => 'nullable|string',
            'genus' => 'nullable|string',
            'cnameGenus' => 'nullable|string',
            'genusKey' => 'nullable|integer',
            'subgenus' => 'nullable|string',
            'cnamesubgenus' => 'nullable|string',
            'species' => 'nullable|string',
            'cnameSpecies' => 'nullable|string',
            'speciesKey' => 'nullable|integer',
            'subspecies' => 'nullable|string',
            'cnamesubspecies' => 'nullable|string',
            'variety' => 'nullable|string',
            'cnamevariety' => 'nullable|string',
            'iucnRedListCategory' => 'nullable|string',
            'statuskepunahan' => 'nullable|string',
        ]);

        Taxontest::create($request->all());

        return redirect()->route('taxontests.index')->with('success', 'Taxontest created successfully.');
    }
    public function edit($id)
    {
        $taxontest = Taxontest::findOrFail($id);
        return view('admin.taxontests.edit', compact('taxontest'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'taxonKey' => 'nullable|integer',
            'scientificName' => 'nullable|string',
            'acceptedTaxonKey' => 'nullable|integer',
            'acceptedScientificName' => 'nullable|string',
            'taxonRank' => 'nullable|string',
            'taxonomicStatus' => 'nullable|string',
            'domain' => 'nullable|string',
            'cnamedomain' => 'nullable|string',
            'superkingdom' => 'nullable|string',
            'cnamesuperkingdom' => 'nullable|string',
            'kingdom' => 'nullable|string',
            'cnameKingdom' => 'nullable|string',
            'kingdomKey' => 'nullable|integer',
            'subkingdom' => 'nullable|string',
            'cnamesubkingdom' => 'nullable|string',
            'superphylum' => 'nullable|string',
            'cnamesuperphylum' => 'nullable|string',
            'phylum' => 'nullable|string',
            'cnamePhylum' => 'nullable|string',
            'phylumKey' => 'nullable|integer',
            'subphylum' => 'nullable|string',
            'cnamesubphylum' => 'nullable|string',
            'superclass' => 'nullable|string',
            'cnamesuperclass' => 'nullable|string',
            'class' => 'nullable|string',
            'cnameClass' => 'nullable|string',
            'classKey' => 'nullable|integer',
            'subclass' => 'nullable|string',
            'cnamesubclass' => 'nullable|string',
            'superorder' => 'nullable|string',
            'cnamesuperorder' => 'nullable|string',
            'order' => 'nullable|string',
            'cnameOrder' => 'nullable|string',
            'orderKey' => 'nullable|integer',
            'suborder' => 'nullable|string',
            'cnamesuborder' => 'nullable|string',
            'superfamily' => 'nullable|string',
            'cnamesuperfamily' => 'nullable|string',
            'family' => 'nullable|string',
            'cnameFamily' => 'nullable|string',
            'familyKey' => 'nullable|integer',
            'subfamily' => 'nullable|string',
            'cnamesubfamily' => 'nullable|string',
            'supertribe' => 'nullable|string',
            'cnamesupertribe' => 'nullable|string',
            'tribe' => 'nullable|string',
            'cnametribe' => 'nullable|string',
            'subtribe' => 'nullable|string',
            'cnamesubtribe' => 'nullable|string',
            'genus' => 'nullable|string',
            'cnameGenus' => 'nullable|string',
            'genusKey' => 'nullable|integer',
            'subgenus' => 'nullable|string',
            'cnamesubgenus' => 'nullable|string',
            'species' => 'nullable|string',
            'cnameSpecies' => 'nullable|string',
            'speciesKey' => 'nullable|integer',
            'subspecies' => 'nullable|string',
            'cnamesubspecies' => 'nullable|string',
            'variety' => 'nullable|string',
            'cnamevariety' => 'nullable|string',
            'iucnRedListCategory' => 'nullable|string',
            'statuskepunahan' => 'nullable|string',
        ]);

        $taxontest = Taxontest::findOrFail($id);
        $taxontest->update($request->all());

        return redirect()->route('taxontests.index')->with('success', 'Taxontest updated successfully.');
    }
    public function destroy($id)
    {
        $taxontest = Taxontest::findOrFail($id);
        $taxontest->delete();

        return redirect()->route('taxontests.index')->with('success', 'Taxontest deleted successfully.');
    }
    public function export()
    {
        return Excel::download(new TaxontestsExport, 'taxontests.csv');
    }
    public function exportSpecific($id)
{
    $taxontest = Taxontest::findOrFail($id);
    return Excel::download(new TaxontestExportSpeci($taxontest), 'taxontest_' . $id . '.csv');
}

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);

        Excel::import(new TaxontestsImport, $request->file('file'));

        return redirect()->route('taxontests.index')->with('success', 'Data imported successfully.');
    }
    public function importSpecific(Request $request, $id = null)
{
    $request->validate([
        'file' => 'required|mimes:csv,txt',
    ]);

    // Logic to handle specific import
    // If $id is provided, update the specific Taxontest
    // Otherwise, create a new Taxontest

    Excel::import(new TaxontestsImport, $request->file('file'));

    return redirect()->route('taxontests.index')->with('success', 'Data imported successfully.');
}

public function importToForm(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:csv,txt',
    ]);

    $path = $request->file('file')->getRealPath();
    $data = array_map('str_getcsv', file($path));

    // Simpan data ke sesi
    Session::put('importedData', $data[1]); // Asumsikan baris pertama adalah header

    return redirect()->route('taxontests.create')->with('success', 'Data imported to form.');
}
}
