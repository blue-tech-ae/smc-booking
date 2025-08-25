<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class EventApprovalRequest extends Mailable
{
    use Queueable, SerializesModels;

    public Event $event;
    public string $approveUrl;

    public function __construct(Event $event)
    {
        $this->event = $event;
        $this->approveUrl = URL::signedRoute('events.email-approve', ['event' => $event->id]);
    }

    public function build(): self
    {
        return $this->subject('New Event Awaiting Approval')
            ->view('emails.event-approval');
    }
}