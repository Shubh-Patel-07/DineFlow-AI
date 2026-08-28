<?php

namespace App\Mail;

use App\Models\DemoRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewLeadNotification extends Mailable
{
    use Queueable, SerializesModels;

    public DemoRequest $lead;

    /**
     * Create a new message instance.
     */
    public function __construct(DemoRequest $lead)
    {
        $this->lead = $lead;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = "🔔 New Enquiry from " . ($this->lead->restaurant_name ?: $this->lead->name) . " — Tendorabuy";

        return $this->subject($subject)
                    ->view('emails.new-lead');
    }
}
