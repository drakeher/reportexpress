<?php

namespace ReportExpress\Component;

/**
 * Line Class
 * 
 * This class contains the logic of the Line component.
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Component
 * @version     1.0 
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Creative Commons (CC) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
class Line extends GraphicProperty {

   /**
    * {@inheritdoc}
    */
   public function __construct($data) {
      parent::__construct($data);
   }

   /**
    * {@inheritdoc}
    */
   public function render($report, $x, $y) {
      $x1 = $this->x() + $x;
      $y1 = $this->y() + $y;

      $x2 = $x1 + $this->width() + ($this->width() == 1 ? -1 : 0);
      $y2 = $this->height() == 1 ? $y1 : $y1 + $this->height();

      if ($this->directionBottomUp()) {
	 $report->get('pdf')->Line($x1, $y2, $x2, $y1, $this->getLineStyle());
	 if ($this->lineStyle() == 'Double') {
	    $report->get('pdf')->Line($x1, $y2 + 2, $x2, $y1 + 2, $this->getLineStyle());
	 }
      } else {
	 $report->get('pdf')->Line($x1, $y1, $x2, $y2, $this->getLineStyle());
	 if ($this->lineStyle() == 'Double') {
	    $report->get('pdf')->Line($x1, $y1 + 2, $x2, $y2 + 2, $this->getLineStyle());
	 }
      }

      return TRUE;
   }

   /**
    * If the address has been changed default is Top Down and 
    * the property is not written in the jrxml.
    *  
    * @return boolean
    */
   public function directionBottomUp() {
      return isset($this->data['direction']);
   }

}

?>
