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
        $upload  = new ModelsUpload();
        $upload->cadastarXML();
    }



}
