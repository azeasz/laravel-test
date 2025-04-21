import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
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
    Button
} from '@mui/material';
import { extractAuthorFromScientificName, getDisplayName } from '../../utils/speciesUtils';
import DistributionMap from './DistributionMap';

const SpeciesDetail = () => {
    const { taxaId } = useParams();
    const [speciesData, setSpeciesData] = useState(null);
    const [similarSpecies, setSimilarSpecies] = useState([]);
    const [tabValue, setTabValue] = useState(0);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchSpeciesData();
        fetchSimilarSpecies();
    }, [taxaId]);

    const fetchSpeciesData = async () => {
        try {
            const response = await axios.get(`${import.meta.env.VITE_API_URL}/species-gallery/detail/${taxaId}`);
            setSpeciesData(response.data.data);
        } catch (error) {
            console.error('Error fetching species data:', error);
        } finally {
            setLoading(false);
        }
    };

    const fetchSimilarSpecies = async () => {
        try {
            const response = await axios.get(`${import.meta.env.VITE_API_URL}/species-gallery/${taxaId}/similar`);
            setSimilarSpecies(response.data.data);
        } catch (error) {
            console.error('Error fetching similar species:', error);
        }
    };

    if (loading || !speciesData) return <div>Loading...</div>;

    const { species, media, locations } = speciesData;
    const { name: scientificNameWithoutAuthor, author } = extractAuthorFromScientificName(species.scientific_name);
    const displayName = getDisplayName(species);

    return (
        <Box sx={{ p: 3, mt: 5 }}>
            <Paper elevation={3} sx={{ p: 3, mb: 3 }}>
                <Grid container spacing={3}>
                    {/* Informasi Species */}
                    <Grid item xs={12} md={6}>
                        <Typography variant="h4" component="h1" gutterBottom>
                            {displayName}
                        </Typography>
                        <Typography variant="h6" sx={{ fontStyle: 'italic' }} gutterBottom>
                            {scientificNameWithoutAuthor}
                        </Typography>
                        <Typography variant="body1" gutterBottom>
                            Family: {species.family}
                        </Typography>
                        {species.iucn_red_list_category && (
                            <Typography variant="body1" gutterBottom>
                                Status IUCN: {species.iucn_red_list_category}
                            </Typography>
                        )}
                        {species.status_kepunahan && (
                            <Typography variant="body1" gutterBottom>
                                Status Kepunahan: {species.status_kepunahan}
                            </Typography>
                        )}
                        {species.description && (
                            <Typography variant="body1" gutterBottom>
                                Deskripsi: {species.description}
                            </Typography>
                        )}
                    </Grid>

                    {/* Galeri Media dan Spectrogram */}
                    <Grid item xs={12} md={6}>
                        <ImageList cols={2} gap={8}>
                            {media.map((item, index) => (
                                <ImageListItem key={item.id}>
                                    <img
                                        src={`http://localhost:8000/storage/${item.file_path}` || `https://talinara.com/api/storage/${item.file_path}`}
                                        alt={`Observasi ${index + 1}`}
                                        loading="lazy"
                                    />
                                    {item.spectrogram && (
                                        <img
                                            src={`http://localhost:8000/storage/${item.spectrogram}` || `https://talinara.com/api/storage/${item.spectrogram}`}
                                            alt={`Spectrogram ${index + 1}`}
                                            style={{
                                                marginTop: '8px',
                                                borderTop: '1px solid rgba(0, 0, 0, 0.12)',
                                                backgroundColor: '#f5f5f5'
                                            }}
                                        />
                                    )}
                                    <Typography variant="caption" display="block">
                                        Tanggal: {new Date(item.date).toLocaleDateString('id-ID')}
                                    </Typography>
                                </ImageListItem>
                            ))}
                        </ImageList>
                    </Grid>
                </Grid>
            </Paper>

            {/* Tabs untuk Species Mirip dan Peta Sebaran */}
            <Paper elevation={3}>
                <Tabs
                    value={tabValue}
                    onChange={(e, newValue) => setTabValue(newValue)}
                    sx={{ borderBottom: 1, borderColor: 'divider' }}
                >
                    <Tab label="Species Mirip" />
                    <Tab label="Peta Sebaran" />
                </Tabs>

                <Box sx={{ p: 3 }}>
                    {tabValue === 0 && (
                        <Grid container spacing={2}>
                            {similarSpecies.map((species) => {
                                const { name: simScientificName, author: simAuthor } = extractAuthorFromScientificName(species.scientific_name);
                                const simDisplayName = getDisplayName(species);

                                return (
                                    <Grid item xs={12} sm={6} md={4} key={species.id}>
                                        <Card>
                                            <CardContent>
                                                <Typography variant="h6">
                                                    {simDisplayName}
                                                </Typography>
                                                <Typography
                                                    variant="body2"
                                                    sx={{ fontStyle: 'italic' }}
                                                >
                                                    {simScientificName}
                                                </Typography>
                                                {species.family && (
                                                    <Typography variant="body2" color="text.secondary">
                                                        Family: {species.family}
                                                    </Typography>
                                                )}
                                                <Button
                                                    variant="contained"
                                                    size="small"
                                                    sx={{ mt: 2 }}
                                                    onClick={() => window.open(`/species/${species.taxa_id}`, '_blank')}
                                                >
                                                    Lihat Species Ini
                                                </Button>
                                            </CardContent>
                                        </Card>
                                    </Grid>
                                );
                            })}
                        </Grid>
                    )}

                    {tabValue === 1 && <DistributionMap taxaId={taxaId} />}
                </Box>
            </Paper>
        </Box>
    );
};

export default SpeciesDetail;
