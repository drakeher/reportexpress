<?php

namespace ReportExpress\Core;

/**
 * Property Class
 * 
 * Used for controlling of the report.
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Core
 * @version     1.0 In development. Very unstable.
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Creative Commons (CC) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
class Property {

    /**
     * Name of report.
     * @var string 
     */
    public $name;

    /**
     * Width of page.
     * @var int 
     */
    public $pageWidth;

    /**
     * Height of page.
     * @var int 
     */
    public $pageHeight;

    /**
     * Orientation of the page.
     * @var string 
     */
    public $orientation;

    /**
     * Margin left of the page.
     * @var int 
     */
    public $leftMargin;

    /**
     * Margin right of the page.
     * @var int 
     */
    public $rightMargin;

    /**
     * Margin top of the page. 
     * @var int
     */
    public $topMargin;

    /**
     * Margin bottom of the page.
     * @var int
     */
    public $bottomMargin;

    /**
     * Measurement unit
     * @var string 
     */
    public $unit = "pt";

    /**
     * Format of page
     * @var string 
     */
    public $format = "A4";

    /**
     * Format of character.
     * @var boolean 
     */
    public $unicode = TRUE;

    /**
     * Use cache
     * @var boolean 
     */
    public $diskcache = TRUE;

    /**
     * Encoding
     * @var string 
     */
    public $encoding = "UTF-8";
    
    /**
     * Constructor of the class.
     * 
     * @param \SimpleXmlElment $xml XML.
     */
    public function __construct($xml) {
        $this->name = (string) $xml["name"];
        $this->orientation = isset($xml["orientation"]) ? substr((string) $xml["orientation"], 0, 1) : 'P';
        $this->pageWidth = (string) $xml["pageWidth"];
        $this->pageHeight = (string) $xml["pageHeight"];
        $this->leftMargin = (string) $xml["leftMargin"];
        $this->rightMargin = (string) $xml["rightMargin"];
        $this->topMargin = (string) $xml["topMargin"];
        $this->bottomMargin = (string) $xml["bottomMargin"];
        $this->columnWidth = (string) $xml["columnWidth"];
    }

}

?>
