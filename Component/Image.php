<?php

namespace Component;

/**
 * Image Class
 * 
 * This class contains the logic of the Image component.
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Component
 * @version     1.0 In development. Very unstable.
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Creative Commons (CC) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
class Image extends Component {

   /**
    * {@inheritdoc}
    */
    public function __construct($data) {
        parent::__construct($data);
    }

    /**
     * The value can be varied, a direction of image, a calculated value 
     * or simply the name. Implemented only the last.
     * 
     * @return string The value.
     */
    public function imageExpression() {
        return (string) $this->data->imageExpression;
    }

    /**
    * {@inheritdoc}
    */
    public function render($report, $x, $y) {
        $report->get('pdf')->Image($report->get('path') . $report->analyse($this->imageExpression()), $this->x() + $x, $this->y() + $y, $this->width(), $this->height());
    }

}

?>
