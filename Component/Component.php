<?php

namespace ReportExpress\Component;

/**
 * Component Class
 * 
 * This class contains the logic of the component.
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Component
 * @version     1.0 In development. Very unstable.
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Creative Commons (CC) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
abstract class Component {

   /**
    * The XML data.
    * @var \SimpleXMLElement 
    */
   protected $data = NULL;

   /**
    * Constuctor of the class.
    * 
    * @param type $xml
    * @return void
    */
   public function __construct($data) {
      $this->data = $data;
   }

   /**
    * Return the X axis.
    * 
    * @return int The value
    */
   public function x() {
      return (int) $this->data->reportElement['x'];
   }

   /**
    * Return the y axis.
    * 
    * @return int The value.
    */
   public function y() {
      return (int) $this->data->reportElement['y'];
   }

   /**
    * Return the width of component.
    * 
    * @return int The value.
    */
   public function width() {
      return (int) $this->data->reportElement['width'];
   }

   /**
    * Return the height of component.
    * 
    * @return int The value.
    */
   public function height() {
      return (int) $this->data->reportElement['height'];
   }

   /**
    * Conviert a hexadecimal color to RGB color.
    * 
    * @param string $color The hexadecimal color.
    * @return array The RGB color.
    */
   public function toColor($color) {
      return array(hexdec(substr($color, 1, 2)), hexdec(substr($color, 3, 2)), hexdec(substr($color, 5, 2)));
   }

   /**
    * Return the color of font.
    * 
    * @return array The RGB color of the font.
    */
   public function fontColor() {
      return isset($this->data->reportElement["forecolor"]) ? $this->toColor((string) $this->data->reportElement["forecolor"]) : array(0, 0, 0);
   }

   /**
    * Return the background color
    * 
    * @return array The RGB background color.
    */
   public function backgroundColor() {
      return isset($this->data->reportElement["backcolor"]) ? $this->toColor((string) $this->data->reportElement["backcolor"]) : array(255, 255, 255);
   }

   /**
    * Return the transparent
    * 
    * @return boolean TRUE it is transparent, FALSE otherwise.
    */
   public function isTransparent() {
      return $this->data->reportElement["mode"] == "Transparent" ? TRUE : FALSE;
   }

   /**
    * Return the relative position of the component 
    * (FixRelativeToBottom, FixRelativeToTop, Float).
    * 
    * @return string The position.
    */
   public function positionType() {
      return isset($this->data->reportElement["positionType"]) ? $this->data->reportElement["positionType"] : 'Float';
   }

   /**
    * Return the result of the expression after testing.
    * 
    * @param \ReportExpress\ReportExpress $report The report.
    * @return boolean
    */
   public function printWhenExpression($report) {
      return isset($this->data->reportElement->printWhenExpression) ? (boolean) $report->analyse((string) $this->data->reportElement->printWhenExpression) : TRUE;
   }

   /**
    * @return string The name of group.
    */
   public function printWhenGroupChanges() {
      return (string) $this->data->reportElement['printWhenGroupChanges'];
   }

   /**
    * @param \ReportExpress\ReportExpress $report The report.
    * @param int $x X axis.
    * @param int $y Y axis.
    */
   abstract public function render($report, $x, $y);

}

?>
