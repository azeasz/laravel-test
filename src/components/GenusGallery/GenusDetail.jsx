import React, { useState, useEffect, useCallback } from 'react';
import { useParams, Link } from 'react-router-dom';
import axios from 'axios';
import {
    Grid,
    Card,
    CardMedia,
    CardContent,
    Typography,
    Tabs,
    Tab,
    Box,
    Paper,
    ImageList,
    ImageListItem,
    Button,
    ThemeProvider,
    createTheme,
    CssBaseline
} from '@mui/material';
import { extractAuthorFromScientificName } from '../../utils/speciesUtils';
import { MapContainer, TileLayer, Rectangle, useMap, Popup } from 'react-leaflet';
import L from 'leaflet';
import TreeView from 'react-treeview';
import "react-treeview/react-treeview.css";

// Komponen ZoomHandler yang terpisah
const ZoomHandler = ({ setVisibleGrid }) => {
    const map = useMap();
    
    useEffect(() => {
        if (!map) return;

        const handleZoomChange = () => {
            const zoom = map.getZoom();
            if (zoom > 14) {
                setVisibleGrid('small');
            } else if (zoom > 12) {
                setVisibleGrid('medium');
            } else if (zoom > 10) {
                setVisibleGrid('large');
            } else if (zoom > 8) {
                setVisibleGrid('extraLarge');
            } else {
                setVisibleGrid('superLarge');
            }
        };

        map.on('zoomend', handleZoomChange);
        return () => {
            map.off('zoomend', handleZoomChange);
        };
    }, [map, setVisibleGrid]);

    return null;
};

