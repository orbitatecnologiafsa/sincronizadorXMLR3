<?php

namespace App\Models;

use App\Repositorio\UploadRepositorio;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    use HasFactory;
     protected $repositorio;

    public function __construct()
    {
       $this->repositorio = new UploadRepositorio();
    }

    public function cadastarXML()
    {
        $this->repositorio->envairXML();
    }

    public function gerarZipRelatorio(){
        $this->repositorio->gerarZipComRelatorio();
    }

}


// <?php

// use GuzzleHttp\Client;

// class UploadRepositorio
// {
//     public function envairXML()
//     {
//         // Obtenha os arquivos enviados pelo formulário
//         $arquivos = $request->file('arquivos');

//         // Crie uma instância do cliente Guzzle
//         $cliente = new Client();

//         // Array para armazenar as respostas da API
//         $respostas = [];

//         // Loop pelos arquivos
//         foreach ($arquivos as $arquivo) {
//             // Crie uma nova solicitação POST para enviar o arquivo para a API
//             $resposta = $cliente->request('POST', 'http://sua-api.com/upload', [
//                 'multipart' => [
//                     [
//                         'name'     => 'arquivo',
//                         'contents' => fopen($arquivo->getPathname(), 'r'),
//                         'filename' => $arquivo->getClientOriginalName(),
//                     ],
//                 ],
//             ]);

//             // Armazene a resposta da API
//             $respostas[] = $resposta->getBody()->getContents();
//         }

//         // Retorne as respostas da API
//         return $respostas;
//     }
// }


// <?php

// use Illuminate\Support\Facades\Http;

// class Up
// {
//     public function envairXML()
//     {
//         // Diretório contendo os arquivos a serem enviados
//         $diretorioArquivos = '/caminho/do/diretorio';

//         // URL da API para enviar os arquivos
//         $urlApi = 'http://exemplo.com/api/upload';

//         // Cria uma instância de PendingRequest para a API
//         $http = Http::post($urlApi);

//         // Itera sobre os arquivos do diretório
//         $arquivos = scandir($diretorioArquivos);
//         foreach ($arquivos as $arquivo) {
//             if ($arquivo !== '.' && $arquivo !== '..') {
//                 // Caminho completo do arquivo
//                 $caminhoArquivo = $diretorioArquivos . '/' . $arquivo;

//                 // Adiciona cada arquivo à solicitação
//                 $http = $http->attach(
//                     'arquivos[]',
//                     file_get_contents($caminhoArquivo),
//                     $arquivo
//                 );
//             }
//         }

//         // Envia a solicitação com os arquivos para a API
//         $response = $http->send();

//         // Verifica a resposta da API
//         if ($response->successful()) {
//             // Arquivos enviados com sucesso
//             return response()->json(['message' => 'Arquivos enviados com sucesso']);
//         } else {
//             // Ocorreu um erro ao enviar os arquivos
//             return response()->json(['message' => 'Erro ao enviar os arquivos'], $response->status());
//         }
//     }
// }




// use GuzzleHttp\Client;
// use GuzzleHttp\Exception\RequestException;
// use Illuminate\Http\Request;

// Route::post('/enviar-arquivos', function (Request $request) {
//     // Verifique se os arquivos foram enviados corretamente
//     if ($request->hasFile('arquivos')) {
//         $arquivos = $request->file('arquivos');

//         // Crie uma instância do cliente Guzzle
//         $cliente = new Client();

//         try {
//             // Faça a solicitação para a API para cada arquivo enviado
//             foreach ($arquivos as $arquivo) {
//                 $response = $cliente->post('http://sua-api.com/upload', [
//                     'headers' => [
//                         'Authorization' => 'Bearer SEU_TOKEN_DE_ACESSO',
//                     ],
//                     'multipart' => [
//                         [
//                             'name'     => 'arquivo',
//                             'contents' => fopen($arquivo->getPathname(), 'r'),
//                             'filename' => $arquivo->getClientOriginalName(),
//                         ],
//                         // Adicione outros campos do corpo (body) conforme necessário
//                         [
//                             'name'     => 'campo1',
//                             'contents' => 'valor1',
//                         ],
//                         [
//                             'name'     => 'campo2',
//                             'contents' => 'valor2',
//                         ],
//                     ],
//                 ]);

//                 // Processar a resposta da API, se necessário
//                 $statusCode = $response->getStatusCode();
//                 $responseData = $response->getBody()->getContents();

//                 // Faça o que desejar com a resposta da API
//                 // ...
//             }

//             // Retorne uma resposta adequada para o cliente
//             return response()->json(['message' => 'Arquivos enviados com sucesso'], 200);
//         } catch (RequestException $e) {
//             // Lidar com erros de solicitação
//             if ($e->hasResponse()) {
//                 $statusCode = $e->getResponse()->getStatusCode();
//                 $responseData = $e->getResponse()->getBody()->getContents();

//                 // Faça o que desejar com a resposta de erro da API
//                 // ...
//             }

//             // Retorne uma resposta de erro adequada para o cliente
//             return response()->json(['message' => 'Erro ao enviar os arquivos'], 500);
//         }
//     }

//     // Caso não tenha arquivos enviados
//     return response()->json(['message' => 'Nenhum arquivo enviado'], 400);
// });
