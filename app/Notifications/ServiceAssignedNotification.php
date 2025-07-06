<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\EventService;

class ServiceAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected EventService $service;

    public function __construct(EventService $service)
    {
        $this->service = $service;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $event = $this->service->event;

        return (new MailMessage)
            ->subject('New Service Assignment')
            ->line('You have been assigned to a new service.')
            ->line('Event: ' . $event->title)
            ->line('Service Type: ' . ucfirst($this->service->service_type))
            ->line('Start: ' . $event->start_time)
            ->line('End: ' . $event->end_time);
    }
}
