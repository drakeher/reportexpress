<?php

namespace ReportExpress\Component;

/**
 * Description of Ellipse
 *
 * @author Wickedsick
 */
class Ellipse extends GraphicProperty {

    public function __construct($data) {
        parent::__construct($data);
    }

    /**
     * Rendere el componente ellipse.
     * 
     * @param \ReportExpress\Core\ReportExpress $report El reporte donde se renderea.
     * @param int $x La x del punto.
     * @param int $y La y del punto
     */
    public function render($report, $x, $y) {
        $report->get('pdf')->Ellipse($this->x() + $x + ($this->width() / 2), $this->y() + $y + ( $this->height() / 2), $this->width() / 2, $this->height() / 2, 0, 0, 360, $this->isTransparent() ? "D" : "DF", $this->getLineStyle(), $this->backgroundColor());
    }

}

?>
