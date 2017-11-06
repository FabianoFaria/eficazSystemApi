<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrcamentosFechadosMailable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $address    = 'noreply@sistema.eficazsystem.com.br';
        $name       = 'Sistema EficazSystem';
        $subject    = 'Aviso de fechamento de orçamento';

        /*
         *
         *->cc($address, $name)
         *->bcc($address, $name)
         *->replyTo($address, $name)
         *
         */

        return $this->view('emails.aviso_faturamento')
                    ->with(['dadosVendedor' => $this->data])
                    ->from($address, $name)
                    ->subject($subject);
    }
}
