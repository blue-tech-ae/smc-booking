<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEventNoteRequest;
use App\Services\EventNoteService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Post(
 *     path="/api/event-services/{id}/note",
 *     summary="Add a note to an assigned event service",
 *     tags={"Staff - Assignments"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the Event Service",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"note"},
 *             @OA\Property(property="note", type="string", example="Security cameras were installed.")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Note added successfully")
 * )
 */
class EventNoteController extends Controller
{
    protected EventNoteService $service;

    public function __construct(EventNoteService $service)
    {
        $this->service = $service;
    }

    public function store(StoreEventNoteRequest $request, int $id): JsonResponse
    {
        $note = $this->service->store($id, $request->user()->id, $request->note);

        return response()->json([
            'message' => 'Note added successfully',
            'data' => $note,
        ]);
    }
}
