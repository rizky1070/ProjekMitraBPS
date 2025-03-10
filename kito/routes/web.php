<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\BAController;
use App\Http\Controllers\SKController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KontrakController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InstansiController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\DisposisiController;
use App\Http\Controllers\IzinKeluarController;
use App\Http\Controllers\SuratMasukController;
use App\Http\Controllers\SuratTugasController;
use App\Http\Controllers\InputBarangController;
use App\Http\Controllers\MeetingNoteController;
use App\Http\Controllers\SuratKeluarController;
use App\Http\Controllers\DaftarBarangController;
use App\Http\Controllers\KetersediaanController;
use App\Http\Controllers\PermintaanBarangController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\InputMitraBpsController;
use App\Http\Controllers\DaftarSurveiBpsController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/storage-link', function(){
	Artisan::call('storage:link');
	return 'Storage linked successfully.';
});

Route::get('/', function () {
    return view('login');
});

Route::get('/hamukti', function () {
    return view('hamukti');
})->middleware(['auth', 'verified'])->name('hamukti');

Route::get('/agenkitapresensi', function () {
    return view('agenkita.agenkitapresensi');
})->middleware(['auth', 'verified'])->name('agenkitapresensi');

Route::get('/agenkitaformagenda', function () {
    return view('agenkita.agenkitaformagenda');
})->middleware(['auth', 'verified'])->name('agenkitaformagenda');


// agenda
Route::get('/agenkitaagenda', [ScheduleController::class, 'index'])->middleware(['auth', 'verified'])->name('agenkitaagenda.index');
Route::get('/events', [ScheduleController::class, 'getEvents'])->middleware(['auth', 'verified'])->name('agenkitaagenda.getEvents');

// full calendar
// Route::get('fullcalender', [ScheduleController::class, 'index']);
// Route::get('/events', [ScheduleController::class, 'getEvents']);
Route::delete('/schedule/delete/{id}', [ScheduleController::class, 'deleteEvent'])->middleware(['auth', 'verified'])->name('agenkitaagenda.deleteEvent');
Route::get('/events/{id}', [ScheduleController::class, 'getEventById'])->middleware(['auth', 'verified'])->name('agenkitaagenda.getEventById');
// Route::post('/schedule/{id}', [ScheduleController::class, 'update'])->middleware(['auth', 'verified'])->name('agenkitaagenda');;
// Route::post('/schedule/{id}/resize', [ScheduleController::class, 'resize']);
// Route::get('/events/search', [ScheduleController::class, 'search']);

// Route::view('add-schedule', 'schedule.add');
Route::post('/schedule/create', [ScheduleController::class, 'create'])->middleware(['auth', 'verified'])->name('schedules.create'); // 
Route::post('schedule/store', [ScheduleController::class, 'store'])->middleware(['auth', 'verified'])->name('schedules.store');
Route::put('/schedule/update/{id}', [ScheduleController::class, 'update'])->middleware(['auth', 'verified'])->name('schedules.update');
Route::get('/agenkitaformeditagenda/{id}', [ScheduleController::class, 'editFormAgenda'])->middleware(['auth', 'verified'])->name('agenkitaformeditagenda');
// upload image
// Route::post('/')

// presensi 
Route::get('/agenkitapresensi', [PresenceController::class, 'index'])->middleware(['auth', 'verified'])->name('agenkitapresensi.index');
Route::get('/agenkitaformpresensi', [PresenceController::class, 'getEventsPresensi'])->middleware(['auth', 'verified'])->name('agenkitaformpresensi.getEventsPresensi');
Route::get('presence/{id}', [PresenceController::class, 'getPresenceById'])->middleware(['auth', 'verified'])->name('agenkitapresensi.getPresenceById');
Route::post('/agenkitaformpresensi/store', [PresenceController::class, 'store'])->middleware(['auth', 'verified'])->name('agenkitaformpresensi.store');
Route::delete('/agenkitapresensi/delete/{id}', [PresenceController::class, 'deletePresence'])->middleware(['auth', 'verified'])->name('agenkitapresensi.deletePresence');
Route::put('/agenkitapresensi/update/{id}', [PresenceController::class, 'update'])->middleware(['auth', 'verified'])->name('agenkitapresensi.update');

