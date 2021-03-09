<?php

namespace App\Util;

class Points {

    /**
     * @param $given
     * @param $total
     *
     * @return string
     */
    public static function getWithPercentage($given, $total) {
        return $given . '/' . $total . ' (' . self::getPercentage($given, $total) . '%)';
    }

    public static function getPercentage($given, $total) {
        return ($total === 0) ? 0 : intval(($given / $total) * 100);
    }

    public static function calcStandardDeviation($gradesArr){
        $num_of_elements = count($gradesArr);

        $variance = 0.0;

        // calculating mean using array_sum() method
        $average = array_sum($gradesArr)/$num_of_elements;

        foreach($gradesArr as $i)
        {
            // sum of squares of differences between
            // all numbers and means.
            $variance += pow(($i - $average), 2);
        }

        return round((float)sqrt($variance/$num_of_elements),2);
    }
}
