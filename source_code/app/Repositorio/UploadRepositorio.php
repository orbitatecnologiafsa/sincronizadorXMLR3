<?php


namespace App\Repositorio;

use App\Util\HelperUtil;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Barryvdh\DomPDF\Facade\Pdf as Pdf;




class UploadRepositorio
{
    protected $url = "http://192.168.0.103:9090/api/";
    protected $certificado = "app/cacert.pem";
    protected $dirSaveZip = '//home//pingo//Documentos//sincZipXML';
    // protected $diretorio = "C:\Orbita\R3 Núcleo\nfe";
    protected $diretorio = "//home//pingo//Documentos//XML mar 23//Core3//C3 Núcleo//nfe/NFCe";

    public function envairXML()
    {
        try {
            $nomepasta = ($this->pastasNomeGenerate());
            $access_token = $this->autenticar();
            //var_dump($access_token);die();
            $pasta = '';

            // if(!File::exists(storage_path($this->diretorio))){
            //     File::makeDirectory(storage_path($this->diretorio),0755);
            // }

            $cnpj = $this->getCNPJ();
            if (empty($cnpj)) {
                var_dump(['error' => 'cnpj não encontrado']);
                die();
            }

            foreach ($nomepasta as $key => $value) {
                echo $value . "\n";
                $dirZip = $this->getNameZips($value, true);
                if (File::exists($dirZip)) {
                    echo "$value zip enviando\n";
                    $name = $value ."-" .$this->getNameZips('',false,true) ;

                    $cliente = new Client([
                        'verify' => storage_path($this->certificado), // Caminho completo para o arquivo cacert.pem
                    ]);

                    $response = $cliente->post($this->url . "uploadxml/$value/$cnpj", [

                        'headers' => [
                            'Authorization' => "Bearer $access_token",
                            'cnpj_cliente' => $cnpj,
                            'nome_pasta' => $value
                        ],
                        'multipart' =>
                        [
                            [
                                'name'     => 'arquivo',
                                'contents' => fopen($dirZip, 'r'),
                                'filename' => $name
                            ],
                        ]

                    ]);

                    // Processar a resposta da API, se necessário
                    $statusCode = $response->getStatusCode();
                    $responseData = $response->getBody()->getContents();

                    // Faça o que desejar com a resposta da API
                    // ...
                    File::delete($dirZip);
                    // Retorne uma resposta adequada para o cliente
                    var_dump(['message' => 'Arquivos enviados com sucesso', "resposta" => $responseData], 200);
                }


            }
        } catch (Exception $e) {
            // Lidar com erros de solicitação
            var_dump($e->getMessage());

            // ...
            // Retorne uma resposta de erro adequada para o cliente

        }
    }

    public function pastasNomeGenerate()
    {
        date_default_timezone_set('America/Sao_Paulo');
        $intMes = date('m'); //05
        $ano = date('Y');
        if ($intMes == 1) {
            $intAno = intval(date('Y'));
            $ano = strval($intAno - 1);
            $array = [
                "mes1" => $ano . '10',
                "mes2" => $ano . '11',
                "mes3" => $ano . '12',
            ];
        } else if ($intMes == 2) {
            $intAno = intval(date('Y'));
            $ano = strval($intAno - 1);
            $array = [
                "mes1" => $ano . '11',
                "mes2" => $ano . '12',
            ];
            $ano = date('Y');
            $array["mes3"] = $ano . '01';
        } else if ($intMes == 3) {
            $intAno = intval(date('Y'));
            $ano = strval($intAno - 1);
            $array = [
                "mes1" => $ano . '12',
            ];
            $ano = date('Y');
            $array["mes2"] = $ano . '01';
            $array["mes3"] = $ano . '02';
        } else {

            $mes1 = $this->validarMes(strval($intMes - 1));
            $mes2 = $this->validarMes(strval($intMes - 2));
            $mes3 = $this->validarMes(strval($intMes - 3));
            $ano = date('Y');

            $array = [
                "mes1" => $ano . $mes1,
                "mes2" => $ano . $mes2,
                "mes3" => $ano . $mes3,
            ];
        }


        return $array;
    }

    public function validarMes($mes)
    {

        if (intval($mes) <= 9) {
            return ('0' .  strval($mes));
        }
        return $mes;
    }

    public function autenticar()
    {
        try {
            $this->getCNPJ();

            //autenticador da api
            $cliente  = new Client([
                'verify' => storage_path($this->certificado), // Caminho completo para o arquivo cacert.pem
            ]);
            $response = $cliente->post($this->url . 'auth/login', [
                "form_params" => [
                    "email" => env('SECRET_CONFIG_USERNAME'),
                    "password" => env('SECRET_CONFIG_PASS')
                ]
            ]);
            $responseData = json_decode($response->getBody(), true);
            $access_token = $responseData['access_token'];

            if (!empty($access_token)) {
                return $access_token;
            } else {
                return '';
            }
        } catch (\Throwable $th) {
            return (['error' => $th->getMessage()]);
        }
    }

    public function getCNPJ()
    {

        $cnpj = DB::select('select "CNPJ" as cnpj from "C000004" limit 1');
        if (!empty($cnpj)) {
            return HelperUtil::removerMascara($cnpj[0]->cnpj);
        }
        return '';
    }

    public function gerarZipComRelatorio()
    {


        //var_dump($this->validZip(''));die();
        $nomepasta = ($this->pastasNomeGenerate());
        $cnpj = $this->getCNPJ();
        if (empty($cnpj)) {
            var_dump(['error' => 'cnpj não encontrado']);
            die();
        }
        foreach ($nomepasta as $key => $value) {
            $diretorio = ($this->diretorio . '//' . $value);
            if (File::exists($diretorio)) {
                //gerar relatorio
                $this->gearRelatorio($value, $cnpj, $diretorio);
            }
        }
    }

