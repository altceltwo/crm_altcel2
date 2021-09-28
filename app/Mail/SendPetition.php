<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendPetition extends Mailable
{
    use Queueable, SerializesModels;
    public $info;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->info = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->info['subject'];
        $name = $this->info['name'];
        $lastname = $this->info['lastname'];
        $comment = $this->info['comment'];
        $status = $this->info['status'];
        $remitente = $this->info['remitente'];
        $correo = $this->info['correo'];

        return $this->subject($subject)
                    ->view('mails.notificationActivation',$this->info)
                    ->with([
                            'subject' => $subject,
                            'name' =>$name,
                            'lastname' => $lastname,
                            'comment'=>$comment,
                            'status'=>$status,
                            'remitente'=>$remitente,
                            'correo'=>$correo
                        ]);
    }
}
