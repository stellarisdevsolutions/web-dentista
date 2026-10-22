<?php

use App\Http\Controllers\Admin\MediaLibraryController;
use App\Http\Controllers\Admin\VisualEditorController;
use App\Http\Controllers\AppointmentBookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// Public clinic landing page
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/en', [HomeController::class, 'index'])->name('home.en');

// About / Specialists
Route::get('/nosotros', [HomeController::class, 'about'])->name('about');
Route::get('/about', [HomeController::class, 'about'])->name('about.en');

// Treatments / Services catalog and detail
Route::get('/servicios', [HomeController::class, 'services'])->name('services.index');
Route::get('/services', [HomeController::class, 'services'])->name('services.index.en');
Route::get('/servicios/{slug}', [HomeController::class, 'serviceDetail'])->name('services.show');
Route::get('/services/{slug}', [HomeController::class, 'serviceDetail'])->name('services.show.en');

// Contact page and direct message submission
Route::get('/contacto', [HomeController::class, 'contact'])->name('contact');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact.en');
Route::post('/contacto', [HomeController::class, 'contactSubmit'])->middleware('throttle:15,1')->name('contact.submit');
Route::post('/contact', [HomeController::class, 'contactSubmit'])->middleware('throttle:15,1')->name('contact.submit.en');

// Dedicated Blog pages
Route::get('/blog', [HomeController::class, 'blog'])->name('blog.index');
Route::get('/en/blog', [HomeController::class, 'blog'])->name('blog.index.en');
Route::get('/blog/{slug}', [HomeController::class, 'blogPost'])->name('blog.show');
Route::get('/en/blog/{slug}', [HomeController::class, 'blogPost'])->name('blog.show.en');

// Legal and Compliance Pages
Route::get('/politica-de-privacidad', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/privacy-policy', [HomeController::class, 'privacy'])->name('privacy.en');
Route::get('/privacy', [HomeController::class, 'privacy']);
Route::get('/terminos-y-condiciones', [HomeController::class, 'terms'])->name('terms');
Route::get('/terms-and-conditions', [HomeController::class, 'terms'])->name('terms.en');
Route::get('/terms', [HomeController::class, 'terms']);
Route::get('/derechos-del-paciente', [HomeController::class, 'patientRights'])->name('patient-rights');
Route::get('/patient-rights', [HomeController::class, 'patientRights'])->name('patient-rights.en');

// Dedicated online appointment scheduling page
Route::get('/reservar', [AppointmentBookingController::class, 'schedule'])->name('schedule');
Route::get('/schedule', [AppointmentBookingController::class, 'schedule'])->name('schedule.en');
Route::get('/appointments/schedule', [AppointmentBookingController::class, 'schedule'])->name('appointments.schedule');

// Search Engine Optimization (Sitemap & Robots.txt)
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', function () {
    $content = "User-agent: *\nDisallow: /admin\nDisallow: /admin/\n\nSitemap: ".url('/sitemap.xml')."\n";

    return response($content, 200, ['Content-Type' => 'text/plain; charset=utf-8']);
})->name('robots');

// Direct Language switcher route
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['es', 'en'], true)) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    $redirect = request('redirect');
    if ($redirect && filter_var($redirect, FILTER_VALIDATE_URL) && str_starts_with($redirect, url('/'))) {
        return redirect()->to($redirect);
    }

    return redirect()->back();
})->name('lang.switch');

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
    Route::patch('/media-library/{id}', [MediaLibraryController::class, 'update'])->name('media-library.update');
    Route::delete('/media-library/{id}', [MediaLibraryController::class, 'destroy'])->name('media-library.destroy');
    Route::post('/media-library/sync', [MediaLibraryController::class, 'syncExisting'])->name('media-library.sync');
});
