<?php


namespace App\Repositorio;

use App\Util\HelperUtil;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;



class UploadRepositorio
{
    protected $url = "http://127.0.0.1:8000/api/";
    protected $certificado = "app/cacert.pem";
    // protected $diretorio = "C:\Orbita\R3 Núcleo\nfe";
    protected $diretorio = "/home/pingo/Documentos/Projetos/versao-ob/sincronizadorXMLR3/source_code/storage/app/xml";

    public function envairXML()
    {
        try {
            $nomepasta = ($this->pastasNomeGenerate());
            $access_token = $this->autenticar();

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
                // Diretório onde estão os arquivos a serem enviados
                $diretorio = ($this->diretorio . '/' . $value);
                if (File::exists($diretorio)) {
                    // Obtém a lista de arquivos no diretório
                    $arquivos = collect(File::files($diretorio));

                    //  $chunk = array_chunk($arquivos,100,true);
                    //  var_dump(count($arquivos) == 0);die();
                    if (count($arquivos) > 0) {

                        $chunkFiles = $arquivos->chunk(6);
                        // Cria um array para armazenar os arquivos
                        $arquivosData = [];
                        foreach ($chunkFiles as $chunk) {
                            foreach ($chunk as $arquivo) {
                                $arquivosData[] = [
                                    'name' => 'arquivo[]',
                                    'contents' => file_get_contents($arquivo->getPathname()),
                                    'filename' => $arquivo->getFilename(),
                                ];
                            }

                            $cliente = new Client([
                                'verify' => storage_path($this->certificado), // Caminho completo para o arquivo cacert.pem
                            ]);

                            // Faça a solicitação para a API enviando todos os arquivos de uma vez
                            $response = $cliente->post($this->url . 'uploadxml', [
                                'headers' => [
                                    'Authorization' => "Bearer $access_token",
                                    'cnpj_cliente' => $cnpj,
                                    'nome_pasta' => $value
                                ],
                                'multipart' =>
                                $arquivosData

                            ]);

                            // Processar a resposta da API, se necessário
                            $statusCode = $response->getStatusCode();
                            $responseData = $response->getBody()->getContents();

                            // Faça o que desejar com a resposta da API
                            // ...

                            // Retorne uma resposta adequada para o cliente
                            var_dump(['message' => 'Arquivos enviados com sucesso', "resposta" => $responseData], 200);
                        }
                    }
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
            date_default_timezone_set('America/Sao_Paulo');
            $hora_init = date('H:i:s');


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
            $hora_final =  date('H:i:s');
            echo "Serviço autenticar finalizado! \n time init {$hora_init}  time final {$hora_final} \n";
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
}