Route::get('/pdf_export', [PresenceController::class, 'pdf_export_get'])->middleware(['auth', 'verified'])->name('agenkitapresensi.pdf_export_get');
// Route::get('/uploads/signatures/{signaturePath}', [PresenceController::class, 'index']);

// Notulen
Route::get('/agenkitanotulen', [MeetingNoteController::class, 'index'])->middleware(['auth', 'verified'])->name('agenkitanotulen.index');
// Route::get('/agenkitaformnotulen', [MeetingNoteController::class, 'getMeetingNotes'])->middleware(['auth', 'verified'])->name('agenkitaformnotulen.getMeetingNotes');
Route::post('/agenkitaformnotulen/store', [MeetingNoteController::class, 'store'])->middleware(['auth', 'verified'])->name('agenkitaformnotulen.store');
Route::delete('/agenkitanotulen/delete/{id}', [MeetingNoteController::class, 'delete'])->middleware(['auth', 'verified'])->name('agenkitanotulen.delete');
Route::put('/agenkitanotulen/update/{id}', [MeetingNoteController::class, 'update'])->middleware(['auth', 'verified'])->name('agenkitanotulen.update');
Route::get('/agenkitaformnotulen', [MeetingNoteController::class, 'getEventsNotulen'])->middleware(['auth', 'verified'])->name('agenkitaformnotulen.getEventsNotulen');
Route::get('notulen/{id}', [MeetingNoteController::class, 'getNotulenById'])->middleware(['auth', 'verified'])->name('agenkitanotulen.getNotulenById');
Route::get('/agenkitaformeditnotulen/{id}', [MeetingNoteController::class, 'editFormNotulen'])->middleware(['auth', 'verified'])->name('agenkitaformeditnotulen.editFormNotulen');


// hamukti
// surat keluar
// Route::get('/hamuktisuratkeluar', [SuratKeluarController::class, 'index'])->middleware(['auth', 'verified'])->name('hamuktisuratkeluar.index');
Route::delete('/hamuktisuratkeluar/delete/{id}', [SuratKeluarController::class, 'delete'])->middleware(['auth', 'verified'])->name('hamuktisuratkeluar.delete');
// Route::get('hamuktisuratkeluar/{id}', [SuratKeluarController::class, 'getSuratKeluarById'])->middleware(['auth', 'verified'])->name('hamuktisuratkeluar.getSuratKeluarById');
// Route::get('/hamuktisuratkeluarform', [SuratKeluarController::class, 'suratKeluarForm'])->middleware(['auth', 'verified'])->name('hamuktisuratkeluarform.suratKeluarForm');
// Route::post('/hamuktisuratkeluarform/store', [SuratKeluarController::class, 'store'])->middleware(['auth', 'verified'])->name('hamuktisuratkeluarform.store');
// Route::get('/hamuktisuratkeluarformedit/{id}', [SuratKeluarController::class, 'suratKeluarFormEdit'])->middleware(['auth', 'verified'])->name('hamuktisuratkeluarformedit.suratKeluarFormEdit');
Route::put('/hamuktisuratkeluar/update/{id}', [SuratKeluarController::class, 'update'])->middleware(['auth', 'verified'])->name('hamuktisuratkeluar.update');

