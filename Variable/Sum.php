<?php

namespace ReportExpress\Variable;

/**
 * ReportExpress
 *
 * @package		ReportExpress
 * @subpackage          Variable
 * @author		Sparkle Team
 * @copyright           Copyright (c) 2013, ReportExpress.
 * @license		http://reportexpress.com/license.html
 * @link		http://reportexpress.com
 * @since		Version 1.0
 * @filesource          Sum.php
 */
class Sum extends Variable {

    public function __construct($data) {
        parent::__construct($data);
    }

    /**
     * Se evalua la variable.
     * 
     * @param \ReportExpress\Core\ReportExpress $report El objeto del reporte.
     * @return void
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
