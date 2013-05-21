<?php

namespace ReportExpress\Component\Charts;

/**
 * PieChart Class
 * 
 * This class contains the logic of the PieChart chart.
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Chart
 * @version     1.0
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Copyright (C) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
class PieChart extends Chart {

   /**
    * {@inheritdoc}
    */
   function __construct($plot, $dataSet, $chart) {
      parent::__construct($plot, $dataSet, $chart);
   }

   /**
    * Return the number of sections that should have 
    * or false if it is not set this property.
    * 
    * @return mixed
    */
   public function maxCount() {
      return isset($this->dataSet['maxCount']) ? (integer) $this->dataSet['maxCount'] : FALSE;
   }
   /**
    * Description.
    * 
    * @param \ReportExpress\ReportExpress $report The report.
    * @return void
    */
   public function configData($report) {

      $data = $report->get('values');

      $max = $this->maxCount();

      $label = $this->dataSet->labelExpression ? (string) $this->dataSet->labelExpression : (string) $this->dataSet->keyExpression;

      $pointer = $report->get('ownvariables', 'REPORT_COUNT');

      for ($i = 0; $i < count($data); $i++) {

	 $report->set('ownvariables', $i, 'REPORT_COUNT');

	 $v = $report->analyse((string) $this->dataSet->valueExpression);
	 $l = $report->analyse($label);

	 if (!$max) {
	    $points [] = $v;
	    $desc [] = $l;
	 } else {
	    if ($i < $max) {
	       $points [] = $v;
	       $desc [] = $l;
	    } else {
	       if ($i == $max) {
		  $points[] = $v;
		  $desc[] = $report->analyse((string) $this->dataSet->otherLabelExpression);
	       }
	       else
		  $points [$max] += $v;
	    }
	 }
      }


      $report->set('ownvariables', $pointer, 'REPORT_COUNT');

      $this->charData->AddPoint($points, "SerieValues");
      $this->charData->AddPoint($desc, "SerieAbsciseLabels");

      $this->charData->AddAllSeries();

      $this->charData->SetAbsciseLabelSerie("SerieAbsciseLabels");

      $a = array();

      foreach ($this->charData->GetData() as $v)
	 $a[$v['SerieAbsciseLabels']] = $v['SerieAbsciseLabels'];

      return $a;
   }

   /**
    * {@inheritdoc}
    */
   public function render($report, $x, $y) {

      $this->preRender($report);

      // Draw the pie chart                

      $this->charObj->drawBasicPieGraph($this->charData->GetData(), $this->charData->GetDataDescription(), ($this->dimencion['width'] - $this->legend_dimencion['width']) / 2, $this->dimencion['heigth'] / 2, $this->dimencion['heigth'] / 4, PIE_LABELS, 255, 255, 218);

      $this->charObj->clearShadow();

      $this->dibujarPieLegend();
      $this->titulo($report);

      $this->reportAdd($report, $x, $y, $this->charObj);
   }

   /**
    * Draw the legend of Pie type charts. 
    * Default is drawn with white background and black text.
    * 
    * @return void
    */
   public function dibujarPieLegend() {
      $this->charObj->drawPieLegend($this->dimencion['width'] - $this->legend_dimencion['width'], 0, $this->charData->GetData(), $this->charData->GetDataDescription(), 250, 250, 250);
   }

}

?>