// surat masuk
// Route::get('/hamuktisuratmasuk', [SuratMasukController::class, 'index'])->middleware(['auth', 'verified'])->name('hamuktisuratmasuk.index');
Route::delete('/hamuktisuratmasuk/delete/{id}', [SuratMasukController::class, 'delete'])->middleware(['auth', 'verified'])->name('hamuktisuratmasuk.delete');
// Route::get('hamuktisuratmasuk/{id}', [SuratMasukController::class, 'getSuratMasukById'])->middleware(['auth', 'verified'])->name('hamuktisuratmasuk.getSuratMasukById');
// Route::get('/hamuktisuratmasukform', [SuratMasukController::class, 'suratMasukForm'])->middleware(['auth', 'verified'])->name('hamuktisuratmasukform.suratMasukForm');
Route::post('/hamuktisuratmasukform/store', [SuratMasukController::class, 'store'])->middleware(['auth', 'verified'])->name('hamuktisuratmasukform.store');
// Route::get('/hamuktisuratmasukformedit/{id}', [SuratMasukController::class, 'suratMasukFormEdit'])->middleware(['auth', 'verified'])->name('hamuktisuratmasukformedit.suratMasukFormEdit');
Route::put('/hamuktisuratmasuk/update/{id}', [SuratMasukController::class, 'update'])->middleware(['auth', 'verified'])->name('hamuktisuratmasuk.update');

// SK
// Route::get('/hamuktisk', [SKController::class, 'index'])->middleware(['auth', 'verified'])->name('hamuktisk.index');
// Route::get('/hamuktiskform', [SKController::class, 'SKForm'])->middleware(['auth', 'verified'])->name('hamuktiskform.SKForm');
Route::delete('/hamuktisk/delete/{id}', [SKController::class, 'delete'])->middleware(['auth', 'verified'])->name('hamuktisk.delete');
// Route::get('hamuktisurattugas/{id}', [SuratTugasController::class, 'getSuratTugasById'])->middleware(['auth', 'verified'])->name('hamuktisurattugas.getSuratTugasById');
// Route::get('hamuktisk/{id}', [SKController::class, 'getSKById'])->middleware(['auth', 'verified'])->name('hamuktisk.getSKById');
Route::post('/hamuktiskform/store', [SKController::class, 'store'])->middleware(['auth', 'verified'])->name('hamuktiskform.store');
// Route::get('/hamuktiskformedit/{id}', [SKController::class, 'SKFormEdit'])->middleware(['auth', 'verified'])->name('hamuktiskformedit.SKFormEdit');
Route::put('/hamuktisk/update/{id}', [SKController::class, 'update'])->middleware(['auth', 'verified'])->name('hamuktisk.update');

// surattugas
// Route::get('/hamuktisurattugas', [SuratTugasController::class, 'index'])->middleware(['auth', 'verified'])->name('hamuktisurattugas.index');
// Route::get('/hamuktisurattugasform', [SuratTugasController::class, 'suratTugasForm'])->middleware(['auth', 'verified'])->name('hamuktisurattugasform.suratTugasForm');
Route::delete('/hamuktisurattugas/delete/{id}', [SuratTugasController::class, 'delete'])->middleware(['auth', 'verified'])->name('hamuktisurattugas.delete');
Route::post('/hamuktisurattugasform/store', [SuratTugasController::class, 'store'])->middleware(['auth', 'verified'])->name('hamuktisurattugasform.store');
// Route::get('/hamuktisurattugasformedit/{id}', [SuratTugasController::class, 'suratTugasFormEdit'])->middleware(['auth', 'verified'])->name('hamuktisurattugasformedit.suratTugasFormEdit');
Route::put('/hamuktisurattugas/update/{id}', [SuratTugasController::class, 'update'])->middleware(['auth', 'verified'])->name('hamuktisurattugas.update');

