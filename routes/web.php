<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\EventEmailApprovalController;
use Illuminate\Support\Facades\URL;

// Signed URL for approving an event directly from email
Route::get('/events/{event}/approve-email', EventEmailApprovalController::class)
    ->name('events.email-approve')
    ->middleware('signed');
