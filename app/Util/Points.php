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
        $percentage = ($total === 0) ? 0 : intval(($given / $total) * 100);
        return $given . '/' . $total . ' (' . $percentage . '%)';
    }
}
