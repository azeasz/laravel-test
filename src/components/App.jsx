import React, { useState } from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { UserProvider } from './context/UserContext';
import Header from './components/Header';
import HomePage from './components/Home/HomePage';
import ExplorePage from './components/ExplorePage';
import HelpPage from './components/HelpPage';
import CommunityPage from './components/CommunityPage';
import Login from './components/Auth/Login';
import Register from './components/Auth/Register';
import Logout from './components/Auth/Logout';
import UploadForm from './components/Upload/UploadForm';
import UploadFobiData from './components/UploadFobiData';
import FobiUpload from './components/FobiUpload';
import KupunesiaUpload from './components/KupunesiaUpload';
import MediaUpload from './components/MediaUpload';
import ProtectedRoute from './components/ProtectedRoute';
import PilihObservasi from './components/PilihObservasi';
import ChecklistDetail from './components/DetailObservations/ChecklistDetail';
import BirdObservationDetail from './components/DetailObservations/BirdObservation/BirdObservationDetail';
import ButterflyObservationDetail from './components/DetailObservations/ButterflyObservation/ButterflyObservationDetail';
import UserObservationsPage from './components/Home/UserObservationsPage';
import VerificationPending from './components/Auth/VerificationPending';
import VerifyEmail from './components/Auth/VerifyEmail';
import ForgotPassword from './components/Auth/ForgotPassword';
import ResetPassword from './components/Auth/ResetPassword';
import HistoryPage from './components/DetailObservations/HistoryPage';
import AdminHistoryPage from './components/DetailObservations/AdminHistoryPage';
import BantuIdent from './components/BantuIdent/BantuIdent';
import Profile from './pages/Profile';
import ProfileObservations from './pages/ProfileObservations';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { calculateCenterPoint } from './utils/geoHelpers';
import DetailChecklistBurkup from './components/DetailChecklistBurkup';
import AppChecklistDetail from './components/AppChecklistDetail';
import NotificationPage from './components/NotificationPage';
import PlatformVerification from './components/Auth/PlatformVerification';
import SyncAccounts from './components/Auth/SyncAccounts';
import SpeciesGallery from './components/SpeciesDetail/SpeciesGallery';
import SpeciesDetail from './components/SpeciesDetail/SpeciesDetail';
import GenusGallery from './components/GenusGallery/GenusGallery';
import GenusDetail from './components/GenusGallery/GenusDetail';
const queryClient = new QueryClient({
    defaultOptions: {
        queries: {
            refetchOnWindowFocus: false,
            retry: 1
        }
    }
});

