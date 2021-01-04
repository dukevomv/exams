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
        return $given.'/'.$total.' ('.intval(($given/$total)*100).'%)';
    }
}
