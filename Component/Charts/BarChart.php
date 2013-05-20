<?php

namespace ReportExpress\Component\Charts;

/**
 * Description of PieChar
 *
 * @author humbe
 */
class BarChart extends Chart {

    function __construct($plot, $dataSet, $chart) {
        parent::__construct($plot, $dataSet, $chart);
    }

    public function render($report, $x, $y) {

        $this->preRender($report);
          
        $this->preRenderBarLineArea();
        
        // Draw the bar graph
        $this->charObj->drawBarGraph($this->charData->GetData(), $this->charData->GetDataDescription(), TRUE, $this->foregroundAlpha());

        $this->leyenda();
        $this->titulo($report);

        $this->reportAdd($report, $x, $y, $this->charObj);
    }

}

?>
