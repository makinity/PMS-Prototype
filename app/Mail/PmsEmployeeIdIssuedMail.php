<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PmsEmployeeIdIssuedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $name,
        public string $employeeId,
        public string $email,
    ) {
        //
    }

    public function build(): static
    {
        return $this->subject('Your PMS Employee ID')
            ->view('emails.pms-employee-id-issued')
            ->with([
                'name' => $this->name,
                'employeeId' => $this->employeeId,
                'email' => $this->email,
            ]);
    }
}
