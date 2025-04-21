import React from 'react';

const TaxonomyDetails = ({ fauna }) => {
    return (
        <div className="bg-white rounded-lg shadow-lg p-6">
            <h2 className="text-xl font-semibold mb-4">Detail Taksonomi</h2>
            <div className="grid grid-cols-2 gap-4">
                <div>
                    <div className="font-semibold">Nama Lokal</div>
                    <div>{fauna?.nama_lokal || '-'}</div>
                </div>
                <div>
                    <div className="font-semibold">Nama Ilmiah</div>
                    <div className="italic">{fauna?.nama_ilmiah || '-'}</div>
                </div>
                <div>
                    <div className="font-semibold">Family</div>
                    <div>{fauna?.family || '-'}</div>
                </div>
            </div>
        </div>
    );
};

export default TaxonomyDetails;
