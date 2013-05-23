<?php

namespace ReportExpress\Component;

/**
 * TextProperty Class
 * 
 * This class contains the logic of the TextProperty component.
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Component
 * @version     1.0
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Creative Commons (CC) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
abstract class TextProperty extends Component {

   /**
    * @TODO Not programmed.
    * @var type 
    */
   protected $pdfEmbedded = FALSE;

   /**
    * (Center,Left,Right,Justified)
    * 
    * @TODO Not programmed
    * @var string
    */
   protected $horizontalAlignment = 'Left';

   /**
    * (Middle,Top,Bottom)
    * 
    * @TODO Not programmed
    * @var string
    */
   protected $verticalAlignment = 'Top';

   /**
    * Position into which must rotate. (UpsideDown,Left,Right)
    * @var string
    */
   protected $rotation = '';

   /**
    * (Single,1.5,Double)
    * 
    * @TODO Not programmed.
    * @var string
    */
   protected $lineSpacing = 'Single';

   /**
    * @TODO Not programmed.
    * @var string 
    */
   protected $markup = '';

   /**
    * {@inheritdoc}
    */
   public function __construct($data) {
      parent::__construct($data);
   }

   /**
    * Returns the name of the font.
    * 
    * @return string The name of font.
    */
   public function fontName() {
      return isset($this->data->textElement->font->fontName) ? $this->data->textElement->font->fontName : 'times';
   }

   /**
    * Return the font style.
    * 
    * @return string The font style.
    */
   public function fontStyle() {

      $style = '';

      $font = $this->data->textElement->font;

      if ((string) $font['isBold'] == 'true') {
	 $style = 'B';
      }
      if ((string) $font['isItalic'] == 'true') {
	 $style .= 'I';
      }
      if ((string) $font['isUnderline'] == 'true') {
	 $style .= 'U';
      }
      if ((string) $font['isStrikeThrough'] == 'true') {
	 $style .= 'D';
      }
      return $style;
   }

   /**
    * Return the font size.
    * 
    * @return int The font size.
    */
   public function size() {
      return (int) $this->data->textElement->font['size'] ? (int) $this->data->textElement->font['size'] : 10;
   }

   /**
    * Return the text align (L=>left,C=>center,R=>right).
    * 
    * @return string The align.
    */
   public function aling() {
      return isset($this->data->textElement['textAlignment']) ? substr((string) $this->data->textElement['textAlignment'], 0, 1) : 'L';
   }

   /**
    * Indicates whether to rotate.
    * 
    * @return boolean
    */
   public function needRotate() {
      return isset($this->data->textElement['rotation']);
   }

   /**
    * Returns the angle to which to rotate.
    * 
    * @return int The angle.
    */
   public function angle() {
      //@TODO rotacion basica
      switch ($this->rotate()) {
	 case "Left":
	    return 90;
	    break;
	 case "Right":
	    return 270;
	    break;
	 case "UpsideDown":
	    return 180;
	    break;
	 default:
	    return 0;
	    break;
      }
   }

   /**
    * Returns the side to which to rotate.
    * 
    * @return string The side.
    */
   public function rotate() {
      return (string) $this->data->textElement['rotation'];
   }

   /**
    * Indicates whether the cell should be tailored to maximizing content or 
    * the content is adapted to the cell.
    * 
    * @return boolean
    */
   public function isStretchWithOverflow() {
      return isset($this->data['isStretchWithOverflow']);
   }

   /**
    * Give me back the time that should be evaluated.
    * 
    * @return string The time.
    */
   public function evaluationTime() {
      return (string) $this->data['evaluationTime'];
   }

}

?>
