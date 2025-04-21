<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FaunaController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\BantuIdentifikasiController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\DataMigrationController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\Auth\FobiUserController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\FobiUploadController;
use App\Http\Controllers\SuggestionController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GenusGalleryController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\AdminChecklistController;
use App\Http\Controllers\TaxontestController;
use App\Http\Controllers\OverseerController;
use App\Http\Controllers\FobiUserManageController;
use App\Http\Controllers\Admin\TaxaManagementController;
use App\Http\Controllers\RegionController;
Route::get('/', function () {
    return view('admin.landing');
})->name('landing');

// Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/public_profile', [ProfileController::class, 'show'])->name('fobi_profile.show');
// Route::get('/faunas', [FaunaController::class, 'index'])->name('faunas.index');
Route::get('/migrate-data', [DataMigrationController::class, 'migrateData']);
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('/bantu-identifikasi', [BantuIdentifikasiController::class, 'index'])->name('bantu_identifikasi.index');
Route::get('/detail-identifikasi/{id}', [ChecklistController::class, 'show'])->name('detail_identifikasi.show');
// routes/web.php
use App\Http\Controllers\IdentifikasiObservasiController;

Route::get('/faunas', [IdentifikasiObservasiController::class, 'index'])->name('faunas.index');
Route::post('/faunas', [IdentifikasiObservasiController::class, 'store'])->name('faunas.store');
Route::get('/faunas/{id}/exif', [IdentifikasiObservasiController::class, 'showExif'])->name('observations.exif');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [UserProfileController::class, 'index'])->name('profile.home');
    Route::get('/profile/observasi', [UserProfileController::class, 'observasi'])->name('profile.observasi');
    Route::get('/profile/taksa-favorit', [UserProfileController::class, 'showTaksaFavorit'])->name('profile.taksa_favorit');
    Route::post('/profile/taksa-favorit', [UserProfileController::class, 'storeTaksaFavorit'])->name('profile.taksa_favorit.store');
    Route::post('/profile/taksa-favorit/reset', [UserProfileController::class, 'resetTaksaFavorit'])->name('profile.taksa_favorit.reset');
    Route::get('/profile/diskusi-identifikasi', [UserProfileController::class, 'showDiskusiIdentifikasi'])->name('profile.diskusi_identifikasi');
    Route::get('/profile/pilih-observasi', [UserProfileController::class, 'pilihObservasi'])->name('profile.pilih_observasi');
    Route::get('/profile/spesies-saya', [UserProfileController::class, 'showSpesiesSaya'])->name('profile.spesies_saya');
    Route::get('/profile/unggah-observasi-burungnesia', [UserProfileController::class, 'unggahObservasiBurungnesia'])->name('profile.unggah_observasi_burungnesia');
    Route::get('/profile/unggah-observasi-kupunesia', [UserProfileController::class, 'unggahObservasiKupunesia'])->name('profile.unggah_observasi_kupunesia');
    Route::post('/profile/simpan-observasi-burungnesia', [UserProfileController::class, 'simpanObservasiBurungnesia'])->name('profile.simpan_observasi_burungnesia');
    Route::post('/profile/simpan-observasi-kupunesia', [UserProfileController::class, 'simpanObservasiKupunesia'])->name('profile.simpan_observasi_kupunesia');
    Route::get('/profile/unggah-observasi-media', [UserProfileController::class, 'unggahObservasiMedia'])->name('profile.unggah_observasi_media');
    Route::post('/profile/simpan-observasi-media', [UserProfileController::class, 'simpanObservasiMedia'])->name('profile.simpan_observasi_media');
    Route::get('/profile/{uname}', [UserProfileController::class, 'showPublicProfile'])->name('profile.public');
    Route::get('/profile/{uname}/observasi', [UserProfileController::class, 'showPublicObservations'])->name('profile.public.observasi');
    Route::post('/profile/update', [UserProfileController::class, 'editProfile'])->name('profile.update');
});