// kontrak
// Route::get('/hamuktikontrak', [KontrakController::class, 'index'])->middleware(['auth', 'verified'])->name('hamuktikontrak.index');
// Route::get('/hamuktikontrakform', [KontrakController::class, 'KontrakForm'])->middleware(['auth', 'verified'])->name('hamuktikontrakform.KontrakForm');
Route::delete('/hamuktikontrak/delete/{id}', [KontrakController::class, 'delete'])->middleware(['auth', 'verified'])->name('hamuktikontrak.delete');
// Route::get('hamuktikontrak/{id}', [KontrakController::class, 'getKontrakById'])->middleware(['auth', 'verified'])->name('hamuktikontrak.getKontrakById');
Route::post('/hamuktikontrakform/store', [KontrakController::class, 'store'])->middleware(['auth', 'verified'])->name('hamuktikontrakform.store');
// Route::get('/hamuktikontrakformedit/{id}', [KontrakController::class, 'kontrakFormEdit'])->middleware(['auth', 'verified'])->name('hamuktikontrakformedit.kontrakFormEdit');
Route::put('/hamuktikontrak/update/{id}', [KontrakController::class, 'update'])->middleware(['auth', 'verified'])->name('hamuktikontrak.update');

// BA
Route::get('/hamuktiba', [BAController::class, 'index'])->middleware(['auth', 'verified'])->name('hamuktiba.index');
Route::get('/hamuktibaform', [BAController::class, 'BAForm'])->middleware(['auth', 'verified'])->name('hamuktibaform.BAForm');
Route::delete('/hamuktiba/delete/{id}', [BAController::class, 'delete'])->middleware(['auth', 'verified'])->name('hamuktiba.delete');
Route::get('hamuktiba/{id}', [BAController::class, 'getBAById'])->middleware(['auth', 'verified'])->name('hamuktiba.getBAById');
Route::post('/hamuktibaform/store', [BAController::class, 'store'])->middleware(['auth', 'verified'])->name('hamuktibaform.store');
Route::get('/hamuktibaformedit/{id}', [BAController::class, 'BAFormEdit'])->middleware(['auth', 'verified'])->name('hamuktibaformedit.BAFormEdit');
Route::put('/hamuktiba/update/{id}', [BAController::class, 'update'])->middleware(['auth', 'verified'])->name('hamuktiba.update');


// SIMINBAR
// daftar barang
Route::get('/siminbardaftarbarang', [DaftarBarangController::class, 'index'])->middleware(['auth', 'verified'])->name('siminbardaftarbarang.index');
Route::get('/siminbardaftarbarangform', function () {
    return view('siminbar.siminbardaftarbarangform');
})->middleware(['auth', 'verified'])->name('siminbardaftarbarangform');
Route::get('/siminbardaftarbarangformedit/{id}', [DaftarBarangController::class, 'daftarBarangFormEdit'])->middleware(['auth', 'verified'])->name('siminbardaftarbarang.daftarBarangFormEdit');
Route::post('/siminbardaftarbarang/store', [DaftarBarangController::class, 'store'])->middleware(['auth', 'verified'])->name('siminbardaftarbarang.store');
Route::put('/siminbardaftarbarang/update/{id}', [DaftarBarangController::class, 'update'])->middleware(['auth', 'verified'])->name('siminbardaftarbarang.update');
Route::get('/siminbarinputbarangform/{id}', [InputBarangController::class, 'inputBarangForm'])->middleware(['auth', 'verified'])->name('siminbarinputbarangform.inputBarangForm');
Route::post('/siminbarinputbarang/store', [InputBarangController::class, 'store'])->middleware(['auth', 'verified'])->name('siminbarinputbarang.store');
Route::delete('/siminbardaftarbarang/delete/{id}', [DaftarBarangController::class, 'delete'])->middleware(['auth', 'verified'])->name('siminbardaftarbarang.delete');

// Ketersediaan
Route::get('/siminbarketersediaan', [KetersediaanController::class, 'index'])->middleware(['auth', 'verified'])->name('siminbarketersediaan.index');