    public function gerarZip($pasta, $cnpj, $nomeMercado = '-')
    {

        $namePastZip = $this->dirSaveZip;
        if (!File::exists(($namePastZip))) {
            $dirPas_um = ($namePastZip);
            echo " criando pasta em $namePastZip \n";
            File::makeDirectory($dirPas_um, 0755);
        }

        //    if (!file_exists("$namePastZip/$cnpj-$pasta-$nomeMercado.zip")) {
        $directory = $this->diretorio . "//$pasta"; // Insira o caminho para o diretório que contém os arquivos que deseja compactar
        $zipFile = ("$namePastZip//$pasta-$cnpj-$nomeMercado.zip"); // Caminho para salvar o arquivo RAR

        $zip = new ZipArchive();
        $zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($directory) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();

        $zipFilePath = ("$namePastZip//$pasta-$cnpj-$nomeMercado.zip"); // Caminho completo para o arquivo zip dentro da pasta "public"
        echo " salvando zip no caminho $zipFilePath\n";
        // Verifica se o arquivo zip existe
        if (file_exists($zipFilePath)) {
            // Gera o URL para o arquivo zip usando a função asset()
            echo "$pasta zip gerado\n";
            return true;
        }

        // Se o arquivo zip não existir, retorne uma mensagem de erro
        echo "$pasta zip error ao gerar\n";
        return false;
        //  }
        //return false;
    }

    public function gearRelatorio($pasta, $cnpj, $diretorio)
    {
        $report = [];
        $valor = '';
        $chaveAcesso = '';
        $mod = '';
        $dataEmissao = '';
        $dataRecebe = '';
        $serie = '';
        $numNota = '';
        $cfop = '';
        $sit = '';
        $nomeMercado = '';
        $xmlFilesPath = $diretorio;
        $pdfFilesPath = $diretorio;
        $xmlFiles = File::files($xmlFilesPath);

        foreach ($xmlFiles as $xmlFile) {
            libxml_use_internal_errors(TRUE);
            if ($xmlFile->getExtension() != 'pdf') {
                $xmlContent = file_get_contents($xmlFile->getPathname());
                $xml =  (array) simplexml_load_string($xmlContent, 'SimpleXMLElement', LIBXML_NOCDATA);
                foreach ($xml as $key => $v) {
                    $ar = (array) $v;
                    echo "lendo arq. pasta $pasta\n";
                    if (isset($ar['infProt'])) {
                        $chaveAcesso = $ar['infProt']->chNFe;
                        $dataRecebe =  $ar['infProt']->dhRecbto;
                    }
                    if (isset($ar['infNFe'])) {
                        $valor = $ar['infNFe']->total->ICMSTot->vNF;
                        $mod = $ar['infNFe']->ide->mod;
                        $dataEmissao = $ar['infNFe']->ide->dhEmi;
                        $serie = $ar['infNFe']->ide->serie;
                        $numNota = $ar['infNFe']->ide->nNF;
                        $cfop = $ar['infNFe']->det->prod->CFOP;
                        $sit = $ar['infNFe']->ide->procEmi;
                        $nomeMercado = $ar['infNFe']->emit->xFant;
                    }
                }

                $report[] = [
                    'chaveAcesso' => $chaveAcesso,
                    'valor' => $valor,
                    'mode' => $mod,
                    'dataEmissao' => $dataEmissao,
                    'dataRecebe' => $dataRecebe,
                    'serie' => $serie,
                    'numNota' => $numNota,
                    'cfop' => $cfop,
                    'sit' => $sit
                ];
            }
        }

        $arr = (array) $report;
        $uni = array_unique($arr, SORT_REGULAR);
        $pdf = Pdf::setPaper('a4')->loadView('pdf', ['report' => $uni, 'nome_mercado' => $nomeMercado, 'pasta' => $pasta]);
        $pdf->save($pdfFilesPath . "//relatorio-sinc-$pasta-$nomeMercado.pdf");
        echo $pasta . " gerado pdf \n";
        echo $pasta . " qtd de arquvios " . count($uni) . " \n";
        $this->gerarZip($pasta, $cnpj, $nomeMercado);
        return true;
    }

    public function getNameZips($parte = '', $patch = false, $name = false)
    {

        $storagePath = $this->dirSaveZip;

        $dirInfo = '';
        $nameZip = '';
        // Caminho para o diretório de armazenamento
        if (File::exists($storagePath)) {
            $directories = File::files($storagePath);
            $pastas = [];
            foreach ($directories as $directory) {

                if (pathinfo($directory->getExtension() === 'zip')) {
                    if (str_contains($directory->getFilename(), $parte)) {
                        $dirInfo = $directory->getRealPath();
                        $nameZip = substr($directory->getFilename(), 7);
                    }

                    $pastas[] = substr($directory->getFilename(), 0, 6);
                }
            }
            if ($patch == true) {
                return $dirInfo;
            }
             if($name == true){

                return $nameZip;
            }
            return $pastas;
        }
        return [];
    }


    // public function validZip($pasta)
    // {

    //     $pastas = $this->getNameZips();
    //     $res = false;
    //     foreach ($pastas as $p => $v) {
    //         if ($pasta == $v) {
    //             $res = true;
    //         }
    //         $res = false;
    //     }

    //     return $res;
    // }
}
