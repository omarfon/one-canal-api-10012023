<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/apple-app-site-association', function () {
    $json = file_get_contents(public_path('.well-known/apple-app-site-association'));
    return response($json, 200)
        ->header('Content-Type', 'application/json');
});

Route::get('/', function () {
    return view('welcome');
});
