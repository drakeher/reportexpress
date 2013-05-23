<?php

namespace ReportExpress\Component\Charts;

/**
 * LineChart Class
 * 
 * This class contains the logic of the LineChart chart.
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Chart
 * @version     1.0
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Creative Commons (CC) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
class LineChart extends Chart {

   /**
    * {@inheritdoc}
    */
   function __construct($plot, $dataSet, $chart) {
      parent::__construct($plot, $dataSet, $chart);
   }

   /**
    * Return the value of the property -isShowLines-.
    * 
    * @return boolean The value -isShowLines-.
    */
   public function isShowLines() {
      return isset($this->plot['isShowLines']) &&
	      (string) $this->plot['isShowLines'] == 'false' ? FALSE : TRUE;
   }

   /**
    * Return the value of the property -isShowShapes-.
    * 
    * @return boolean The value  -isShowShapes-.
    */
   public function isShowShapes() {
      return isset($this->plot['isShowShapes']) &&
	      (string) $this->plot['isShowShapes'] == 'false' ? FALSE : TRUE;
   }

   /**
    * {@inheritdoc}
    */
   public function render($report, $x, $y) {

      $this->preRender($report);

      $this->preRenderBarLineArea();

      // Draw the line graph
      if ($this->isShowLines())
	 $this->charObj->drawLineGraph($this->charData->GetData(), $this->charData->GetDataDescription());
      if ($this->isShowShapes())
	 $this->charObj->drawPlotGraph($this->charData->GetData(), $this->charData->GetDataDescription(), 3, 2, 255, 255, 255);

      $this->leyenda();
      $this->titulo($report);

      $this->reportAdd($report, $x, $y, $this->charObj);
   }

}

?>