const App = () => {
  const [searchResults, setSearchResults] = useState([]);
  const [searchParams, setSearchParams] = useState({
    search: '',
    location: '',
    latitude: '',
    longitude: '',
    searchType: 'all',
    boundingbox: null,
    calculatedRadius: null
  });

  const [filterParams, setFilterParams] = useState({
    start_date: '',
    end_date: '',
    grade: '',
    has_media: false,
    media_type: '',
    radius: 100,
    data_source: ['fobi', 'burungnesia', 'kupunesia']
  });

  // Fungsi untuk menangani pencarian dari Header dan StatsBar
  const handleSearch = (params) => {
    console.log('Search params received in App:', params);

    let centerPoint = null;

    // Hitung titik tengah jika ada boundingbox
    if (params.boundingbox) {
      centerPoint = calculateCenterPoint(
        parseFloat(params.boundingbox[0]), // south
        parseFloat(params.boundingbox[1]), // north
        parseFloat(params.boundingbox[2]), // west
        parseFloat(params.boundingbox[3])  // east
      );
    }

    // Update searchParams dengan boundingbox, radius yang dihitung, dan titik tengah
    setSearchParams(prevParams => ({
      ...prevParams,
      ...params,
      boundingbox: params.boundingbox || null,
      calculatedRadius: params.radius || null,
      // Gunakan titik tengah yang dihitung atau koordinat yang diberikan
      latitude: centerPoint ? centerPoint.lat : params.latitude,
      longitude: centerPoint ? centerPoint.lng : params.longitude
    }));

    // Update filterParams dengan radius yang baru
    if (params.radius || params.data_source || params.has_media || params.media_type) {
      setFilterParams(prevFilter => ({
        ...prevFilter,
        ...params,
        radius: params.radius || prevFilter.radius
      }));
    }
  };

  return (
    <QueryClientProvider client={queryClient}>
      <UserProvider>
        <Router>
          <>
            {
              (
               <Header
                 onSearch={handleSearch}
                 searchParams={searchParams}
                 filterParams={filterParams}
               />
             )}

            <Routes>
              <Route
                path="/"
                element={
                  <HomePage
                    searchParams={searchParams}
                    filterParams={filterParams}
                    onSearch={handleSearch}
                  />
                }
              />
              <Route path="/explore" element={<ExplorePage />} />
              <Route path="/help" element={<HelpPage />} />
              <Route path="/community" element={<CommunityPage />} />
              <Route path="/login" element={<Login />} />
              <Route path="/register" element={<Register />} />
              <Route path="/logout" element={<Logout />} />
              <Route path="/verification-pending" element={<VerificationPending />} />
              <Route path="/platform-verification" element={<PlatformVerification />} />
              <Route path="/sync-accounts" element={<SyncAccounts />} />
              <Route path="/verify-email/:token/:tokenType" element={<VerifyEmail />} />
              <Route path="/forgot-password" element={<ForgotPassword />} />
              <Route path="/reset-password" element={<ResetPassword />} />
              <Route path="/history/:id" element={<HistoryPage />} />
              <Route path="/species-gallery" element={<SpeciesGallery />} />
              <Route path="/species/:taxaId" element={<SpeciesDetail />} />
              <Route path="/genus" element={<GenusGallery />} />
              <Route path="/genus/:taxaId" element={<GenusDetail />} />
              {/* Protected Routes */}
              <Route path="/upload" element={
                <ProtectedRoute>
                  <UploadForm />
                </ProtectedRoute>
              } />

              <Route path="/upload-fobi" element={
                <ProtectedRoute>
                  <UploadFobiData />
                </ProtectedRoute>
              } />

              <Route path="/media-upload" element={
                <ProtectedRoute>
                  <MediaUpload />
                </ProtectedRoute>
              } />

              <Route path="/burungnesia-upload" element={
                <ProtectedRoute>
                  <FobiUpload />
                </ProtectedRoute>
              } />

              <Route path="/kupunesia-upload" element={
                <ProtectedRoute>
                  <KupunesiaUpload />
                </ProtectedRoute>
              } />

              <Route path="/pilih-observasi" element={
                <ProtectedRoute>
                  <PilihObservasi />
                </ProtectedRoute>
              } />

              <Route path="/observations/:id" element={
                <ProtectedRoute>
                  <ChecklistDetail />
                </ProtectedRoute>
              } />

              <Route path="/detail-checklist/:id" element={
                <ProtectedRoute>
                  <DetailChecklistBurkup />
                </ProtectedRoute>
              } />

              <Route path="/app-checklist/:id" element={
                <ProtectedRoute>
                  <AppChecklistDetail />
                </ProtectedRoute>
              } />

              <Route path="/burungnesia/observations/:id" element={
                <ProtectedRoute>
                  <BirdObservationDetail />
                </ProtectedRoute>
              } />

              <Route path="/kupunesia/observations/:id" element={
                <ProtectedRoute>
                  <ButterflyObservationDetail />
                </ProtectedRoute>
              } />

              <Route path="/user-observations" element={
                <ProtectedRoute>
                  <UserObservationsPage searchParams={searchParams} />
                </ProtectedRoute>
              } />

              <Route path="/admin-history" element={
                <ProtectedRoute>
                  <AdminHistoryPage />
                </ProtectedRoute>
              } />
              <Route path="/bantu-ident" element={
                <ProtectedRoute>
                    <BantuIdent />
                </ProtectedRoute>
            } />
            <Route path="/profile/:id" element={
              <ProtectedRoute>
                <Profile />
              </ProtectedRoute>
            } />
            <Route path="/profile/:id/observasi" element={
              <ProtectedRoute>
                <ProfileObservations />
              </ProtectedRoute>
            } />
            <Route path="/notifications" element={<NotificationPage />} />
            </Routes>
          </>
        </Router>
      </UserProvider>
    </QueryClientProvider>
  );
};

export default App;
