<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailNotify extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;

    /** @var array<int, array{path:string,name?:string,mime?:string}> */
    protected $fileAttachments;

    /**
     * @param array $data
     * @param array<int, array{path:string,name?:string,mime?:string}> $fileAttachments
     */
    public function __construct($data, array $fileAttachments = [])
    {
        $this->data = $data;
        $this->fileAttachments = $fileAttachments;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->from(get_option('MAIL_FROM_ADDRESS'), get_option('app_name'))
            ->subject($this->data['subject'])
            ->markdown('mail.email-notify')
            ->with($this->data);

        foreach ($this->fileAttachments as $file) {
            if (empty($file['path']) || !is_file($file['path'])) {
                continue;
            }

            $mail->attach($file['path'], [
                'as' => $file['name'] ?? basename($file['path']),
                'mime' => $file['mime'] ?? 'application/pdf',
            ]);
        }

        return $mail;
    }
}
