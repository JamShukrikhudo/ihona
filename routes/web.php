<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ARTourController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomReportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyValuationController;
use App\Http\Controllers\TenancyApplicationController;
use App\Http\Controllers\TenantController;
use App\Livewire\BookingCalendar;
use App\Livewire\CalculatorsComponent;
use App\Livewire\HolographicViewer;
use App\Livewire\NewsDetail;
use App\Livewire\NewsList;
use App\Livewire\PropertyBooking;
use App\Livewire\PropertyComparison;
use App\Livewire\PropertyDetail;
use App\Livewire\PropertyList;
use App\Livewire\PropertyValuationComponent;
use App\Livewire\RentalApplicationForm;
use App\Livewire\WishlistManager;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::post('/bookings', [BookingController::class, 'store'])->middleware('throttle:10,1');
Route::middleware('auth')->group(function () {
    Route::get('/bookings/{booking}/calendar.ics', [BookingController::class, 'downloadIcs'])->name('booking.ics');
    Route::get('/appointments/{appointment}/calendar.ics', [AppointmentController::class, 'downloadIcs'])->name('appointment.ics');
    Route::put('/bookings/{booking}', [BookingController::class, 'update']);
    Route::get('/bookings', [BookingController::class, 'index']);
});
Route::get('/properties/{propertyId}/book', PropertyBooking::class)->name('property.book');
Route::post('/payments/session', [PaymentController::class, 'createSession'])->middleware(['auth', 'throttle:10,1']);
Route::get('/payments/success', [PaymentController::class, 'handlePaymentSuccess'])->middleware('auth');
// The component's mount() takes the appointment type, and the route had no
// parameter to give it, so the page could never boot: "Unable to resolve
// dependency [Parameter #0 $appointmentType]".
Route::get('/booking-calendar/{appointmentType}', BookingCalendar::class)->middleware('auth')->name('booking.calendar');
Route::get('/properties', PropertyList::class)->name('property.list');
Route::get('/properties/search', [PropertyController::class, 'search'])->name('property.search');
// Before the catch-all detail route, or {propertyId} swallows it.
Route::get('/properties/{property}/media/{medium}', \App\Http\Controllers\PropertyMediaController::class)
    ->whereNumber(['property', 'medium'])
    ->name('property.media');
Route::get('/properties/{propertyId}', PropertyDetail::class)->name('property.detail');
Route::get('/properties/{propertyId}/holographic-tour', HolographicViewer::class)->name('property.holographic-tour');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/apply/{property}', RentalApplicationForm::class)->name('rental.apply');
    Route::get('/applications', [TenantController::class, 'applications'])->name('tenant.applications');
    Route::get('/wishlist', WishlistManager::class)->name('wishlist');
    Route::get('/custom-reports', [CustomReportController::class, 'index'])->name('custom-reports.index');
    Route::post('/custom-reports/generate', [CustomReportController::class, 'generateReport'])->name('custom-reports.generate');
    Route::post('/custom-reports/export-pdf', [CustomReportController::class, 'exportReportToPdf'])->name('custom-reports.export-pdf');
    Route::post('/custom-reports/export-excel', [CustomReportController::class, 'exportReportToExcel'])->name('custom-reports.export-excel');
});

Route::get('/properties/{property}/apply', [TenancyApplicationController::class, 'create'])->name('tenancy.apply');
Route::post('/properties/{property}/apply', [TenancyApplicationController::class, 'store'])->name('tenancy.store');

Route::get('/properties/compare/{propertyIds}', PropertyComparison::class)->name('property.compare');

Route::controller(ContactController::class)->group(function () {
    Route::get('/contact', 'show')->name('contact.show');
    // Unauthenticated public write. Throttled so the enquiries table and the
    // agency's inbox cannot be filled by anyone with a loop.
    Route::post('/contact', 'submit')->name('contact.submit')->middleware('throttle:10,1');
});

// The Survey Sheet styleguide, rendered from the same components the public
// site uses so it cannot drift into a lie. Not available in production, and
// never indexed.
if (! app()->environment('production')) {
    Route::get('/design', fn () => response()
        ->view('design.styleguide')
        ->header('X-Robots-Tag', 'noindex, nofollow')
    )->name('design.styleguide');
}

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/terms-and-conditions', [PageController::class, 'terms'])->name('termsandconditions');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacypolicy');
Route::get('/services', [PageController::class, 'services'])->name('services');
Route::get('/calculators', CalculatorsComponent::class)->name('calculators');

Route::get('/news', NewsList::class)->name('news.list');
Route::get('/news/{slug}', NewsDetail::class)->name('news.detail');

Route::get('/properties/{propertyId}/valuation', PropertyValuationComponent::class)->name('property.valuation');
Route::middleware(['auth'])->group(function () {
    Route::post('/properties/{property}/valuation/generate', [PropertyValuationController::class, 'generateValuation'])->name('property.valuation.generate');
    Route::get('/properties/{property}/valuation/history', [PropertyValuationController::class, 'getValuationHistory'])->name('property.valuation.history');
    Route::get('/properties/{property}/valuation/report', [PropertyValuationController::class, 'getDetailedReport'])->name('property.valuation.report');
    Route::post('/valuation/train-model', [PropertyValuationController::class, 'trainModel'])->name('valuation.train');
});

Route::get('/properties/{property}/ar-tour/config', [ARTourController::class, 'getConfig'])->name('property.ar-tour.config');
Route::get('/properties/{property}/ar-tour/availability', [ARTourController::class, 'checkAvailability'])->name('property.ar-tour.availability');

Route::middleware(['auth'])->group(function () {
    Route::post('/properties/{property}/ar-tour/enable', [ARTourController::class, 'enable'])->name('property.ar-tour.enable');
    Route::post('/properties/{property}/ar-tour/disable', [ARTourController::class, 'disable'])->name('property.ar-tour.disable');
    Route::put('/properties/{property}/ar-tour/settings', [ARTourController::class, 'updateSettings'])->name('property.ar-tour.update-settings');
});

require __DIR__.'/socialstream.php';
