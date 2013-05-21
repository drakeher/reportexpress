<?php

namespace ReportExpress\Variable;

/**
 * Count Class
 * 
 * Used for controlling the Count variables of the report.
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Variable
 * @version     1.0 In development. Very unstable.
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Creative Commons (CC) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
class Count extends Variable {
   
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
        $data = $report->get('values');

        if ($this->resetType() == 'Report') {

            // si aun no se ha evaluado
            if ($this->evaluateReport == FALSE) {
                // esto es un calculo global lo hacemos una ves 
                $this->value = count($data);

                $this->evaluateReport = TRUE;
            }
            // evitamos que se evalue
            return;
        }

        $this->value++;
    }

}

?>
