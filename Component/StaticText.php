<?php

namespace ReportExpress\Component;

/**
 * Description of StaticText
 *
 * @author Wickedsick
 */
class StaticText extends TextProperty {

    public function __construct($data) {
        parent::__construct($data);
    }

    /**
     * Rendere el componente staticText.
     * 
     * @param \ReportExpress\Core\ReportExpress $report El reporte donde se renderea.
     * @param int $x La x del punto.
     * @param int $y La y del punto
     * @return boolean TRUE o NULL.
     */
    public function render($report, $x, $y) {

        //estilo 
        $report->get('pdf')->SetFont($this->fontName(), $this->fontStyle(), $this->size());

        //color 
        $color = $this->fontColor();
        $report->get('pdf')->SetTextColor($color[0], $color[1], $color[2]);

        //rotacion
        if ($this->needRotate() == 1) {
            $report->get('pdf')->StartTransform();
            $report->get('pdf')->Rotate($this->angle(), $this->x() + $x, $this->y() + $y);
            $report->get('pdf')->StopTransform();
        }

        //background
        $bgcolor = $this->backgroundColor();
        $report->get('pdf')->SetFillColor($bgcolor[0], $bgcolor[1], $bgcolor[2]);

        //celda       
        $report->get('pdf')->MultiCell($this->width(), $this->height(), (string) $this->data->text, 0, $this->aling(), 1, 1, $this->x() + $x, $this->y() + $y, TRUE, 0, FALSE, TRUE, $this->isStretchWithOverflow() == 0 ? $this->height() + 1 : 0);

        return TRUE;
    }

}

?>
