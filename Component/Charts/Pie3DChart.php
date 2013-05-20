<?php

namespace ReportExpress\Component\Charts;

/**
 * Description of Pie3DChart
 *
 * @author humbe
 */
class Pie3DChart extends PieChart{
    
    function __construct($plot, $dataSet, $chart) {
        parent::__construct($plot, $dataSet, $chart);
    }
    
    /**
     * Devuelve el valor de la propiedad depthFactor (número entre 0 y 100)
     * o FALSE si no está configurada esta opción.
     * 
     * @return mixed
     */
    public function depthFactor()
    {
        $depthFactor = isset($this->plot['depthFactor']) ? (float) $this->plot['depthFactor'] : FALSE;
        if ($depthFactor) 
            return $depthFactor*100;
        return 25;
    }
    
    public function render($report, $x, $y) {

        $this->preRender($report);
        
        $this->charObj->drawPieGraph($this->charData->GetData(), $this->charData->GetDataDescription(), ($this->dimencion['width'] - $this->legend_dimencion['width']) / 2, $this->dimencion['heigth'] / 2, $this->dimencion['heigth'] / 3, PIE_LABELS,FALSE,60, $this->depthFactor(),6);    

        $this->dibujarPieLegend();
        $this->titulo($report);

        $this->reportAdd($report, $x, $y, $this->charObj);
    }
}

?>
