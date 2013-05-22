<?php

namespace ReportExpress\Variable;
/**
 * Sum Class
 * 
 * Used for controlling the Sum variables of the report.
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Variable
 * @version     1.0
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Creative Commons (CC) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
class Sum extends Variable {
   /**
    * {@inheritdoc}
    */
    public function __construct($data) {
        parent::__construct($data);
    }

    /**
     * {@inheritdoc}
     */
    public function evaluate($report) {
        // @TODO esta expresion puede ser mucho mas compleja
        $name = substr(preg_replace('/\s/', '', $this->expression()), 3, -1);

        $pointer = $report->get('ownvariables', 'REPORT_COUNT');
        $data = $report->get('values');

        if ($this->resetType() == 'Report') {

            // si aun no se ha evaluado
            if ($this->evaluateReport == FALSE) {

                //esta operacion solo se realiza una vez
                foreach ($data as $value) {
                    $this->value += $value[$name];
                }

                $this->evaluateReport = TRUE;
            }
            // evitamos que se evalue
            return;
        }
        $this->value += $data[$pointer][$name];
    }

}

?>
