<?php

use App\Http\Controllers\Admin\MediaLibraryController;
use App\Http\Controllers\Admin\VisualEditorController;
use App\Http\Controllers\AppointmentBookingController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Public clinic landing page
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/nosotros', [HomeController::class, 'about'])->name('about');
Route::get('/about', [HomeController::class, 'about']);

// Treatments / Services catalog and detail
Route::get('/servicios', [HomeController::class, 'services'])->name('services.index');
Route::get('/services', [HomeController::class, 'services']);
Route::get('/servicios/{slug}', [HomeController::class, 'serviceDetail'])->name('services.show');
Route::get('/services/{slug}', [HomeController::class, 'serviceDetail']);

// Contact page and direct message submission
Route::get('/contacto', [HomeController::class, 'contact'])->name('contact');
Route::get('/contact', [HomeController::class, 'contact']);
Route::post('/contacto', [HomeController::class, 'contactSubmit'])->middleware('throttle:15,1')->name('contact.submit');
Route::post('/contact', [HomeController::class, 'contactSubmit'])->middleware('throttle:15,1');

// Dedicated Blog pages
Route::get('/blog', [HomeController::class, 'blog'])->name('blog.index');
Route::get('/blog/{slug}', [HomeController::class, 'blogPost'])->name('blog.show');

// Dedicated online appointment scheduling page
Route::get('/reservar', [AppointmentBookingController::class, 'schedule'])->name('schedule');
Route::get('/schedule', [AppointmentBookingController::class, 'schedule'])->name('appointments.schedule');

// Appointment booking endpoints
Route::post('/appointments', [AppointmentBookingController::class, 'store'])->middleware('throttle:20,1')->name('appointments.store');
Route::get('/api/services/{service}/dentists', [AppointmentBookingController::class, 'dentistsForService'])->middleware('throttle:60,1')->name('api.services.dentists');
Route::get('/api/appointments/available-slots', [AppointmentBookingController::class, 'availableSlots'])->middleware('throttle:60,1')->name('api.appointments.slots');

// Full-screen Visual Editor and Media Library (Admin Only)
Route::middleware(['web', 'auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/editor', [VisualEditorController::class, 'index'])->name('editor.index');
    Route::post('/editor/save', [VisualEditorController::class, 'save'])->name('editor.save');

    Route::get('/media', [MediaLibraryController::class, 'standalone'])->name('media.standalone');
    Route::get('/media-library', [MediaLibraryController::class, 'index'])->name('media-library.index');
    Route::post('/media-library', [MediaLibraryController::class, 'store'])->name('media-library.store');
    Route::patch('/media-library/{mediaAsset}', [MediaLibraryController::class, 'update'])->name('media-library.update');
    Route::delete('/media-library/{mediaAsset}', [MediaLibraryController::class, 'destroy'])->name('media-library.destroy');
    Route::post('/media-library/sync', [MediaLibraryController::class, 'syncExisting'])->name('media-library.sync');
});
