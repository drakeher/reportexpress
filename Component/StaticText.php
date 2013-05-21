<?php

namespace ReportExpress\Component;

/**
 * StaticText Class
 * 
 * This class contains the logic of the StaticText component.
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Component
 * @version     1.0
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Copyright (C) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
class StaticText extends TextProperty {

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

      //estilo 
      $report->get('pdf')->SetFont($this->fontName(), $this->fontStyle(), $this->size());

      //color 
      $color = $this->fontColor();
      $report->get('pdf')->SetTextColor($color[0], $color[1], $color[2]);

      //rotacion
      if ($this->needRotate() == 1) {
	 $report->get('pdf')->StartTransform();
	 $report->get('pdf')->Rotate($this->angle(), $this->x() + $x, $this->y() + $y);
	 $report->get('pdf')->StopTransform();
      }

      //background
      $bgcolor = $this->backgroundColor();
      $report->get('pdf')->SetFillColor($bgcolor[0], $bgcolor[1], $bgcolor[2]);

      //celda       
      $report->get('pdf')->MultiCell($this->width(), $this->height(), (string) $this->data->text, 0, $this->aling(), 1, 1, $this->x() + $x, $this->y() + $y, TRUE, 0, FALSE, TRUE, $this->isStretchWithOverflow() == 0 ? $this->height() + 1 : 0);

      return TRUE;
   }

}

?>
