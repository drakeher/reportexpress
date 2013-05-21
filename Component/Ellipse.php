<?php

namespace ReportExpress\Component;

/**
 * Ellipse Class
 * 
 * This class contains the logic of the Ellipse component.
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Component
 * @version     1.0 In development. Very unstable.
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Creative Commons (CC) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
class Ellipse extends GraphicProperty {
    
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
        $report->get('pdf')->Ellipse($this->x() + $x + ($this->width() / 2), $this->y() + $y + ( $this->height() / 2), $this->width() / 2, $this->height() / 2, 0, 0, 360, $this->isTransparent() ? "D" : "DF", $this->getLineStyle(), $this->backgroundColor());
    }

}

?>
