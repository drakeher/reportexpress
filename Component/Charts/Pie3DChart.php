<?php

namespace ReportExpress\Component\Charts;

/**
 * Pie3DChart Class
 * 
 * This class contains the logic of the Pie3DChart chart.
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Chart
 * @version     1.0
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Copyright (C) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
class Pie3DChart extends PieChart {

   /**
    * {@inheritdoc}
    */
   function __construct($plot, $dataSet, $chart) {
      parent::__construct($plot, $dataSet, $chart);
   }

   /**
    * Returns the value of the property depth factor (number between 0 and 100)
    * or false if not set this option.
    * 
    * @return mixed
    */
   public function depthFactor() {
      $depthFactor = isset($this->plot['depthFactor']) ? (float) $this->plot['depthFactor'] : FALSE;
      if ($depthFactor)
	 return $depthFactor * 100;
      return 25;
   }

   /**
    * {@inheritdoc}
    */
   public function render($report, $x, $y) {

      $this->preRender($report);

      $this->charObj->drawPieGraph($this->charData->GetData(), $this->charData->GetDataDescription(), ($this->dimencion['width'] - $this->legend_dimencion['width']) / 2, $this->dimencion['heigth'] / 2, $this->dimencion['heigth'] / 3, PIE_LABELS, FALSE, 60, $this->depthFactor(), 6);

      $this->dibujarPieLegend();
      $this->titulo($report);

      $this->reportAdd($report, $x, $y, $this->charObj);
   }

}

?>
