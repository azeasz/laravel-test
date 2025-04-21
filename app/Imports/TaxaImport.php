<?php

namespace App\Imports;

use App\Models\Taxa;
use App\Models\TaxaHistory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Auth;

class TaxaImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $taxa = Taxa::create([
            'taxon_key' => $row['taxon_key'],
            'scientific_name' => $row['scientific_name'],
            'accepted_taxon_key' => $row['accepted_taxon_key'],
            'accepted_scientific_name' => $row['accepted_scientific_name'],
            'taxon_rank' => $row['taxon_rank'],
            'taxonomic_status' => $row['taxonomic_status'],
            'domain' => $row['domain'],
            'cname_domain' => $row['cname_domain'],
            'superkingdom' => $row['superkingdom'],
            'cname_superkingdom' => $row['cname_superkingdom'],
            'kingdom' => $row['kingdom'],
            'cname_kingdom' => $row['cname_kingdom'],
            'kingdom_key' => $row['kingdom_key'],
            'subkingdom' => $row['subkingdom'],
            'cname_subkingdom' => $row['cname_subkingdom'],
            'superphylum' => $row['superphylum'],
            'cname_superphylum' => $row['cname_superphylum'],
            'phylum' => $row['phylum'],
            'cname_phylum' => $row['cname_phylum'],
            'phylum_key' => $row['phylum_key'],
            'subphylum' => $row['subphylum'],
            'cname_subphylum' => $row['cname_subphylum'],
            'superclass' => $row['superclass'],
            'cname_superclass' => $row['cname_superclass'],
            'class' => $row['class'],
            'cname_class' => $row['cname_class'],
            'class_key' => $row['class_key'],
            'subclass' => $row['subclass'],
            'cname_subclass' => $row['cname_subclass'],
            'infraclass' => $row['infraclass'],
            'cname_infraclass' => $row['cname_infraclass'],
            'subterclass' => $row['subterclass'],
            'superorder' => $row['superorder'],
            'cname_superorder' => $row['cname_superorder'],
            'order' => $row['order'],
            'cname_order' => $row['cname_order'],
            'order_key' => $row['order_key'],
            'suborder' => $row['suborder'],
            'cname_suborder' => $row['cname_suborder'],
            'infraorder' => $row['infraorder'],
            'superfamily' => $row['superfamily'],
            'cname_superfamily' => $row['cname_superfamily'],
            'family' => $row['family'],
            'cname_family' => $row['cname_family'],
            'family_key' => $row['family_key'],
            'subfamily' => $row['subfamily'],
            'cname_subfamily' => $row['cname_subfamily'],
            'genus' => $row['genus'],
            'cname_genus' => $row['cname_genus'],
            'genus_key' => $row['genus_key'],
            'species' => $row['species'],
            'cname_species' => $row['cname_species'],
            'species_key' => $row['species_key'],
            'subspecies' => $row['subspecies'],
            'cname_subspecies' => $row['cname_subspecies'],
            'variety' => $row['variety'],
            'cname_variety' => $row['cname_variety'],
            'iucn_red_list_category' => $row['iucn_red_list_category'],
            'status_kepunahan' => $row['status_kepunahan'],
            'created_by' => Auth::id(),
            'updated_by' => Auth::id()
        ]);

        TaxaHistory::create([
            'taxa_id' => $taxa->id,
            'action' => 'imported',
            'changes' => json_encode($taxa->toArray()),
            'changed_by' => Auth::id()
        ]);

        return $taxa;
    }

    public function rules(): array
    {
        return [
            'taxon_key' => 'required',
            'scientific_name' => 'required',
            'accepted_taxon_key' => 'nullable',
            'accepted_scientific_name' => 'nullable',
            'taxon_rank' => 'required',
            'taxonomic_status' => 'required',
            'domain' => 'nullable',
            'cname_domain' => 'nullable',
            'superkingdom' => 'nullable',
            'cname_superkingdom' => 'nullable',
            'kingdom' => 'nullable',
            'cname_kingdom' => 'nullable',
            'kingdom_key' => 'nullable',
            'subkingdom' => 'nullable',
            'cname_subkingdom' => 'nullable',
            'superphylum' => 'nullable',
            'cname_superphylum' => 'nullable',
            'phylum' => 'nullable',
            'cname_phylum' => 'nullable',
            'phylum_key' => 'nullable',
            'subphylum' => 'nullable',
            'cname_subphylum' => 'nullable',
            'superclass' => 'nullable',
            'cname_superclass' => 'nullable',
            'class' => 'nullable',
            'cname_class' => 'nullable',
            'class_key' => 'nullable',
            'subclass' => 'nullable',
            'cname_subclass' => 'nullable',
            'infraclass' => 'nullable',
            'cname_infraclass' => 'nullable',
            'subterclass' => 'nullable',
            'superorder' => 'nullable',
            'cname_superorder' => 'nullable',
            'order' => 'nullable',
            'cname_order' => 'nullable',
            'order_key' => 'nullable',
            'suborder' => 'nullable',
            'cname_suborder' => 'nullable',
            'infraorder' => 'nullable',
            'superfamily' => 'nullable',
            'cname_superfamily' => 'nullable',
            'family' => 'nullable',
            'cname_family' => 'nullable',
            'family_key' => 'nullable',
            'subfamily' => 'nullable',
            'cname_subfamily' => 'nullable',
            'genus' => 'nullable',
            'cname_genus' => 'nullable',
            'genus_key' => 'nullable',
            'species' => 'nullable',
            'cname_species' => 'nullable',
            'species_key' => 'nullable',
            'subspecies' => 'nullable',
            'cname_subspecies' => 'nullable',
            'variety' => 'nullable',
            'cname_variety' => 'nullable',
            'iucn_red_list_category' => 'nullable',
            'status_kepunahan' => 'nullable'
        ];
    }
}
