<?php

namespace App\Mails;

use App\Models\Attraction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewAttractionMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Attraction $attraction;

    public function __construct(Attraction $attraction)
    {
        $this->attraction = $attraction;
    }

    public function build(): Mailable
    {
        $mailable = $this->view('emails.new-attraction')
            ->subject('New Attraction Created');

        if ($this->attraction->image) {
            $mailable->attach(storage_path('app/public/' . $this->attraction->image));
        }

        return $mailable;
    }
}
