<?php

namespace ReportExpress\Component;

/**
 * Rectangle Class
 * 
 * This class contains the logic of the Rectangle component.
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Component
 * @version     1.0
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Creative Commons (CC) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
class Rectangle extends GraphicProperty {

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
      $borderStyle = $this->getLineStyle();
      $style = $this->isTransparent() ? "D" : "DF";
      $radius = $this->getRadius();

      if ($radius > 0) {
	 $report->get('pdf')->RoundedRect($this->x() + $x, $this->y() + $y, $this->width(), $this->height(), $radius, "1111", $style, $borderStyle, $this->backgroundColor());
      } else {
	 $report->get('pdf')->Rect($this->x() + $x, $this->y() + $y, $this->width(), $this->height(), $style, array('all' => $borderStyle), $this->backgroundColor());
      }
   }

   /**
    * The radius.
    * 
    * @return float The value.
    */
   public function getRadius() {
      return isset($this->data["radius"]) ? (float) $this->data["radius"] : 0;
   }

}

?>
