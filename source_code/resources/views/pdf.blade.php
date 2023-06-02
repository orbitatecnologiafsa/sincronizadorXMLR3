<!DOCTYPE html>
<html>
{{ date_default_timezone_set('America/Sao_Paulo') }}

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Relatorio de notas {{$pasta}}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            margin: 1cm;
            font-family: Arial, Helvetica, sans-serif
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            border-color: #f2f2f2;
        }

        th,
        td {
            padding: 8px;
            border: none;
            /* Remover bordas */
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body class="main">
    <header style="margin-top: 50px;">
        <h1 style="text-align: center;">Relação de nota fiscal de saída</h1>
        <h5 style="text-align: center;">{{ $nome_mercado }}</h5>
        <table>
            <td style="color:black; font-size:15px; text-align: left;">
                Movimento : saída
            </td>
            <td style="color:black; font-size:15px; text-align: right; margin-bottom:5px;">
                Emissão : {{ date('d/m/Y H:i:s') }}
            </td>
        </table>

    </header>

    <div class="container">


        <main class="main">
            <table>
                <thead>
                    <tr>
                        <th>Chave de acesso</th>
                        <th>Valor</th>
                        <th>Mod</th>
                        <th>Data de emissão</th>
                        <th>Data de saída</th>
                        <th>Número</th>
                        <th>Série</th>
                        <th>Cfop</th>
                        <th>Sit</th>
                        <!-- Adicione mais cabeçalhos conforme necessário -->
                    </tr>
                </thead>
                {{ $qtdNota = 0, $totalNota = 0 }}
                <tbody>
                    @foreach ($report as $item)
                        <tr>
                            <td>{{ $item['chaveAcesso'] }}</td>
                            <td style="width:60px;">
                                {{ "R$ " . number_format(doubleval($item['valor']), 2, ',', '.') }}</td>
                            <td>{{ $item['mode'] }}</td>
                            <td>{{ date('d/m/y H:i:s', strtotime($item['dataEmissao'])) }}</td>
                            <td>{{ date('d/m/y H:i:s', strtotime($item['dataEmissao'])) }}</td>
                            <td>{{ $item['numNota'] }}</td>
                            <td>{{ $item['serie'] }}</td>
                            <td>{{ $item['cfop'] }}</td>
                            <td>{{ $item['sit'] }}</td>
                        </tr>
                        {{ $qtdNota += 1 }}
                        {{ $totalNota += doubleval($item['valor']) }}
                    @endforeach
                </tbody>
                {{-- <h1>quantidade de notas {{ $qtdNota }}</h1><br>
        <h1>total de notas {{ $totalNota }}</h1> --}}
            </table>
        </main>
        <main class="main" style="margin-top: 20px;">

            <table>
                <td style="color:black; font-size:20px; text-align: left;">
                    Quantidade de notas {{ number_format($qtdNota) }}
                </td>
                <td style="color:black; font-size:20px; text-align: right;">
                    Total nota R$ {{ number_format($totalNota, 2, ',', '.') }}
                </td>

            </table>
        </main>
    </div>
</body>

</html>
