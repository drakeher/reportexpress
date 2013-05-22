<?php

namespace ReportExpress\Core;

/**
 * Point Class
 * 
 * Used for controlling the axis.
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Core
 * @version     1.0
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Creative Commons (CC) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
class Point {

    /**
     * The position (x).
     * @var int 
     */
    public $x;

    /**
     * The position (y).
     * @var int 
     */
    public $y;

    /**
     * The maximum of the band.
     * @var int 
     */
    public $mxb;
    
    /**
     * Constructor of the class.
     * 
     * @param int $x The position (x).
     * @param int $y The position (y).
     * @param int $mxb The maximum of the band.
     * @return void
     */
    public function __construct($x, $y, $mxb = 0) {
        $this->x = $x;
        $this->y = $y;
        $this->mxb = $mxb;
    }

}

?>
