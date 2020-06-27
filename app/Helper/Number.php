<?php

namespace App\Helper;

class Number
{
    public static function formatCurrencyBr($money, $toDataBase = true)
    {
        if (true === $toDataBase) {
            return self::moedaToDatabase($money);
        }
        return self::moedaToView($money);
    }

    public static function getOnlyNumber($string)
    {
        $numero = preg_replace("/[^0-9]/", '', $string);
        return $numero;
    }

    /**
     * to database moeda
     * @param $get_valor
     * @return mixed
     */
    public static function moedaToDatabase($get_valor)
    {
        $source = array('.', ',');
        $replace = array('', '.');
        $valor = str_replace($source, $replace, $get_valor); //remove os pontos e substitui a virgula pelo ponto
        return str_replace('R$', '', $valor);
    }

    /**
     * to view moeda
     * @param $get_valor
     * @return mixed
     */
    public static function moedaToView($get_valor)
    {
        return 'R$ ' . number_format($get_valor, 2, ',', '.');
    }

    public static function phoneToView($phone)
    {

        if (strlen($phone) === 10) {
            $phone = '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 4)
                . '-' . substr($phone, 6);

        }
        if (strlen($phone) === 11) {
            $phone = '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5)
                . '-' . substr($phone, 7);
        }
        return $phone;
    }
}
