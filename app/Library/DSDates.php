<?php

/**
 * This file is part of RepoMapper Framework
 *
 * @copyright Copyright (c) 2015 McGarryIT
 * @link      (http://www.mcgarryit.com)
 */

namespace App\Library;

use Carbon\Carbon;

/**
 * Description of MyDateHandler
 *
 * @author Paul McGarry <mcgarryit@gmail.com>
 */
class DSDates {

    public static function monthEndDate() {
        $c = new Carbon;
        return $c->endOfMonth();
    }

    public static function nextMonthEndDate($dateString='') {
        $c = new Carbon($dateString);
        return $c->addMonth()->endOfMonth();
    }
}
