<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Authentication route
Route::match(['get', 'post'], '/login', [NewsController::class, 'login']);



Route::middleware('auth:sanctum')->group(function () {
    // List all news (GET)
    Route::get('news', 'NewsController@index');

    // Create a new news (POST)
    Route::post('news', 'NewsController@store');

    // Update a news by ID (PUT)
    Route::put('news/{id}', 'NewsController@update');

    // Delete a news by ID (DELETE)
    Route::delete('news/{id}', 'NewsController@destroy');

    // Get a specific news by ID (GET)
    Route::get('news/{id}', 'NewsController@show');

    // Display a list of news in descending order of publication date, excluding expired news
    Route::get('latest-news', 'NewsController@getLatestNews');

    // Récupérer les articles dans une catégorie et ses sous-catégories (GET)
    Route::get('category/{categoryId}/articles', 'NewsController@getArticlesInCategoryAndSubcategories');

    // Search for a category by name and retrieve associated articles (GET)
    Route::get('category/{categoryName}/articles', 'NewsController@search');
});