Route::get('register', [FobiUserController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [FobiUserController::class, 'register']);
Route::get('login', [FobiUserController::class, 'showLoginForm'])->name('login');
Route::post('login', [FobiUserController::class, 'login']);
Route::post('logout', [FobiUserController::class, 'logout'])->name('logout');

Route::post('/uploads', [UploadController::class, 'store']);
Route::delete('/uploads/{upload}', [UploadController::class, 'destroy']);

// Route::post('/unggah-observasi-media', [FobiUploadController::class, 'storeMedia'])->name('fobi.upload.store_media');
Route::post('/unggah-observasi', [FobiUploadController::class, 'storeBurnesKupnesia'])->name('fobi.upload.store_burnes_kupnesia');

Route::post('/suggestions', [SuggestionController::class, 'store'])->name('suggestions.store');
Route::delete('/suggestions/{id}', [SuggestionController::class, 'destroy']);

Route::get('/discussions', [DiscussionController::class, 'index'])->name('discussions.index');
Route::post('/discussions', [DiscussionController::class, 'store'])->name('discussions.store');
Route::delete('/discussions/{id}', [DiscussionController::class, 'destroy']);

Route::get('/checklist/{source}', [ChecklistController::class, 'showSource'])->name('checklist.show_source');

Route::prefix('admin')->middleware('checkRole:3,4')->group(function () {
    Route::get('landing', [AdminController::class, 'landing'])->name('admin.landing');
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::get('checklists', [AdminChecklistController::class, 'index'])->name('admin.checklists.index');
    Route::get('checklists/create', [AdminChecklistController::class, 'create'])->name('admin.checklists.create');
    Route::post('checklists', [AdminChecklistController::class, 'store'])->name('admin.checklists.store');
    Route::get('checklists/{id}/edit', [AdminChecklistController::class, 'edit'])->name('admin.checklists.edit');
    Route::put('checklists/{id}', [AdminChecklistController::class, 'update'])->name('admin.checklists.update');
    Route::delete('checklists/{id}', [AdminChecklistController::class, 'destroy'])->name('admin.checklists.destroy');
    Route::get('taxontests', [TaxontestController::class, 'index'])->name('taxontests.index');
    Route::get('taxontests/create', [TaxontestController::class, 'create'])->name('taxontests.create');
    Route::post('taxontests', [TaxontestController::class, 'store'])->name('taxontests.store');
    Route::get('taxontests/{id}/edit', [TaxontestController::class, 'edit'])->name('taxontests.edit');
    Route::put('taxontests/{id}', [TaxontestController::class, 'update'])->name('taxontests.update');
    Route::delete('taxontests/{id}', [TaxontestController::class, 'destroy'])->name('taxontests.destroy');
    Route::get('taxontests/export', [TaxontestController::class, 'export'])->name('taxontests.export');
Route::post('taxontests/import', [TaxontestController::class, 'import'])->name('taxontests.import');
Route::post('taxontests/importSpecific/{id?}', [TaxontestController::class, 'importSpecific'])->name('taxontests.importSpecific');
Route::post('taxontests/importToForm', [TaxontestController::class, 'importToForm'])->name('taxontests.importToForm');
Route::get('taxontests/export/{id}', [TaxontestController::class, 'exportSpecific'])->name('taxontests.exportSpecific');
Route::get('overseer', [OverseerController::class, 'index'])->name('overseer.index');
Route::get('overseer/create', [OverseerController::class, 'create'])->name('overseer.create');
Route::post('overseer', [OverseerController::class, 'store'])->name('overseer.store');
Route::get('overseer/{overseer}/edit', [OverseerController::class, 'edit'])->name('overseer.edit');
Route::put('overseer/{overseer}', [OverseerController::class, 'update'])->name('overseer.update');
Route::delete('overseer/{overseer}', [OverseerController::class, 'destroy'])->name('overseer.destroy');
Route::get('fobiuser', [FobiUserManageController::class, 'index'])->name('fobiuser.index');
    Route::get('fobiuser/create', [FobiUserManageController::class, 'create'])->name('fobiuser.create');
    Route::post('fobiuser', [FobiUserManageController::class, 'store'])->name('fobiuser.store');
    Route::get('fobiuser/{user}/edit', [FobiUserManageController::class, 'edit'])->name('fobiuser.edit');
    Route::put('fobiuser/{user}', [FobiUserManageController::class, 'update'])->name('fobiuser.update');
    Route::delete('fobiuser/{user}', [FobiUserManageController::class, 'destroy'])->name('fobiuser.destroy');
    Route::resource('regions', RegionController::class);
    Route::get('/checklist/{id}', [AdminController::class, 'showChecklist'])->name('admin.checklist.show');
    Route::get('/taxa/{id}', [AdminController::class, 'showTaxa'])->name('admin.taxa.show');
    Route::get('/fobiuser/{id}', [AdminController::class, 'showFobiUser'])->name('admin.fobiuser.show');
    Route::get('/burungnesia-taxa', [AdminController::class, 'getBurungnesiaTaxa'])
    ->name('admin.burungnesia.taxa');
Route::get('/kupunesia-taxa', [AdminController::class, 'getKupunesiaTaxa'])
    ->name('admin.kupunesia.taxa');
    Route::get('/burungnesia/checklist/{id}', [AdminController::class, 'showBurungnesiaChecklist'])->name('admin.burungnesia.checklist.show');
    Route::get('/kupunesia/checklist/{id}', [AdminController::class, 'showKupunesiaChecklist'])->name('admin.kupunesia.checklist.show');
    Route::get('/burungnesia/user/{id}', [AdminController::class, 'showBurungnesiaUser'])->name('admin.burungnesia.user.show');
    Route::get('/kupunesia/user/{id}', [AdminController::class, 'showKupunesiaUser'])->name('admin.kupunesia.user.show');
});

// Route::get('/kurator', [KuratorController::class, 'index'])->middleware('checkRole:2'); // Hanya untuk kurator dan admin

Route::get('/genus/{genus}', [GenusGalleryController::class, 'show'])->name('genus.gallery');
Route::get('/genus/partials/map/{genus}', [MapController::class, 'show'])->name('map.show');
Route::get('/api/map/{genus}', [MapController::class, 'apiMap']);
   // routes/web.php
   use App\Http\Controllers\SpeciesDetailController;

   Route::get('/species/{checklist_id}/{fauna_id}', [SpeciesDetailController::class, 'show']);
   Route::middleware('auth')->group(function () {
    Route::post('/rate', [SpeciesDetailController::class, 'storeRating'])->name('rate');
    Route::post('/identify', [SpeciesDetailController::class, 'storeIdentification'])->name('identify');
    Route::post('/suggest', [SpeciesDetailController::class, 'storeSuggestion'])->name('suggest');
    Route::post('/comments', [SpeciesDetailController::class, 'postComment'])->name('postComment');
    Route::get('/comments/{checklist_id}', [SpeciesDetailController::class, 'getComments'])->name('getComments');
    Route::post('/species/cancel-identification', [SpeciesDetailController::class, 'cancelIdentification'])->name('cancelIdentify');
    Route::post('/suggestions/{id}/cancel', [SpeciesDetailController::class, 'cancel'])->name('suggestions.cancel');
    Route::post('/suggestions/{id}/undo-cancel', [SpeciesDetailController::class, 'undoCancel'])->name('suggestions.undoCancel');
    Route::post('/suggestions/{id}/delete', [SpeciesDetailController::class, 'delete'])->name('suggestions.delete');
    Route::get('/suggestions/{id}/edit', [SpeciesDetailController::class, 'edit'])->name('suggestions.edit');
Route::post('/suggestions/{id}/update', [SpeciesDetailController::class, 'update'])->name('suggestions.update');
Route::post('/user/follow', [UserProfileController::class, 'toggleFollow'])->name('user.follow');
Route::post('/user/report', [UserProfileController::class, 'reportUser'])->name('user.report');
Route::get('/user/follow-status/{userId}', [UserProfileController::class, 'followStatus'])->name('user.followStatus');
Route::post('/species/follow', [SpeciesDetailController::class, 'toggleFollow'])->name('species.follow');
Route::post('/species/report', [SpeciesDetailController::class, 'reportUser'])->name('species.report');
Route::get('/species/follow-status/{userId}', [SpeciesDetailController::class, 'followStatus'])->name('species.followStatus');

});
Route::get('/fauna-suggestions', [SpeciesDetailController::class, 'getFaunaSuggestions']);
// Route::get('/autocomplete/bird_species', [FobiUploadController::class, 'autocompleteBirdSpecies'])->name('autocomplete.bird_species');
use App\Http\Controllers\FobiObservationController;

// Rute untuk mengunggah observasi Burungnesia
Route::post('/upload/burungnesia', [FobiObservationController::class, 'storeBurungnesia'])->name('fobi.upload.store_burungnesia');

// Rute untuk mengunggah observasi Kupunesia
Route::post('/upload/kupunesia', [FobiObservationController::class, 'storeKupunesia'])->name('fobi.upload.store_kupunesia');

// Rute untuk mengunggah observasi Media
Route::post('/upload/media', [FobiObservationController::class, 'storeMedia'])->name('fobi.upload.store_media');

Route::get('/upload/burungnesia', function () {
    return view('unggah_burungnesia');
})->name('fobi.upload.burungnesia');

Route::get('/upload/kupunesia', function () {
    return view('unggah_kupunesia');
})->name('fobi.upload.kupunesia');



// Route::get('/fobi/upload', function () {
//     return view('fobi_upload');
// })->name('fobi.upload');

// Route::post('/fobi/store-checklist', [FobiObservationController::class, 'storeChecklist'])->name('fobi.storeChecklist');
// Route::post('/fobi/store-fauna', [FobiObservationController::class, 'storeFauna'])->name('fobi.storeFauna');

Route::middleware('auth')->group(function () {
    Route::get('/fobi/upload', [FobiObservationController::class, 'showUploadForm'])->name('fobi.upload');
    Route::post('/fobi/store-checklist-fauna', [FobiObservationController::class, 'storeChecklistAndFauna'])->name('fobi.storeChecklistAndFauna');
    Route::post('/fobi/store-burungnesia', [FobiObservationController::class, 'storeBurungnesia'])->name('fobi.storeBurungnesia');
    Route::get('/fobi/check-upload', [FobiObservationController::class, 'checkUpload'])->name('fobi.checkUpload');
});

Route::get('/fobi/get-fauna-id', [FobiObservationController::class, 'getFaunaId'])->name('fobi.getFaunaId');
Route::get('/verify-email/{token}/{type}', [FobiUserController::class, 'verifyEmail'])->name('verify.email');
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

Route::get('/password/reset', [ForgotPasswordController::class, '__invoke'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, '__invoke'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
use App\Http\Controllers\Api\FobiObservationApiController;
Route::get('/fobi-upload', [FobiObservationApiController::class, 'showUploadForm'])->name('fobi.uploads');

Route::get('/admin/api-data/{source}', [AdminController::class, 'getApiData']);