// Permintaan barang
Route::get('/siminbarpermintaanbarangform', [DaftarBarangController::class, 'getDaftarBarang'])->middleware(['auth', 'verified'])->name('siminbardaftarbarangform.getDaftarBarang');
Route::get('/pdf_export_siminbar', [PermintaanBarangController::class, 'pdf_export_get_search'])->middleware(['auth', 'verified'])->name('siminbarpermintaanbarangadmin.pdf_export_get_search');
Route::delete('/siminbarpermintaanbarang/delete/{id}', [PermintaanBarangController::class, 'delete'])->middleware(['auth', 'verified'])->name('siminbarpermintaanbarang.delete');
Route::get('/siminbarpermintaanbarangadmin', [PermintaanBarangController::class, 'getPermintaanBarangAdmin'])->middleware(['auth', 'verified'])->name('siminbarpermintaanbarangadmin.getPermintaanBarangAdmin');
Route::post('/siminbarpermintaanbarang/store', [PermintaanBarangController::class, 'store'])->middleware(['auth', 'verified'])->name('siminbarpermintaanbarang.store');
Route::get('/siminbarpermintaanbarang', [PermintaanBarangController::class, 'getPermintaanBarangUser'])->middleware(['auth', 'verified'])->name('siminbarpermintaanbarang.getPermintaanBarangUser');
Route::get('siminbarpermintaanbaranguser/{id}', [PermintaanBarangController::class, 'getPermintaanBarangUserByID'])->middleware(['auth', 'verified'])->name('siminbarpermintaanbaranguser.getPermintaanBarangUserByID');
Route::get('siminbarpermintaanbarangekspor/{id}', [PermintaanBarangController::class, 'pdf_export_get'])->middleware(['auth', 'verified'])->name('siminbarpermintaanbarangekspor.pdf_export_get');

// user
Route::get('/daftaruser', [UserController::class, 'index'])->middleware(['auth', 'verified'])->name('daftaruser.index');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('registerrr');
// Route::post('/daftaruserform/store', [UserController::class, 'store'])->middleware(['auth', 'verified'])->name('daftaruserform.store');
Route::get('/daftaruserform', function () {
    return view('daftaruserform');
})->middleware(['auth', 'verified'])->name('daftaruserform');
Route::get('/daftaruserformedit/{id}', [UserController::class, 'getUserbyId'])->middleware(['auth', 'verified'])->name('daftaruserformedit.getUserbyId');
Route::put('/daftaruserformedit/update/{id}', [UserController::class, 'update'])->middleware(['auth', 'verified'])->name('daftaruserformedit.update');
// profile
Route::get('/ubahpassworduser', function () {
    return view('ubahpassworduser');
})->middleware(['auth', 'verified'])->name('ubahpassworduser');


Route::put('/password/update/{id}', [UserController::class, 'updatePassword'])->middleware(['auth', 'verified'])->name('ubahpassworduser.updatePassword');
Route::put('/profile/update/{id}', [UserController::class, 'updateProfile'])->middleware(['auth', 'verified'])->name('ubahprofileuser.updateProfile');

Route::get('/ubahprofileuser', function () {
    return view('ubahprofileuser');
})->middleware(['auth', 'verified'])->name('ubahprofileuser');


// Izin Keluar
Route::post('/izinkeluarform/store', [IzinKeluarController::class, 'store'])->middleware(['auth', 'verified'])->name('izinkeluar.store');
Route::put('/izinkeluar/update/{id}', [IzinKeluarController::class, 'update'])->middleware(['auth', 'verified'])->name('izinkeluar.update');
Route::get('/izinkeluarform', [IzinKeluarController::class, 'cekPulang'])->middleware(['auth', 'verified'])->name('izinkeluarform.cekPulang');

