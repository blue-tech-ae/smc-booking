<?php

use App\Http\Controllers\Api\Admin\AdminAllBookingsController;
use App\Http\Controllers\Api\Admin\AdminCalendarController;
use App\Http\Controllers\Api\Admin\AdminCalendarOverviewController;
use App\Http\Controllers\Api\Admin\AdminEventApprovalController;
use App\Http\Controllers\Api\Admin\AdminLocationController;
use App\Http\Controllers\Api\Admin\AdminPendingEventsController;
use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Auth\MicrosoftAuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\CancellationController;
use App\Http\Controllers\Api\EventServiceController;
use App\Http\Controllers\Api\Staff\EventNoteController;
use App\Http\Controllers\Api\Staff\MyAssignmentsController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\Admin\AdminPhotographyTypeController;
use Illuminate\Support\Facades\Route;


///////////////Auth///////////////////////
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

////////////Microsoft Auth///////////////////////
Route::get('/auth/microsoft', [MicrosoftAuthController::class, 'redirect']);
Route::get('/auth/microsoft/callback', [MicrosoftAuthController::class, 'callback']);

// Locations
Route::get('/locations', [LocationController::class, 'index']);
Route::get('/photography-types', [\App\Http\Controllers\Api\PhotographyTypeController::class, 'index']);

//////////Events////////////////
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/events', [EventController::class, 'store']);
    Route::get('/events/{event}', [EventController::class, 'show']);
    Route::put('/events/{event}', [EventController::class, 'update']);
    Route::post('/events/{event}/cancel', [CancellationController::class, 'store']);
    Route::get('/my-bookings', [EventController::class, 'myBookings']);
});


Route::middleware(['auth:sanctum', 'role:Admin'])->patch('/cancellations/{cancellationRequest}', [
    CancellationController::class,
    'handle'
]);

/////////////Event Service//////////////////
Route::middleware('auth:sanctum')->post('/events/{event}/services', [EventServiceController::class, 'store']);


///////////////Dashboard - Admin//////////////////////////

Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {
    Route::get('/admin/calendar-view', [AdminCalendarController::class, 'index']);
    Route::get('/admin/calendar-overview', [AdminCalendarOverviewController::class, 'index']);
    Route::get('/admin/pending-events', [AdminPendingEventsController::class, 'index']);
    Route::prefix('admin/events')->group(function () {
        Route::post('{event}/approve', [AdminEventApprovalController::class, 'approve']);
        Route::post('{event}/reject', [AdminEventApprovalController::class, 'reject']);
    });
    Route::get('/admin/bookings', [AdminAllBookingsController::class, 'index']);
    Route::get('/admin/users', [AdminUserController::class, 'index']);
    Route::prefix('admin/locations')->group(function () {
        Route::post('/', [AdminLocationController::class, 'store']);
        Route::put('/{location}', [AdminLocationController::class, 'update']);
        Route::delete('/{location}', [AdminLocationController::class, 'destroy']);
    });
});

Route::middleware(['auth:sanctum', 'role:Admin|Super Admin'])->group(function () {
    Route::put('/admin/users/{user}/role', [AdminUserController::class, 'updateRole']);
});

Route::middleware(['auth:sanctum', 'role:Admin|Super Admin'])->prefix('admin/photography-types')->group(function () {
    Route::post('/', [AdminPhotographyTypeController::class, 'store']);
});

/////////Dashboard - Staff//////////////
Route::middleware(['auth:sanctum', 'role:Catering|Photography|Security'])->group(function () {
    Route::get('/my-assignments', [MyAssignmentsController::class, 'index']);
    Route::post('/event-services/{id}/note', [EventNoteController::class, 'store']);
    Route::get('/event-services/{id}', [EventServiceController::class, 'show']);
    Route::post('/event-services/{id}/accept', [EventServiceController::class, 'accept']);
    Route::post('/event-services/{id}/reject', [EventServiceController::class, 'reject']);
});
