<?php

namespace ReportExpress\Component\Charts;

/**
 * AreaChart Class
 * 
 * This class contains the logic of the AreaChart chart.
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Charts
 * @version     1.0 In development. Very unstable.
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Creative Commons (CC) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
class AreaChart extends Chart {

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
      $this->charObj->drawFilledLineGraph($this->charData->GetData(), $this->charData->GetDataDescription(), $this->foregroundAlpha());

      $this->leyenda();
      $this->titulo($report);

      $this->reportAdd($report, $x, $y, $this->charObj);
   }

}

?>
