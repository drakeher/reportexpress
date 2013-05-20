<?php

namespace ReportExpress\Core;

/**
 * ReportExpress
 *
 * @package		ReportExpress
 * @author		Sparkle Team
 * @copyright           Copyright (c) 2013, ReportExpress.
 * @license		http://reportexpress.com/license.html
 * @link		http://reportexpress.com
 * @since		Version 1.0
 * @filesource          ReportExpress/Core/Point.php
 */
class Point {

    /**
     * La posicion x.
     * @var int 
     */
    public $x;

    /**
     * La posicion y.
     * @var int 
     */
    public $y;

    /**
     * El maximo de la banda.
     * @var int 
     */
    public $mxb;

    public function __construct($x, $y, $mxb = 0) {
        $this->x = $x;
        $this->y = $y;
        $this->mxb = $mxb;
    }

}

?>
