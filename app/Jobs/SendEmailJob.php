<?php

namespace App\Jobs;

use App\Models\Communication\Communication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $communication;
    protected $recipients;

    public function __construct(Communication $communication, array $recipients)
    {
        $this->communication = $communication;
        $this->recipients = $recipients;
    }

    public function handle()
    {
        foreach ($this->recipients as $recipient) {
            Mail::to($recipient)->send(new \App\Mail\Communication\Email($this->communication));
        }
    }
}
