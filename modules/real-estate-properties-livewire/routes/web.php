<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Liberu\RealEstate\PropertiesLivewire\Components\AdvancedPropertySearch;
use Liberu\RealEstate\PropertiesLivewire\Components\PropertyComparison;
use Liberu\RealEstate\PropertiesLivewire\Components\PropertyDetail;
use Liberu\RealEstate\PropertiesLivewire\Components\PropertyList;
use Liberu\RealEstate\ViewingsLivewire\Components\ViewingBooking;

Route::get('/properties', PropertyList::class)->name('property.list');
Route::get('/properties/search', AdvancedPropertySearch::class)->name('property.search');
Route::get('/properties/compare/{propertyIds}', PropertyComparison::class)->name('property.compare');
Route::get('/properties/{propertyId}/book', ViewingBooking::class)->whereNumber('propertyId')->name('property.book');
Route::get('/properties/{propertyId}', PropertyDetail::class)->whereNumber('propertyId')->name('property.detail');
