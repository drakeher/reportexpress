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
 * @filesource          Average.php
 */
class Average extends Variable {

    /**
     * Contador de elementos a promediar.
     * @var int 
     */
    private $count = 0;

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
                // esto es un calculo global lo hacemos una ves 
                $this->count = count($data);

                $this->evaluateReport = TRUE;
            }
            // evitamos que se evalue
            return;
        }

        $this->count++;

        $this->value += $data[$pointer][$name];
    }

    /**
     * Resetea la variable si coincide con $type.
     * 
     * @param string $type El tipo de reset.
     */
    public function reset($type) {
        if ($this->resetType() == $type) {
            $this->value = 0;
            $this->count = 0;
        }
    }

    /**
     * Devuelve el valor de la variable.
     * 
     * @return mixed El valor.
     */
    public function value() {
        return $this->value == 0 || $this->count == 0 ? 0 : $this->value / $this->count;
    }

}

?>
