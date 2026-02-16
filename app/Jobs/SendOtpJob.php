<?php

namespace App\Jobs;

use App\Mail\OtpMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOtpJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $otp;
    protected $type;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $otp, $type = 'registration')
    {
        $this->email = $email;
        $this->otp = $otp;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->email)->send(new OtpMail($this->otp, $this->type));
    }
}
