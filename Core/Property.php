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
 * @filesource          ReportExpress/Core/Property.php
 */
class Property {

    /**
     * Nombre del reporte.
     * @var string 
     */
    public $name;

    /**
     * Ancho de la pagina.
     * @var int 
     */
    public $pageWidth;

    /**
     * Altura de la pagina.
     * @var int 
     */
    public $pageHeight;

    /**
     * Orientacion de la pagina.
     * @var string 
     */
    public $orientation;

    /**
     * Margen izquierdo de la pagina.
     * @var int 
     */
    public $leftMargin;

    /**
     * Margen derecho de la pagina.
     * @var int 
     */
    public $rightMargin;

    /**
     * Margen superior de la pagina. 
     * @var int
     */
    public $topMargin;

    /**
     * Margen inferior de la pagina. 
     * @var int
     */
    public $bottomMargin;

    /**
     * Unidad de medida del reporte.
     * @var string 
     */
    public $unit = "pt";

    /**
     * Formato de la pagina.
     * @var string 
     */
    public $format = "A4";

    /**
     * Formato de caracteres.
     * @var boolean 
     */
    public $unicode = TRUE;

    /**
     * Si usaremos la cache.
     * @var boolean 
     */
    public $diskcache = TRUE;

    /**
     * Tipo de codigicacion.
     * @var string 
     */
    public $encoding = "UTF-8";

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
