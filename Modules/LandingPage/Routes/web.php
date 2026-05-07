<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Modules\LandingPage\Http\Controllers\FeaturesController;
use Modules\LandingPage\Http\Controllers\HomeController;
use Modules\LandingPage\Http\Controllers\JoinUsController;
use Modules\LandingPage\Http\Controllers\LandingPageController;
use Modules\LandingPage\Http\Controllers\ScreenshotsController;

Route::resource('join_us', JoinUsController::class)->middleware('auth');
Route::post('join_us/store/', [JoinUsController::class, 'joinUsUserStore'])->name('join_us_store');

Route::group(
    [
        'middleware' => [
            'auth',
            'XSS',
        ],
    ],
    function () {

        Route::resource('landingpage', LandingPageController::class)->middleware('auth');

        Route::resource('homesection', HomeController::class)->middleware('auth');

        Route::resource('features', FeaturesController::class)->middleware('auth');

        Route::get('feature/create/', [FeaturesController::class, 'feature_create'])->name('feature_create');
        Route::post('feature/store/', [FeaturesController::class, 'feature_store'])->name('feature_store');
        Route::get('feature/edit/{key}', [FeaturesController::class, 'feature_edit'])->name('feature_edit');
        Route::post('feature/update/{key}', [FeaturesController::class, 'feature_update'])->name('feature_update');
        Route::get('feature/delete/{key}', [FeaturesController::class, 'feature_delete'])->name('feature_delete');

        Route::post('feature_highlight_create/', [FeaturesController::class, 'feature_highlight_create'])->name('feature_highlight_create');

        Route::get('features/create/', [FeaturesController::class, 'features_create'])->name('features_create');
        Route::post('features/store/', [FeaturesController::class, 'features_store'])->name('features_store');
        Route::get('features/edit/{key}', [FeaturesController::class, 'features_edit'])->name('features_edit');
        Route::post('features/update/{key}', [FeaturesController::class, 'features_update'])->name('features_update');
        Route::get('features/delete/{key}', [FeaturesController::class, 'features_delete'])->name('features_delete');

        Route::resource('screenshots', ScreenshotsController::class)->middleware('auth');
        Route::get('screenshots/create/', [ScreenshotsController::class, 'screenshots_create'])->name('screenshots_create');
        Route::post('screenshots/store/', [ScreenshotsController::class, 'screenshots_store'])->name('screenshots_store');
        Route::get('screenshots/edit/{key}', [ScreenshotsController::class, 'screenshots_edit'])->name('screenshots_edit');
        Route::post('screenshots/update/{key}', [ScreenshotsController::class, 'screenshots_update'])->name('screenshots_update');
        Route::get('screenshots/delete/{key}', [ScreenshotsController::class, 'screenshots_delete'])->name('screenshots_delete');
    }
);