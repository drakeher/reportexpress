<?php

namespace Component\Charts;

/**
 * BarChart Class
 * 
 * This class contains the logic of the BarChart chart.
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Chart
 * @version     1.0 In development. Very unstable.
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Creative Commons (CC) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
class BarChart extends Chart {

   /**
    * {@inheritdoc}
    */
   function __construct($plot, $dataSet, $chart) {
      parent::__construct($plot, $dataSet, $chart);
   }

   /**
    * {@inheritdoc}
    */
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
