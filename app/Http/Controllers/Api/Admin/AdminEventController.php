<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUpdateEventRequest;
use App\Http\Requests\StoreEventServiceRequest;
use App\Models\Event;
use App\Models\EventService as EventServiceModel;
use App\Services\EventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AdminEventController extends Controller
{
    public function __construct(protected EventService $eventService) {}

    /**
     * @OA\Put(
     *     path="/api/admin/events/{id}",
     *     summary="Update an event",
     *     tags={"Admin - Events"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/AdminUpdateEventRequest")
     *     ),
     *     @OA\Response(response=200, description="Event updated successfully")
     * )
     */
    public function update(AdminUpdateEventRequest $request, Event $event): JsonResponse
    {
        $data = $request->validated();

        $updated = $this->eventService->update($event, $data);

        foreach ($request->input('services', []) as $serviceData) {
            $validator = Validator::make(
                $serviceData,
                (new StoreEventServiceRequest())->rules($serviceData['service_type'] ?? null)
            );
            $validated = $validator->validate();
            $validated['event_id'] = $event->id;

            EventServiceModel::updateOrCreate(
                ['event_id' => $event->id, 'service_type' => $validated['service_type']],
                $validated
            );
        }

        return response()->json([
            'message' => 'Event updated successfully',
            'data' => $updated,
        ]);
    }
}
