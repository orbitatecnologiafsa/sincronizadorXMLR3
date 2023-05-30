<?php

namespace App\Console\Commands;

use App\Models\Upload as ModelsUpload;
use Illuminate\Console\Command;

class upload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        date_default_timezone_set('America/Sao_Paulo');

        $hora_init = date('H:i:s');
        $upload  = new ModelsUpload();
        $upload->cadastarXML();
        $hora_final =  date('H:i:s');
        echo "Serviço  finalizado! \n time init {$hora_init}  time final {$hora_final} \n";

    }



}
