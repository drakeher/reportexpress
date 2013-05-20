<?php

namespace ReportExpress\Component;

/**
 * Description of Rectangle
 *
 * @author Wickedsick
 */
class Rectangle extends GraphicProperty {

    public function __construct($data) {
        parent::__construct($data);
    }

    /**
     * Rendere el componente rectangle.
     * 
     * @param \ReportExpress\Core\ReportExpress $report El reporte donde se renderea.
     * @param int $x La x del punto.
     * @param int $y La y del punto
     */
    public function render($report, $x, $y) {
        $borderStyle = $this->getLineStyle();
        $style = $this->isTransparent() ? "D" : "DF";
        $radius = $this->getRadius();

        if ($radius > 0) {
            $report->get('pdf')->RoundedRect($this->x() + $x, $this->y() + $y, $this->width(), $this->height(), $radius, "1111", $style, $borderStyle, $this->backgroundColor());
        } else {
            $report->get('pdf')->Rect($this->x() + $x, $this->y() + $y, $this->width(), $this->height(), $style, array('all' => $borderStyle), $this->backgroundColor());
        }
    }

    /**
     * El radio usado para las esquinas del rectangulo.
     * 
     * @return float El valor.
     */
    public function getRadius() {
        return isset($this->data["radius"]) ? (float) $this->data["radius"] : 0;
    }

}

?>
