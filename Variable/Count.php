<?php

namespace ReportExpress\Variable;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

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
 * @filesource          Count.php
 */
class Count extends Variable {

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
