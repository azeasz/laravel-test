import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { UserProvider } from './context/UserContext'; // Import UserProvider
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
import ProfileHeader from './components/ProfileHeader';
import ChecklistDetail from './components/ChecklistDetail';
// Komponen PrivateRoute untuk memeriksa autentikasi dan peran pengguna
const PrivateRoute = ({ element: Component, requiredRole, ...rest }) => {
  const token = localStorage.getItem('jwt_token');
  const userRole = localStorage.getItem('user_role'); // Misalnya, peran pengguna disimpan di localStorage

  const isLoggedIn = !!token;
  const hasRequiredRole = requiredRole ? userRole === requiredRole : true;

  return isLoggedIn && hasRequiredRole ? (
    <Component {...rest} />
  ) : (
    <Navigate to="/login" />
  );
};

const App = () => {
  return (
    <UserProvider>
      <Router>
        <>
          {window.location.pathname !== '/pilih-observasi' && <Header />}
          <Routes>
            <Route path="/" element={<HomePage />} />
            <Route path="/explore" element={<ExplorePage />} />
            <Route path="/help" element={<HelpPage />} />
            <Route path="/community" element={<CommunityPage />} />
            <Route path="/login" element={<Login />} />
            <Route path="/register" element={<Register />} />
            <Route path="/logout" element={<Logout />} />
            <Route path="/upload" element={<UploadForm />} />
            <Route path="/upload-fobi" element={<UploadFobiData />} />
            
            {/* Tambahkan route untuk ChecklistDetail */}
            <Route path="/checklist/:id" element={<ChecklistDetail />} />
            
            {/* Protected routes */}
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
          </Routes>
        </>
      </Router>
    </UserProvider>
  );
};

export default App;