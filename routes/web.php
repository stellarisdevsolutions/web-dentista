<?php

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
Route::post('/contacto', [HomeController::class, 'contactSubmit'])->name('contact.submit');
Route::post('/contact', [HomeController::class, 'contactSubmit']);

// Dedicated Blog pages
Route::get('/blog', [HomeController::class, 'blog'])->name('blog.index');
Route::get('/blog/{slug}', [HomeController::class, 'blogPost'])->name('blog.show');

// Dedicated online appointment scheduling page
Route::get('/reservar', [AppointmentBookingController::class, 'schedule'])->name('schedule');
Route::get('/schedule', [AppointmentBookingController::class, 'schedule'])->name('appointments.schedule');

// Appointment booking endpoints
Route::post('/appointments', [AppointmentBookingController::class, 'store'])->name('appointments.store');
Route::get('/api/services/{service}/dentists', [AppointmentBookingController::class, 'dentistsForService'])->name('api.services.dentists');
Route::get('/api/appointments/available-slots', [AppointmentBookingController::class, 'availableSlots'])->name('api.appointments.slots');
