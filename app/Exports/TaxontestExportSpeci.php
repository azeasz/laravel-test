<?php

namespace App\Exports;

use App\Models\Taxontest;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TaxontestExportSpeci implements FromArray, WithHeadings
{
    protected $taxontest;

    public function __construct(Taxontest $taxontest)
    {
        $this->taxontest = $taxontest;
    }

    public function array(): array
    {
        return [
            [
                $this->taxontest->taxonKey,
                $this->taxontest->scientificName,
                $this->taxontest->acceptedTaxonKey,
                $this->taxontest->acceptedScientificName,
                $this->taxontest->taxonRank,
                $this->taxontest->taxonomicStatus,
                $this->taxontest->domain,
                $this->taxontest->cnameDomain,
                $this->taxontest->superkingdom,
                $this->taxontest->cnamesuperkingdom,
                $this->taxontest->kingdom,
                $this->taxontest->cnameKingdom,
                $this->taxontest->kingdomKey,
                $this->taxontest->subkingdom,
                $this->taxontest->cnamesubkingdom,
                $this->taxontest->superphylum,
                $this->taxontest->cnamesuperphylum,
                $this->taxontest->phylum,
                $this->taxontest->cnamePhylum,
                $this->taxontest->phylumKey,
                $this->taxontest->subphylum,
                $this->taxontest->cnamesubphylum,
                $this->taxontest->superclass,
                $this->taxontest->cnamesuperclass,
                $this->taxontest->class,
                $this->taxontest->cnameClass,
                $this->taxontest->classKey,
                $this->taxontest->subclass,
                $this->taxontest->cnamesubclass,
                $this->taxontest->superorder,
                $this->taxontest->cnamesuperorder,
                $this->taxontest->order,
                $this->taxontest->cnameOrder,
                $this->taxontest->orderKey,
                $this->taxontest->suborder,
                $this->taxontest->cnamesuborder,
                $this->taxontest->superfamily,
                $this->taxontest->cnamesuperfamily,
                $this->taxontest->family,
                $this->taxontest->cnameFamily,
                $this->taxontest->familyKey,
                $this->taxontest->subfamily,
                $this->taxontest->cnamesubfamily,
                $this->taxontest->supertribe,
                $this->taxontest->cnamesupertribe,
                $this->taxontest->tribe,
                $this->taxontest->cnameTribe,
                $this->taxontest->subtribe,
                $this->taxontest->cnamesubtribe,
                $this->taxontest->genus,
                $this->taxontest->cnameGenus,
                $this->taxontest->genusKey,
                $this->taxontest->subgenus,
                $this->taxontest->cnameSubgenus,
                $this->taxontest->species,
                $this->taxontest->cnameSpecies,
                $this->taxontest->speciesKey,
                $this->taxontest->subspecies,
                $this->taxontest->cnameSubspecies,
                $this->taxontest->variety,
                $this->taxontest->cnameVariety,
                $this->taxontest->iucnRedListCategory,
                $this->taxontest->statusKepunahan,
                // Tambahkan kolom lainnya sesuai kebutuhan
            ],
        ];
    }

    public function headings(): array
    {
        return [
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
