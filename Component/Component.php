<?php

namespace ReportExpress\Component;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Component
 *
 * @author osley.rivera
 */
abstract class Component {

    /**
     * Datos del componente provenientes del jrxml.
     * @var \SimpleXMLElement 
     */
    protected $data = NULL;

    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * La x del punto donde se renderea.
     * 
     * @return int El valor.
     */
    public function x() {
        return (int) $this->data->reportElement['x'];
    }

    /**
     * La y del punto donde se renderea.
     * 
     * @return int El valor.
     */
    public function y() {
        return (int) $this->data->reportElement['y'];
    }

    /**
     * El ancho del componente.
     * 
     * @return int El valor.
     */
    public function width() {
        return (int) $this->data->reportElement['width'];
    }

    /**
     * El alto del componente.
     * 
     * @return int El valor.
     */
    public function height() {
        return (int) $this->data->reportElement['height'];
    }

    /**
     * Convierte un color en hexadecimal a RGB.
     * 
     * @param string $color El color en hexadecimal .
     * @return array El color en RGB.
     */
    public function toColor($color) {
        return array(hexdec(substr($color, 1, 2)), hexdec(substr($color, 3, 2)), hexdec(substr($color, 5, 2)));
    }

    /**
     * Devuelve el color de la letra.
     * 
     * @return array El color en RGB
     */
    public function fontColor() {
        return isset($this->data->reportElement["forecolor"]) ? $this->toColor((string) $this->data->reportElement["forecolor"]) : array(0, 0, 0);
    }

    /**
     * Devuelve el color de fondo.
     * 
     * @return array El color en RGB.
     */
    public function backgroundColor() {
        return isset($this->data->reportElement["backcolor"]) ? $this->toColor((string) $this->data->reportElement["backcolor"]) : array(255, 255, 255);
    }

    /**
     * Si usa transaparencia.
     * 
     * @return boolean TRUE o FALSE.
     */
    public function isTransparent() {
        return $this->data->reportElement["mode"] == "Transparent" ? TRUE : FALSE;
    }

    /**
     * Devuelve la posicion relativa del componente (FixRelativeToBottom,FixRelativeToTop,Float).
     * 
     * @return string La posicion.
     */
    public function positionType() {
        return isset($this->data->reportElement["positionType"]) ? $this->data->reportElement["positionType"] : 'Float';
    }

    /**
     * Devuelve el resultado de la expresion despues de ser analizada.
     * 
     * @param \ReportExpress\Core\ReportExpress $report El reporte donde se renderea.
     * @return boolean TRUE o FALSE
     */
    public function printWhenExpression($report) {
        return isset($this->data->reportElement->printWhenExpression) ? (boolean) $report->analyse((string) $this->data->reportElement->printWhenExpression) : TRUE;
    }

    /**
     * Devuelve el nombre del grupo.
     * 
     * @return string Nombre del grupo.
     */
    public function printWhenGroupChanges() {
        return (string) $this->data->reportElement['printWhenGroupChanges'];
    }

    /**
     * Implementado en cada componente.
     * 
     * @param \ReportExpress\Core\ReportExpress $report El reporte donde se renderea.
     * @param int $x La x del punto.
     * @param int $y La y del punto
     */
    abstract public function render($report, $x, $y);
}

?>
