<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Eager load media: every card reads its first image, which is a query
        // per card otherwise.
        $featuredProperties = Property::query()
            ->with('media')
            ->where('is_featured', true)
            ->take(3)
            ->get();

        // The map is not fed from here: <x-property-map> owns that query, and
        // duplicating it meant the controller's version was built, thrown away
        // and replaced on every request.
        return view('home', ['featuredProperties' => $featuredProperties]);
    }
}
