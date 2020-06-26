<?php

namespace App\Helper;

use Carbon\Carbon;
use Carbon\Traits\Macro;

class Date
{
    /**
     * Formate to Database
     * @param $date
     * @return string
     */
    public static function formatToDataBase($date)
    {
        if(!empty($date)) {
            if(Carbon::hasFormat($date, 'Y-m-d')){
                return Carbon::createFromFormat('Y-m-d', $date)->format('Y-m-d');
            }
            return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        }
    }

    /**
     * Adiciona uma quantidade específica de meses
     * @param $date
     * @param $months
     * @return string
     */
    public static function addMonths($date, $months)
    {
        $date = Carbon::parse($date);
        return $date->addMonths($months)->toDateTimeString();
    }

    /**
     * Retorna a diferença dentre uma data e a outra
     * @param $dt_inicio
     * @param $dt_fim
     * @return int
     */
    public static function getHowMuchMonthsBetweenDates($dt_inicio, $dt_fim)
    {
        $start_date = Carbon::parse($dt_inicio);
        $end_date = Carbon::parse($dt_fim);
        $diffDate = $end_date->diffInMonths($start_date);
        return $diffDate;
    }

    /**
     * Formate to View
     * @param $date
     * @return string
     */
    public static function formatToView($date)
    {
        if(!empty($date)) {
            return Carbon::createFromFormat('Y-m-d', $date)->format('d/m/Y');
        }
    }
}
