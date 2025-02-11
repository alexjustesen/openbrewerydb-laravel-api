<?php
use App\Http\Controllers\Api\V1\GetBreweriesMeta;
use App\Http\Controllers\Api\V1\GetBrewery;
use App\Http\Controllers\Api\V1\ListBreweries;
use App\Http\Controllers\Api\V1\RandomBrewery;
use App\Http\Controllers\Api\V1\SearchBreweries;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/breweries', ListBreweries::class);
    Route::get('/breweries/random', RandomBrewery::class);
    Route::get('/breweries/search', SearchBreweries::class);
    Route::get('/breweries/meta', GetBreweriesMeta::class);

    Route::middleware('cache.headers:public;max_age=300;etag')->group(function () {
        Route::get('/breweries/{id}', GetBrewery::class);
    });
});
