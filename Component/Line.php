<?php

namespace ReportExpress\Component;

/**
 * Description of Line
 *
 * @author Wickedsick
 */
class Line extends GraphicProperty {

    public function __construct($data) {
        parent::__construct($data);
    }

    /**
     * Rendere el componente ellipse.
     * 
     * @param \ReportExpress\Core\ReportExpress $report El reporte donde se renderea.
     * @param int $x La x del punto.
     * @param int $y La y del punto
     * @return boolean TRUE o NULL. 
     */
    public function render($report, $x, $y) {
        $x1 = $this->x() + $x;
        $y1 = $this->y() + $y;

        $x2 = $x1 + $this->width() + ($this->width() == 1 ? -1 : 0);
        $y2 = $this->height() == 1 ? $y1 : $y1 + $this->height();

        if ($this->directionBottomUp()) {
            $report->get('pdf')->Line($x1, $y2, $x2, $y1, $this->getLineStyle());
            if ($this->lineStyle() == 'Double') {
                $report->get('pdf')->Line($x1, $y2 + 2, $x2, $y1 + 2, $this->getLineStyle());
            }
        } else {
            $report->get('pdf')->Line($x1, $y1, $x2, $y2, $this->getLineStyle());
            if ($this->lineStyle() == 'Double') {
                $report->get('pdf')->Line($x1, $y1 + 2, $x2, $y2 + 2, $this->getLineStyle());
            }
        }

        return TRUE;
    }

    /**
     * Si ha sido cambiada la direccion por defecto es TopDown y no se 
     * escribe la propiedad en el jrxml.
     *  
     * @return boolean TRUE o FALSE.
     */
    public function directionBottomUp() {
        return isset($this->data['direction']);
    }

}

?>
