<?php

namespace Component;

/**
 * GraphicProperty Class
 * 
 * This class contains the logic of the GraphicProperty component.
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Component
 * @version     1.0 In development. Very unstable.
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Creative Commons (CC) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
abstract class GraphicProperty extends Component {

   /**
    * {@inheritdoc}
    */
   public function __construct($data) {
      parent::__construct($data);
   }

   /**
    * Return the line style.
    * 
    * @return string The line style.
    */
   public function lineStyle() {
      return isset($this->data->graphicElement->pen["lineStyle"]) ? (string) $this->data->graphicElement->pen["lineStyle"] : "Solid";
   }

   /**
    * Return the line width.
    * 
    * @return float The line width.
    */
   public function lineWidth() {
      return isset($this->data->graphicElement->pen["lineWidth"]) ? (float) $this->data->graphicElement->pen["lineWidth"] : 1.0;
   }

   /**
    * Format compatible with TCPDF line for components.
    * 
    * @return array The format line.
    */
   public function getLineStyle() {

      $color = $this->lineColor();

      if (!$color) {
	 $color = $this->fontColor();
      }

      return array(
	  'width' => $this->lineWidth(),
	  'cap' => 'butt', 'join' => 'miter',
	  'dash' => $this->getDash(),
	  'color' => $color
      );
   }

   /**
    * Return line color.
    * 
    * @return mixed The hexadecimal color.
    */
   public function lineColor() {
      return isset($this->data->graphicElement->pen["lineColor"]) ? $this->data->graphicElement->pen["lineColor"] : NULL;
   }

   /**
    * The dash style.
    * 
    * @return string|int The dash.
    */
   public function getDash() {
      switch ($this->lineStyle()) {
	 case "Dashed":
	    return "5,3";
	    break;
	 case "Dotted":
	    return "1";
	    break;
	 default:// Solid, Double
	    return 0;
	    break;
      }
   }

}

?>
