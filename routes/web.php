<?php

    use App\Models\AccountHolder;
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

   
    Route::get('/', function () {

      
        return 'done';
        
    });

    Route::get('/test-load-data-file', function () {
        return response()->json((load_data_file(public_path('data/challenge.xml'))));
    });
    