// Route::get('/agenkitaformpresensi/admin', [PresenceController::class, 'getEventsPresensiAdmin'])->middleware(['auth', 'verified'])->name('agenkitaformpresensi.admin');
// Route::get('siminbarpermintaanbarangadmin/{id}', [PermintaanBarangController::class, 'getPermintaanBarangAdminByID'])->middleware(['auth', 'verified'])->name('siminbarpermintaanbarangadmin.getPermintaanBarangAdminByID');
// Route::get('/siminbarpermintaanbarangadminform/{id}', [PermintaanBarangController::class, 'getPermintaanBarangAdminForm'])->middleware(['auth', 'verified'])->name('siminbarpermintaanbarangadminform.getPermintaanBarangAdminForm');
// Route::put('/siminbarpermintaanbarangadminform/storeAdmin/{id}', [PermintaanBarangController::class, 'storeAdmin'])->middleware(['auth', 'verified'])->name('siminbarpermintaanbarangadminform.storeAdmin');
Route::get('siminbarpermintaanbarangadmin/{id}', [PermintaanBarangController::class, 'getPermintaanBarangAdminByID'])->middleware(['auth', 'verified'])->name('siminbarpermintaanbarangadmin.getPermintaanBarangAdminByID');





//akses admin
Route::middleware(['auth', 'admin', 'verified'])->group(function () {
    Route::get('/agenkitaformpresensi/admin', [PresenceController::class, 'getEventsPresensiAdmin'])->name('agenkitaformpresensi.admin');
    Route::put('/siminbarpermintaanbarangadminform/storeAdmin/{id}', [PermintaanBarangController::class, 'storeAdmin'])->name('siminbarpermintaanbarangadminform.storeAdmin');
    Route::get('/siminbarpermintaanbarangadminform/{id}', [PermintaanBarangController::class, 'getPermintaanBarangAdminForm'])->name('siminbarpermintaanbarangadminform.getPermintaanBarangAdminForm');
    Route::get('/siminbardaftarbarang/export', [DaftarBarangController::class, 'export_excel'])->name('siminbardaftarbarang.export_excel');
    // Route::get('/izinkeluar', [IzinKeluarController::class, 'index'])->name('izinkeluar.index');
    // Route::get('/izinkeluarhistori', [IzinKeluarController::class, 'histori'])->name('historiizinkeluar.histori');
});

// akses leader
// Route::middleware(['auth', 'leader', 'admin', 'verified'])->group(function () {
    // Route::get('/izinkeluar', [IzinKeluarController::class, 'index'])->name('izinkeluar.index');
    // Route::get('/izinkeluarhistori', [IzinKeluarController::class, 'histori'])->name('historiizinkeluar.histori');
// });

// akses leader dan admin
Route::middleware(['auth', 'role', 'verified'])->group(function () {
    Route::get('/izinkeluar', [IzinKeluarController::class, 'index'])->name('izinkeluar.index');
    Route::get('/izinkeluarhistori', [IzinKeluarController::class, 'histori'])->name('historiizinkeluar.histori');
});




// akses umum
Route::get('/siminbarpermintaanbarangumumform/{id}', [PermintaanBarangController::class, 'getPermintaanBarangUmumForm'])->name('siminbarpermintaanbarangumumform.getPermintaanBarangUmumForm');
Route::middleware(['auth', 'umum', 'verified'])->group(function () {
    Route::put('/siminbarpermintaanbarangumumform/storeUmum/{id}', [PermintaanBarangController::class, 'storeUmum'])->name('siminbarpermintaanbarangumumform.storeUmum');
});

