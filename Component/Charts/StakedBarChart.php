<?php

namespace ReportExpress\Component\Charts;

/**
 * Description of PieChar
 *
 * @author humbe
 */
class StakedBarChart extends Chart {

    function __construct($plot, $dataSet, $chart) {
        parent::__construct($plot, $dataSet, $chart);
    }

    public function render($report, $x, $y) {

        $this->preRender($report);
          
        $this->preRenderBarLineArea(SCALE_ADDALLSTART0);        
      
        $this->charObj->drawStackedBarGraph($this->charData->GetData(), $this->charData->GetDataDescription(), $this->foregroundAlpha());

        $this->leyenda();
        $this->titulo($report);

        $this->reportAdd($report, $x, $y, $this->charObj);
    }

}

?>
