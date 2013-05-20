<?php

namespace ReportExpress\Component;

/**
 * Description of Image
 *
 * @author Wickedsick
 */
class Image extends Component {

    public function __construct($data) {
        parent::__construct($data);
    }

    /**
     * El valor puede ser variado, una direccion de imagen, un valor calculado 
     * o simplemente el nombre. Implementado solo el ultimo.
     * 
     * @return string El valor.
     */
    public function imageExpression() {
        return (string) $this->data->imageExpression;
    }

    /**
     * Rendere el componente image.
     * 
     * @param \ReportExpress\Core\ReportExpress $report El reporte donde se renderea.
     * @param int $x La x del punto.
     * @param int $y La y del punto
     */
    public function render($report, $x, $y) {
        $report->get('pdf')->Image($report->get('path') . $report->analyse($this->imageExpression()), $this->x() + $x, $this->y() + $y, $this->width(), $this->height());
    }

}

?>
