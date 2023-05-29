<?php

namespace App\Util;

use DateTime;
use Illuminate\Support\Facades\DB;

class HelperUtil
{
    public static function dataArry()
    {

        $data =  date('Y');
        $intData = intval($data);
        $periodo = 1;
        $dataInit = strval($intData - $periodo);
        $dataFinal = $data;

        $array = [
            "ano_init" => $dataFinal,
            "ano_final" => $dataInit
        ];

        return $array;
    }
    public static function removerCaracteres($string)
    {
        return str_replace('"', "'", $string);
    }

    public static function setData($data)
    {
        $date = new DateTime($data);
        return $date->format('Y-m-d H:i:s');
    }

    public static function removerMascara($cnpj)
    {
        return str_replace('/', '', str_replace('.', '', str_replace('-', '', $cnpj)));
    }
    public static function getCred($cnpj ='')
    {
        $select =  DB::select('select * from lojas limit 1');

        $usuario = [
            'email' => env('SECRET_CONFIG_USERNAME'),
            'password' => env('SECRET_CONFIG_PASS'),
            'cnpj_cliente' =>  !empty($cnpj) ? HelperUtil::removerMascara($cnpj) : $select[0]->cnpj_cliente
        ];
        return $usuario;
    }

    public static function getCredLoja($id)
    {
        $select = DB::select('select * from lojas limit 1');

        if (!empty($select)) {
            $loja =
                [
                    "cnpj_cliente" => HelperUtil::removerMascara($select[0]->cnpj_cliente),
                    "nome_loja" => $select[0]->nome_loja,
                    "cnpj_loja" => HelperUtil::removerMascara($select[0]->cnpj_loja),
                    "id_cliente" => $id

                ];
            return $loja;
        }
        var_dump(['success' => false,"error" => "loja não cadastrada na base de dados"]);
        die();
    }

    public static function verificarInternet()
    {

        $url = 'http://www.google.com';
        $headers = @get_headers($url);
        if ($headers && strpos($headers[0], '200')) {
            return true;
        } else {
            return false;
        }
    }

    public static function configDelet()
    {
        $select = DB::select('select * from lojas limit 1');

        return [
            'cnpj_cliente' => HelperUtil::removerMascara($select[0]->cnpj_cliente),
            'cnpj_loja' => HelperUtil::removerMascara($select[0]->cnpj_loja)
        ];
    }
}
