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
        if ($this->verficarInternet()) {
            $upload  = new ModelsUpload();
            $upload->gerarZipRelatorio();
            $upload->cadastarXML();
            $hora_final =  date('H:i:s');
            echo "Serviço  finalizado! \n time init {$hora_init}  time final {$hora_final} \n";
        }else{
            var_dump(['error' => 'sem conexão com a internet']);
        }
    }


    public function verficarInternet()
    {
        $url = 'http://www.google.com';
        $headers = @get_headers($url);
        if ($headers && strpos($headers[0], '200')) {
            return true;
        } else {
            return false;
        }
    }
}
