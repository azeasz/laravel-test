import React, { useState, useEffect, useRef } from 'react';
import { apiFetch } from '../../utils/api';
import { useDebounce } from '../../hooks/useDebounce';

function SpeciesAutocomplete({ source, onSelect, className }) {
    const [searchTerm, setSearchTerm] = useState('');
    const [suggestions, setSuggestions] = useState([]);
    const [isLoading, setIsLoading] = useState(false);
    const [showSuggestions, setShowSuggestions] = useState(false);
    const debouncedSearch = useDebounce(searchTerm, 300);
    const wrapperRef = useRef(null);

    useEffect(() => {
        const fetchSuggestions = async () => {
            if (debouncedSearch.length < 2) {
                setSuggestions([]);
                return;
            }

            setIsLoading(true);
            try {
                const sourceParam = source || 'burungnesia';
                const response = await apiFetch(`/faunas/search?q=${debouncedSearch}&source=${sourceParam}`);
                const data = await response.json();
                setSuggestions(data.data || []);
            } catch (error) {
                console.error('Error fetching suggestions:', error);
                setSuggestions([]);
            } finally {
                setIsLoading(false);
            }
        };

        fetchSuggestions();
    }, [debouncedSearch, source]);

    // Click outside handler
    useEffect(() => {
        const handleClickOutside = (event) => {
            if (wrapperRef.current && !wrapperRef.current.contains(event.target)) {
                setShowSuggestions(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const handleSelect = (species) => {
        setSearchTerm('');
        setShowSuggestions(false);
        onSelect(species);
    };

    return (
        <div ref={wrapperRef} className="relative">
            <div className="relative">
                <input
                    type="text"
                    value={searchTerm}
                    onChange={(e) => {
                        setSearchTerm(e.target.value);
                        setShowSuggestions(true);
                    }}
                    onFocus={() => setShowSuggestions(true)}
                    placeholder="Cari spesies..."
                    className={`w-full px-3 py-2 border rounded-md ${className} ${isLoading ? 'pr-10' : ''}`}
                />
                {isLoading && (
                    <div className="absolute right-3 top-1/2 -translate-y-1/2">
                        <svg className="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                )}
            </div>

            {showSuggestions && suggestions.length > 0 && (
                <div className="absolute z-50 w-full mt-1 bg-white border rounded-md shadow-lg max-h-60 overflow-y-auto">
                    {suggestions.map((species) => (
                        <div
                            key={species.id}
                            className="px-4 py-2 hover:bg-gray-100 cursor-pointer"
                            onClick={() => handleSelect(species)}
                        >
                            <div className="font-medium">{species.nameId}</div>
                            <div className="text-sm text-gray-600 italic">{species.nameLat}</div>
                            {species.family && (
                                <div className="text-xs text-gray-500">Family: {species.family}</div>
                            )}
                        </div>
                    ))}
                </div>
            )}

            {showSuggestions && searchTerm.length >= 2 && suggestions.length === 0 && !isLoading && (
                <div className="absolute z-50 w-full mt-1 bg-white border rounded-md shadow-lg p-4 text-center text-gray-500">
                    Tidak ada spesies yang ditemukan
                </div>
            )}
        </div>
    );
}

export default SpeciesAutocomplete;
