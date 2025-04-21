<?php

namespace App\Imports;

use App\Models\Taxontest;
use Maatwebsite\Excel\Concerns\ToModel;

class TaxontestsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Taxontest([
            'taxonKey' => (int) $row[0],
            'scientificName' => $row[1],
            'acceptedTaxonKey' => (int) $row[2],
            'acceptedScientificName' => $row[3],
            'taxonRank' => $row[4],
            'taxonomicStatus' => $row[5],
            'domain' => $row[6],
            'cnamedomain' => $row[7],
            'superkingdom' => $row[8],
            'cnamesuperkingdom' => $row[9],
            'kingdom' => $row[10],
            'cnameKingdom' => $row[11],
            'kingdomKey' => (int) $row[12],
            'subkingdom' => $row[13],
            'cnamesubkingdom' => $row[14],
            'superphylum' => $row[15],
            'cnamesuperphylum' => $row[16],
            'phylum' => $row[17],
            'cnamePhylum' => $row[18],
            'phylumKey' => (int) $row[19],
            'subphylum' => $row[20],
            'cnamesubphylum' => $row[21],
            'superclass' => $row[22],
            'cnamesuperclass' => $row[23],
            'class' => $row[24],
            'cnameClass' => $row[25],
            'classKey' => (int) $row[26],
            'subclass' => $row[27],
            'cnamesubclass' => $row[28],
            'superorder' => $row[29],
            'cnamesuperorder' => $row[30],
            'order' => $row[31],
            'cnameOrder' => $row[32],
            'orderKey' => (int) $row[33],
            'suborder' => $row[34],
            'cnamesuborder' => $row[35],
            'superfamily' => $row[36],
            'cnamesuperfamily' => $row[37],
            'family' => $row[38],
            'cnameFamily' => $row[39],
            'familyKey' => (int) $row[40],
            'subfamily' => $row[41],
            'cnamesubfamily' => $row[42],
            'supertribe' => $row[43],
            'cnamesupertribe' => $row[44],
            'tribe' => $row[45],
            'cnametribe' => $row[46],
            'subtribe' => $row[47],
            'cnamesubtribe' => $row[48],
            'genus' => $row[49],
            'cnameGenus' => $row[50],
            'genusKey' => $row[51],
            'subgenus' => $row[52],
            'cnamesubgenus' => $row[53],
            'species' => $row[54],
            'cnameSpecies' => $row[55],
            'speciesKey' => $row[56],
            'subspecies' => $row[57],
            'cnamesubspecies' => $row[58],
            'variety' => $row[59],
            'cnamevariety' => $row[60],
            'iucnRedListCategory' => $row[61],
            'statuskepunahan' => $row[62],
        ]);
    }
}
