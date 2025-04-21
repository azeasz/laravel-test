<?php

namespace App\Exports;

use App\Models\Taxa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TaxaExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Taxa::all();
    }

    public function headings(): array
    {
        return [
            'Taxon Key', 'Scientific Name', 'Accepted Taxon Key',
            'Accepted Scientific Name', 'Taxon Rank', 'Taxonomic Status',
            'Domain', 'Cname Domain', 'Superkingdom', 'Cname Superkingdom',
            'Kingdom', 'Cname Kingdom', 'Kingdom Key', 'Subkingdom', 'Cname Subkingdom',
            'Superphylum', 'Cname Superphylum', 'Phylum', 'Cname Phylum', 'Phylum Key',
            'Subphylum', 'Cname Subphylum', 'Superclass', 'Cname Superclass', 'Class',
            'Cname Class', 'Class Key', 'Subclass', 'Cname Subclass', 'Infraclass',
            'Cname Infraclass', 'Subterclass', 'Superorder', 'Cname Superorder', 'Order',
            'Cname Order', 'Order Key', 'Suborder', 'Cname Suborder', 'Infraorder',
            'Superfamily', 'Cname Superfamily', 'Family', 'Cname Family', 'Family Key',
            'Subfamily', 'Cname Subfamily', 'Genus', 'Cname Genus', 'Genus Key',
            'Species', 'Cname Species', 'Species Key', 'Subspecies', 'Cname Subspecies',
            'Variety', 'Cname Variety', 'IUCN Red List Category', 'Status Kepunahan'
        ];
    }

    public function map($taxa): array
    {
        return [
            $taxa->taxon_key,
            $taxa->scientific_name,
            $taxa->accepted_taxon_key,
            $taxa->accepted_scientific_name,
            $taxa->taxon_rank,
            $taxa->taxonomic_status,
            $taxa->domain,
            $taxa->cname_domain,
            $taxa->superkingdom,
            $taxa->cname_superkingdom,
            $taxa->kingdom,
            $taxa->cname_kingdom,
            $taxa->kingdom_key,
            $taxa->subkingdom,
            $taxa->cname_subkingdom,
            $taxa->superphylum,
            $taxa->cname_superphylum,
            $taxa->phylum,
            $taxa->cname_phylum,
            $taxa->phylum_key,
            $taxa->subphylum,
            $taxa->cname_subphylum,
            $taxa->superclass,
            $taxa->cname_superclass,
            $taxa->class,
            $taxa->cname_class,
            $taxa->class_key,
            $taxa->subclass,
            $taxa->cname_subclass,
            $taxa->infraclass,
            $taxa->cname_infraclass,
            $taxa->subterclass,
            $taxa->superorder,
            $taxa->cname_superorder,
            $taxa->order,
            $taxa->cname_order,
            $taxa->order_key,
            $taxa->suborder,
            $taxa->cname_suborder,
            $taxa->infraorder,
            $taxa->superfamily,
            $taxa->cname_superfamily,
            $taxa->family,
            $taxa->cname_family,
            $taxa->family_key,
            $taxa->subfamily,
            $taxa->cname_subfamily,
            $taxa->genus,
            $taxa->cname_genus,
            $taxa->genus_key,
            $taxa->species,
            $taxa->cname_species,
            $taxa->species_key,
            $taxa->subspecies,
            $taxa->cname_subspecies,
            $taxa->variety,
            $taxa->cname_variety,
            $taxa->iucn_red_list_category,
            $taxa->status_kepunahan
        ];
    }
}
