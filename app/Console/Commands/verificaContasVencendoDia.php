<?php

namespace App\Console\Commands;

use DB;
use Mail;
use Illuminate\Console\Command;

class verificaContasVencendoDia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:verificaContasVencendoDia';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica as contas que irão vencer no dia e então envia um email listando com os principais detalhes de cada uma.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $this->line("Listando as contas que irão vencer no dia de hoje...");
        // $this->line("Some text");
        // $this->info("Hey, watch this !");
        // $this->comment("Just a comment passing by");
        // $this->question("Why did you do that?");
        // $this->error("Ops, that should not happen.");


        $listaContasVencendoDia = "";

        if(!empty($listaContasVencendoDia)){

        }else{

            $this->info('Nenhuma conta vencendo na data de hoje!');

        }
    }
}
