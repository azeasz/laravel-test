<?php

namespace App\Exports;

use App\Models\Taxontest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TaxontestsExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Taxontest::all();
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        return [
            'id',
            'Taxon Key',
            'Scientific Name',
            'Accepted Taxon Key',
            'Accepted Scientific Name',
            'Taxon Rank',
            'Taxonomic Status',
            'Domain',
            'Cname Domain',
            'Superkingdom',
            'Cname Superkingdom',
            'Kingdom',
            'Cname Kingdom',
            'Kingdom Key',
            'Subkingdom',
            'Cname Subkingdom',
            'Superphylum',
            'Cname Superphylum',
            'Phylum',
            'Cname Phylum',
            'Phylum Key',
            'Subphylum',
            'Cname Subphylum',
            'Superclass',
            'Cname Superclass',
            'Class',
            'Cname Class',
            'Class Key',
            'Subclass',
            'Cname Subclass',
            'Superorder',
            'Cname Superorder',
            'Order',
            'Cname Order',
            'Order Key',
            'Suborder',
            'Cname Suborder',
            'Superfamily',
            'Cname Superfamily',
            'Family',
            'Cname Family',
            'Family Key',
            'Subfamily',
            'Cname Subfamily',
            'Supertribe',
            'Cname Supertribe',
            'Tribe',
            'Cname Tribe',
            'Subtribe',
            'Cname Subtribe',
            'Genus',
            'Cname Genus',
            'Genus Key',
            'Subgenus',
            'Cname Subgenus',
            'Species',
            'Cname Species',
            'Species Key',
            'Subspecies',
            'Cname Subspecies',
            'Variety',
            'Cname Variety',
            'IUCN Red List Category',
            'Status Kepunahan',
        ];
    }
}
