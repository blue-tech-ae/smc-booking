<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterCalendarRequest;
use App\Services\AdminCalendarService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @OA\Get(
 *     path="/api/admin/calendar-view",
 *     summary="Admin calendar view with filters",
 *     tags={"Admin - Calendar"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string", enum={"draft","pending","service_approved","approved","rejected","cancelled"})
 *     ),
 *     @OA\Parameter(
 *         name="location",
 *         in="query",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="date",
 *         in="query",
 *         required=false,
 *         description="Format: YYYY-MM-DD",
 *         @OA\Schema(type="string", example="2025-06-10")
 *     ),
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         required=false,
 *         description="Keyword to search in title or details",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="role",
 *         in="query",
 *         required=false,
 *         description="Filter events by required service",
 *         @OA\Schema(type="string", enum={"catering","photography","security"})
 *     ),
 *     @OA\Response(response=200, description="List of events"),
 * )
 */
class AdminCalendarController extends Controller
{
    protected AdminCalendarService $calendarService;

    public function __construct(AdminCalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    public function index(FilterCalendarRequest $request): JsonResponse
    {
        $user = $request->user();
        $userRole = $user->roles->pluck('name')->first();

        $filters = $request->validated();
        $requestedRole = $filters['role'] ?? null;
        unset($filters['role']);

        if ($user->hasAnyRole(['Admin', 'Super Admin'])) {
            $events = $this->calendarService->getFilteredEvents($filters, $requestedRole ? ucfirst($requestedRole) : null);
        } else {
            $events = $this->calendarService->getFilteredEvents($filters, $userRole);
        }

        return response()->json(['data' => $events]);
    }
    
    public function export(FilterCalendarRequest $request): StreamedResponse
    {
        $user = $request->user();
        $userRole = $user->roles->pluck('name')->first();

        $filters = $request->validated();
        $requestedRole = $filters['role'] ?? null;
        unset($filters['role']);

        if ($user->hasAnyRole(['Admin', 'Super Admin'])) {
            $events = $this->calendarService->getFilteredEvents($filters, $requestedRole ? ucfirst($requestedRole) : null);
        } else {
            $events = $this->calendarService->getFilteredEvents($filters, $userRole);
        }

        $headers = ['Content-Type' => 'text/csv'];

        $callback = function () use ($events) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Title', 'Details', 'Start Time', 'End Time', 'Status', 'Location', 'Created By']);
            foreach ($events as $event) {
                fputcsv($file, [
                    $event->id,
                    $event->title,
                    $event->details,
                    $event->start_time,
                    $event->end_time,
                    $event->status,
                    $event->location,
                    optional($event->user)->name,
                ]);
            }
            fclose($file);
        };

        return response()->streamDownload($callback, 'events.csv', $headers);
    }
}