// Komponen DistributionMap
const DistributionMap = ({ genusId }) => {
    const [locations, setLocations] = useState([]);
    const [visibleGrid, setVisibleGrid] = useState('large');
    const [gridData, setGridData] = useState([]);
    const [loading, setLoading] = useState(true);
    
    useEffect(() => {
        const fetchLocations = async () => {
            try {
                const response = await axios.get(`${import.meta.env.VITE_API_URL}/genus-gallery/${genusId}/distribution`);
                if (response.data.success) {
                    setLocations(response.data.data);
                }
            } catch (error) {
                console.error('Error fetching distribution data:', error);
            } finally {
                setLoading(false);
            }
        };
        
        fetchLocations();
    }, [genusId]);
    
    // Fungsi untuk membuat grid
    const createSingleGrid = useCallback((lat, lng, gridSize) => {
        if (!lat || !lng || isNaN(parseFloat(lat)) || isNaN(parseFloat(lng))) return null;
        
        const latitude = parseFloat(lat);
        const longitude = parseFloat(lng);
        const halfSize = gridSize / 2;
        
        return {
            bounds: [
                [latitude - halfSize, longitude - halfSize],
                [latitude + halfSize, longitude + halfSize]
            ],
            center: [latitude, longitude]
        };
    }, []);
    
    // Membuat grid berdasarkan lokasi
    useEffect(() => {
        if (locations.length > 0) {
            const grids = locations.map(loc => {
                return {
                    small: createSingleGrid(loc.latitude, loc.longitude, 0.007),
                    medium: createSingleGrid(loc.latitude, loc.longitude, 0.02),
                    large: createSingleGrid(loc.latitude, loc.longitude, 0.05),
                    extraLarge: createSingleGrid(loc.latitude, loc.longitude, 0.2),
                    superLarge: createSingleGrid(loc.latitude, loc.longitude, 0.3)
                };
            }).filter(grid => grid.small && grid.medium && grid.large && grid.extraLarge && grid.superLarge);
            
            setGridData(grids);
        }
    }, [locations, createSingleGrid]);
    
    // Fungsi untuk mendapatkan warna grid
    const getGridColor = (count = 1) => {
        // Warna biru untuk grid, diadaptasi dari MapView.jsx
        if (count > 10) {
            return {
                fillColor: '#0066ff', // Biru medium
                color: '#1a73e8', 
                weight: 0.6,
                fillOpacity: 0.7,
                opacity: 0.6
            };
        } else if (count > 5) {
            return {
                fillColor: '#004dc4', // Biru gelap
                color: '#003380',
                weight: 0.5,
                fillOpacity: 0.6,
                opacity: 0.5
            };
        } else {
            return {
                fillColor: '#1a73e8', // Warna biru utama
                color: '#1a73e8',
                weight: 0.5,
                fillOpacity: 0.4,
                opacity: 0.4
            };
        }
    };
    
    // Fungsi untuk mendapatkan bounds dari semua lokasi
    const getBounds = () => {
        if (locations.length === 0) return L.latLngBounds([[-2.5489, 118.0149], [-2.5489, 118.0149]]);
        
        const points = locations.map(loc => [parseFloat(loc.latitude), parseFloat(loc.longitude)]);
        return L.latLngBounds(points).pad(0.2);
    };
    
    if (loading) {
        return (
            <Box sx={{ height: 400, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                <Typography>Loading map data...</Typography>
            </Box>
        );
    }
    
    if (locations.length === 0) {
        return (
            <Box sx={{ height: 400, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                <Typography>No location data available</Typography>
            </Box>
        );
    }
    
    return (
        <div className="relative h-[500px] md:h-[400px] rounded-xl overflow-hidden">
            <MapContainer 
                bounds={getBounds()}
                className="h-full w-full"
                maxZoom={14}
                zoomControl={false}
            >
                <TileLayer
                    url="https://cartodb-basemaps-{s}.global.ssl.fastly.net/dark_all/{z}/{x}/{y}.png"
                    attribution='&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
                />
                
                <ZoomHandler setVisibleGrid={setVisibleGrid} />
                
                {gridData.map((locationGrids, locationIndex) => {
                    const grid = locationGrids[visibleGrid];
                    if (!grid) return null;
                    
                    const loc = locations[locationIndex];
                    const gridStyle = getGridColor(1); // Default ke 1, bisa diganti dengan jumlah observasi jika ada
                    
                    return (
                        <Rectangle
                            key={`grid-${locationIndex}`}
                            bounds={grid.bounds}
                            pathOptions={gridStyle}
                        >
                            <Popup className="dark-popup">
                                <div className="p-2 text-white">
                                    <p className="text-sm text-gray-300">
                                        Lat: {parseFloat(loc.latitude).toFixed(6)}<br />
                                        Long: {parseFloat(loc.longitude).toFixed(6)}
                                    </p>
                                    {loc.observation_date && (
                                        <p className="text-xs text-gray-400 mt-1">
                                            Tanggal: {new Date(loc.observation_date).toLocaleDateString('id-ID')}
                                        </p>
                                    )}
                                </div>
                            </Popup>
                        </Rectangle>
                    );
                })}
            </MapContainer>
        </div>
    );
};

// Komponen TaxonomyTree untuk menampilkan pohon taksonomi
const TaxonomyTree = ({ genusData }) => {
    if (!genusData || !genusData.genus) return null;

    // Fungsi untuk membuat label node dengan styling yang lebih baik
    const renderTaxonomyNode = (label, value, isItalic = false) => (
        <div className="flex items-center py-1.5">
            <div className="flex-1">
                <span className="text-gray-300 text-sm">{label}</span>
                <div className={`font-medium text-white ${isItalic ? 'italic' : ''}`}>
                    {value}
                </div>
            </div>
        </div>
    );

    // Bangun urutan hierarki taksonomi
    const taxonomyLevels = [
        { key: 'kingdom', label: 'Kingdom', italic: false },
        { key: 'phylum', label: 'Phylum', italic: false },
        { key: 'class', label: 'Class', italic: false },
        { key: 'order', label: 'Order', italic: false },
        { key: 'family', label: 'Family', italic: false },
        { key: 'genus', label: 'Genus', italic: true }
    ];

    // Fungsi untuk membangun struktur tree
    const buildTaxonomyTree = () => {
        // Filter level yang ada datanya
        const availableLevels = taxonomyLevels.filter(level => 
            genusData.genus[level.key]
        );
        
        if (availableLevels.length === 0) return null;
        
        // Bangun tree dari bawah ke atas
        let currentNode = null;
        for (let i = availableLevels.length - 1; i >= 0; i--) {
            const level = availableLevels[i];
            const value = genusData.genus[level.key];
            
            const newNode = {
                nodeLabel: renderTaxonomyNode(level.label, value, level.italic),
                children: currentNode ? [currentNode] : []
            };
            currentNode = newNode;
        }

        return currentNode;
    };

    const taxonomyTree = buildTaxonomyTree();

    // Fungsi rekursif untuk merender tree
    const renderTree = (node) => {
        if (!node) return null;

        return (
            <TreeView
                nodeLabel={node.nodeLabel}
                defaultCollapsed={false}
                className="taxonomy-node"
            >
                {node.children.map((child, index) => (
                    <div 
                        key={index} 
                        className="ml-4 pl-4 border-l-2 border-[#333] hover:border-[#444] transition-colors"
                    >
                        {renderTree(child)}
                    </div>
                ))}
            </TreeView>
        );
    };

    return (
        <div className="bg-[#1e1e1e] rounded-lg p-6 text-white">
            <h3 className="text-lg font-semibold mb-4 text-white">Taksonomi</h3>
            <div className="space-y-1 taxonomy-tree">
                {taxonomyTree && renderTree(taxonomyTree)}
            </div>
        </div>
    );
};

// Tambahkan CSS untuk styling TreeView
const styles = `
.taxonomy-tree .tree-view_item {
    position: relative;
}

.taxonomy-tree .tree-view_arrow {
    position: absolute;
    left: -20px;
    top: 50%;
    transform: translateY(-50%);
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #B0B0B0;
    transition: all 0.2s ease;
}

.taxonomy-tree .tree-view_arrow::before {
    content: '';
    width: 6px;
    height: 6px;
    border-right: 2px solid currentColor;
    border-bottom: 2px solid currentColor;
    transform: rotate(-45deg);
    transition: transform 0.2s ease;
}

.taxonomy-tree .tree-view_arrow-collapsed::before {
    transform: rotate(45deg);
}

.taxonomy-tree .tree-view_children {
    margin-left: 0;
}

.taxonomy-node {
    position: relative;
    padding: 0.25rem 0;
}

.taxonomy-node:hover > .tree-view_item {
    background-color: #2c2c2c;
    border-radius: 0.375rem;
}

.taxonomy-tree a {
    position: relative;
    color: #1a73e8;
    text-decoration: none;
}

.taxonomy-tree a:hover {
    color: #4285f4;
}

.taxonomy-tree a::after {
    content: '';
    position: absolute;
    width: 100%;
    height: 1px;
    bottom: -1px;
    left: 0;
    background-color: currentColor;
    transform: scaleX(0);
    transform-origin: right;
    transition: transform 0.3s ease;
}

.taxonomy-tree a:hover::after {
    transform: scaleX(1);
    transform-origin: left;
}

/* CSS untuk popup leaflet dengan tema gelap */
.leaflet-popup-content-wrapper {
    background-color: #1e1e1e !important;
    color: #e0e0e0 !important;
    border: 1px solid #444;
}

.leaflet-popup-tip {
    background-color: #1e1e1e !important;
    border: 1px solid #444;
}

.leaflet-popup-close-button {
    color: #e0e0e0 !important;
}

.leaflet-popup-close-button:hover {
    color: #fff !important;
}

.dark-popup .leaflet-popup-content p {
    margin-top: 4px !important;
    margin-bottom: 4px !important;
}

.leaflet-control-attribution {
    background: rgba(30, 30, 30, 0.8) !important;
    color: #e0e0e0 !important;
}

.leaflet-control-attribution a {
    color: #1a73e8 !important;
}

.leaflet-control-layers,
.leaflet-control-zoom {
    background: #1e1e1e !important;
    border-color: #444 !important;
}

.leaflet-control-layers-toggle,
.leaflet-control-zoom-in,
.leaflet-control-zoom-out {
    background-color: #1e1e1e !important;
    color: #e0e0e0 !important;
}

.leaflet-control-zoom-in,
.leaflet-control-zoom-out {
    color: #e0e0e0 !important;
}

.leaflet-control-zoom-in:hover,
.leaflet-control-zoom-out:hover {
    background-color: #2c2c2c !important;
}
`;

// Inject CSS
const styleSheet = document.createElement("style");
styleSheet.innerText = styles;
document.head.appendChild(styleSheet);

const GenusDetail = () => {
    const { taxaId } = useParams();
    const [genusData, setGenusData] = useState(null);
    const [similarGenera, setSimilarGenera] = useState([]);
    const [tabValue, setTabValue] = useState(0);
    const [loading, setLoading] = useState(true);

    // Membuat tema gelap
    const darkTheme = createTheme({
        palette: {
            mode: 'dark',
            primary: {
                main: '#1a73e8',
            },
            secondary: {
                main: '#4285f4',
            },
            background: {
                paper: '#1e1e1e',
                default: '#121212',
            },
            text: {
                primary: '#e0e0e0',
                secondary: '#b0b0b0',
            },
            divider: '#444',
        },
        components: {
            MuiPaper: {
                styleOverrides: {
                    root: {
                        backgroundColor: '#1e1e1e',
                        borderRadius: 8,
                    },
                },
            },
            MuiCard: {
                styleOverrides: {
                    root: {
                        backgroundColor: '#2c2c2c',
                        border: '1px solid #444',
                    },
                },
            },
            MuiButton: {
                styleOverrides: {
                    contained: {
                        backgroundColor: '#1a73e8',
                        '&:hover': {
                            backgroundColor: '#0d47a1',
                        },
                    },
                },
            },
            MuiTabs: {
                styleOverrides: {
                    root: {
                        backgroundColor: '#1e1e1e',
                        borderBottom: '1px solid #444',
                    },
                },
            },
            MuiTab: {
                styleOverrides: {
                    root: {
                        color: '#b0b0b0',
                        '&.Mui-selected': {
                            color: '#1a73e8',
                        },
                    },
                },
            },
        },
    });

    useEffect(() => {
        fetchGenusData();
        fetchSimilarGenera();
    }, [taxaId]);

    const fetchGenusData = async () => {
        try {
            const response = await axios.get(`${import.meta.env.VITE_API_URL}/genus-gallery/detail/${taxaId}`);
            setGenusData(response.data.data);
        } catch (error) {
            console.error('Error fetching genus data:', error);
        } finally {
            setLoading(false);
        }
    };

    const fetchSimilarGenera = async () => {
        try {
            const response = await axios.get(`${import.meta.env.VITE_API_URL}/genus-gallery/${taxaId}/similar`);
            setSimilarGenera(response.data.data);
        } catch (error) {
            console.error('Error fetching similar genera:', error);
        }
    };

    if (loading || !genusData) return (
        <ThemeProvider theme={darkTheme}>
            <CssBaseline />
            <Box sx={{ display: 'flex', justifyContent: 'center', alignItems: 'center', height: '100vh' }}>
                <Typography variant="h6" color="text.secondary">Loading...</Typography>
            </Box>
        </ThemeProvider>
    );

    const { genus, media, locations, species } = genusData;
    const { name: scientificNameWithoutAuthor, author } = extractAuthorFromScientificName(genus.scientific_name);

    return (
        <ThemeProvider theme={darkTheme}>
            <CssBaseline />
        <Box sx={{ p: 3, mt: 5 }}>
            <Paper elevation={3} sx={{ p: 3, mb: 3 }}>
                <Grid container spacing={3}>
                    <Grid item xs={12} md={6}>
                        <Typography variant="h4" component="h1" gutterBottom>
                            {genus.genus}
                        </Typography>
                        <Typography variant="h6" sx={{ color: 'text.secondary' }} gutterBottom>
                            {genus.cname_genus}
                        </Typography>
                        <Typography variant="body1" gutterBottom>
                            Family: {genus.family}
                        </Typography>
                        {genus.description && (
                            <Typography variant="body1" gutterBottom>
                                Deskripsi: {genus.description}
                            </Typography>
                        )}
                    </Grid>

                    <Grid item xs={12} md={6}>
                        <ImageList cols={2} gap={8}>
                            {media.map((item, index) => (
                                <ImageListItem key={`media-${item.id}`}>
                                    <img
                                        src={`https://api.talinara.com/storage/${item.file_path}`}
                                        alt={`Observation ${index + 1}`}
                                        loading="lazy"
                                    />
                                </ImageListItem>
                            ))}
                        </ImageList>
                    </Grid>
                </Grid>
            </Paper>

            <Paper elevation={3}>
                <Tabs
                    value={tabValue}
                    onChange={(e, newValue) => setTabValue(newValue)}
                    sx={{ borderBottom: 1, borderColor: 'divider' }}
                >
                    <Tab label="Species dalam Genus Ini" />
                    <Tab label="Genus Mirip" />
                    <Tab label="Peta Sebaran" />
                        <Tab label="Taksonomi" />
                </Tabs>

                <Box sx={{ p: 3 }}>
                    {tabValue === 0 && (
                        <Grid container spacing={2}>
                                {species.map((sp) => {
                                    const { name: spScientificName, author: spAuthor } = extractAuthorFromScientificName(sp.scientific_name);
                                    const displayName = sp.local_name ? sp.local_name : spScientificName;

                                    return (
                                        <Grid item xs={12} sm={6} md={4} key={sp.taxa_id}>
                                    <Card>
                                                {sp.media && sp.media.length > 0 && (
                                                    <CardMedia
                                                        component="img"
                                                        height="140"
                                                        image={`https://api.talinara.com/storage/${sp.media[0].file_path}`}
                                                        alt={spScientificName}
                                                    />
                                                )}
                                        <CardContent>
                                                    <Typography variant="h6">
                                                        {displayName}
                                            </Typography>
                                                    <Typography
                                                        variant="body2"
                                                        sx={{ fontStyle: 'italic' }}
                                                    >
                                                        {spScientificName} {spAuthor && `(${spAuthor})`}
                                            </Typography>
                                            <Button
                                                variant="contained"
                                                size="small"
                                                sx={{ mt: 2 }}
                                                        component={Link}
                                                        to={`/species/${sp.taxa_id}`}
                                            >
                                                        Lihat Detail
                                            </Button>
                                        </CardContent>
                                    </Card>
                                </Grid>
                                    );
                                })}
                        </Grid>
                    )}

                    {tabValue === 1 && (
                        <Grid container spacing={2}>
                            {similarGenera.map((gen) => (
                                <Grid item xs={12} sm={6} md={4} key={`similar-${gen.taxa_id}`}>
                                    <Card>
                                        <CardContent>
                                            <Typography variant="h6" component="div">
                                                {gen.genus}
                                            </Typography>
                                            <Typography variant="body2" sx={{ color: 'text.secondary' }} gutterBottom>
                                                {gen.cname_genus}
                                            </Typography>
                                            <Button
                                                variant="contained"
                                                size="small"
                                                sx={{ mt: 2 }}
                                                onClick={() => window.open(`/genus/${gen.taxa_id}`, '_blank')}
                                            >
                                                Lihat Genus Ini
                                            </Button>
                                        </CardContent>
                                    </Card>
                                </Grid>
                            ))}
                        </Grid>
                    )}

                        {tabValue === 2 && <DistributionMap genusId={taxaId} />}

                        {tabValue === 3 && <TaxonomyTree genusData={genusData} />}
                </Box>
            </Paper>
        </Box>
        </ThemeProvider>
    );
};

export default GenusDetail;