// akses hamukti
Route::middleware(['auth', 'hamukti', 'verified'])->group(function () {
    Route::get('/hamuktisuratkeluarform', [SuratKeluarController::class, 'suratKeluarForm'])->name('hamuktisuratkeluarform.suratKeluarForm');
    Route::post('/hamuktisuratkeluarform/store', [SuratKeluarController::class, 'store'])->name('hamuktisuratkeluarform.store');
    Route::get('hamuktisuratkeluar/{id}', [SuratKeluarController::class, 'getSuratKeluarById'])->name('hamuktisuratkeluar.getSuratKeluarById');
    Route::get('/hamuktisuratkeluar', [SuratKeluarController::class, 'index'])->name('hamuktisuratkeluar.index');
    Route::get('/hamuktisuratkeluarformedit/{id}', [SuratKeluarController::class, 'suratKeluarFormEdit'])->name('hamuktisuratkeluarformedit.suratKeluarFormEdit');
    Route::get('/hamuktisuratmasuk', [SuratMasukController::class, 'index'])->name('hamuktisuratmasuk.index');
    Route::get('hamuktisuratmasuk/{id}', [SuratMasukController::class, 'getSuratMasukById'])->name('hamuktisuratmasuk.getSuratMasukById');
    Route::get('/hamuktisuratmasukform', [SuratMasukController::class, 'suratMasukForm'])->name('hamuktisuratmasukform.suratMasukForm');
    Route::get('/hamuktisuratmasukformedit/{id}', [SuratMasukController::class, 'suratMasukFormEdit'])->name('hamuktisuratmasukformedit.suratMasukFormEdit');
    Route::get('/hamuktisk', [SKController::class, 'index'])->name('hamuktisk.index');
    Route::get('/hamuktiskform', [SKController::class, 'SKForm'])->name('hamuktiskform.SKForm');
    Route::get('hamuktisurattugas/{id}', [SuratTugasController::class, 'getSuratTugasById'])->name('hamuktisurattugas.getSuratTugasById');
    Route::get('hamuktisk/{id}', [SKController::class, 'getSKById'])->name('hamuktisk.getSKById');
    Route::get('/hamuktiskformedit/{id}', [SKController::class, 'SKFormEdit'])->name('hamuktiskformedit.SKFormEdit');
    Route::get('/hamuktisurattugas', [SuratTugasController::class, 'index'])->name('hamuktisurattugas.index');
    Route::get('/hamuktisurattugasform', [SuratTugasController::class, 'suratTugasForm'])->name('hamuktisurattugasform.suratTugasForm');
    Route::get('/hamuktisurattugasformedit/{id}', [SuratTugasController::class, 'suratTugasFormEdit'])->name('hamuktisurattugasformedit.suratTugasFormEdit');
    Route::get('/hamuktikontrakformedit/{id}', [KontrakController::class, 'kontrakFormEdit'])->name('hamuktikontrakformedit.kontrakFormEdit');
    Route::get('/hamuktikontrak', [KontrakController::class, 'index'])->name('hamuktikontrak.index');
    Route::get('/hamuktikontrakform', [KontrakController::class, 'KontrakForm'])->name('hamuktikontrakform.KontrakForm');
    Route::get('hamuktikontrak/{id}', [KontrakController::class, 'getKontrakById'])->name('hamuktikontrak.getKontrakById');
    Route::get('/hamuktiba', [BAController::class, 'index'])->name('hamuktiba.index');
    Route::get('/hamuktibaform', [BAController::class, 'BAForm'])->name('hamuktibaform.BAForm');
    Route::get('hamuktiba/{id}', [BAController::class, 'getBAById'])->name('hamuktiba.getBAById');
    Route::get('/hamuktibaformedit/{id}', [BAController::class, 'BAFormEdit'])->name('hamuktibaformedit.BAFormEdit');
});

// tambah instansi dan disposisi
Route::post('/instansi/store', [InstansiController::class, 'store'])->middleware(['auth', 'verified'])->name('instansi.store');
Route::post('/disposisi/store', [DisposisiController::class, 'store'])->middleware(['auth', 'verified'])->name('disposisi.store');

Route::get('/siminbar', function () {
    return view('siminbar');
})->middleware(['auth', 'verified'])->name('siminbar');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/', [AuthenticatedSessionController::class, 'store']);
    // Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
});


// MITRA BPS
Route::get('/daftarsurveibps', [DaftarSurveiBpsController::class, 'index']);
Route::get('/surveys', [DaftarSurveiBpsController::class, 'index']);
// Route::get('/mitrabps/inputmitrabps', [InputMitraBpsController::class, 'index']);

require __DIR__ . '/auth.php';
