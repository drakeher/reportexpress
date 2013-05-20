<?php

namespace ReportExpress\Component\Charts;

/**
 * Description of PieChar
 *
 * @author humbe
 */
class LineChart extends Chart {

    function __construct($plot, $dataSet, $chart) {
        parent::__construct($plot, $dataSet, $chart);
    }

    /**
     * Devuelve el valor de la propiedad -isShowLines-
     * 
     * @return boolean Valor de la propiedad -isShowLines-
     */
    public function isShowLines()
    {
      return isset($this->plot['isShowLines']) && 
        (string) $this->plot['isShowLines'] == 'false' ? FALSE : TRUE;   
    }
    
    /**
     * Devuelve el valor de la propiedad -isShowShapes-
     * 
     * @return boolean Valor de la propiedad -isShowShapes-
     */
    public function isShowShapes()
    {
       return isset($this->plot['isShowShapes']) && 
        (string) $this->plot['isShowShapes'] == 'false' ? FALSE : TRUE;       
    }


    public function render($report, $x, $y) {

        $this->preRender($report);
         
        $this->preRenderBarLineArea();
        
         // Draw the line graph
        if($this->isShowLines())
        $this->charObj->drawLineGraph($this->charData->GetData(), $this->charData->GetDataDescription());
        if($this->isShowShapes())
        $this->charObj->drawPlotGraph($this->charData->GetData(), $this->charData->GetDataDescription(), 3, 2, 255, 255, 255);
       
        $this->leyenda();
        $this->titulo($report);

        $this->reportAdd($report, $x, $y, $this->charObj);
    }

//put your code here
}

?>
