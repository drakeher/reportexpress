<?php

namespace ReportExpress\Component;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TextProperty
 *
 * @author osley.rivera
 */
abstract class GraphicProperty extends Component {

    public function __construct($data) {
        parent::__construct($data);
    }

    /**
     * Tipo de linea a usar.
     * 
     * @return string Tipo de linea.
     */
    public function lineStyle() {
        return isset($this->data->graphicElement->pen["lineStyle"]) ? (string) $this->data->graphicElement->pen["lineStyle"] : "Solid";
    }

    /**
     * Ancho de la linea.
     * 
     * @return float El valor.
     */
    public function lineWidth() {
        return isset($this->data->graphicElement->pen["lineWidth"]) ? (float) $this->data->graphicElement->pen["lineWidth"] : 1.0;
    }

    /**
     * Formato de linea compatible con TCPDF para los componentes.
     * 
     * @return array El formato de la linea.
     */
    public function getLineStyle() {

        $color = $this->lineColor();

        if (!$color) {
            $color = $this->fontColor();
        }

        return array(
            'width' => $this->lineWidth(),
            'cap' => 'butt', 'join' => 'miter',
            'dash' => $this->getDash(),
            'color' => $color
        );
    }

    /**
     * El color de la linea.
     * 
     * @return mixed El color en hexadecimal o NULL.
     */
    public function lineColor() {
        return isset($this->data->graphicElement->pen["lineColor"]) ? $this->data->graphicElement->pen["lineColor"] : NULL;
    }

    /**
     * Dash.
     * 
     * @return string|int Dash.
     */
    public function getDash() {
        switch ($this->lineStyle()) {
            case "Dashed":
                return "5,3";
                break;
            case "Dotted":
                return "1";
                break;
            default:// Solid, Double
                return 0;
                break;
        }
    }

}

?>
