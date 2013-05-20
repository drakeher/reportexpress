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
 * @filesource          Variable.php
 */
class Variable {

    /**
     *
     * @var \SimpleXMLElement 
     */
    protected $data = NULL;

    /**
     * Valor de la variable.
     * @var mixed 
     */
    protected $value;

    /**
     * Indica si esta variable ya ha sido evaluada para resetType = report
     * @var boolean 
     */
    protected $evaluateReport;

    public function __construct($data) {
        $this->data = $data;
        $this->evaluateReport = FALSE;
    }

    /**
     * Nombre de la variable.
     * 
     * @return string El nombre.
     */
    public function name() {
        return (string) $this->data['name'];
    }

    /**
     * Devuelve el tipo de calculo que emplea la variable.
     * 
     * @return string Tipo de calculo.
     */
    public function calculation() {
        return (string) $this->data['calculation'];
    }

    /**
     * Devuelve la expresion que evalua la variable.
     * 
     * @return string La expresion.
     */
    public function expression() {
        return (string) $this->data->variableExpression;
    }

    /**
     * Tipo de reset que utiliza.
     * 
     * @return string El reset.
     */
    public function resetType() {
        return isset($this->data['resetType']) ? (string) $this->data['resetType'] : 'Report';
    }

    /**
     * Devuelve el valor de la variable.
     * 
     * @return mixed El valor.
     */
    public function value() {
        return $this->value;
    }

    /**
     * Permite modificar el valor de la variable.
     * 
     * @param mixed $value El nuevo valor.
     */
    public function setVaue($value) {
        $this->value = $value;
    }

    /**
     * Analiza la expresion y reemplaza el valor de value.
     * 
     * @param \ReportExpress\Core\ReportExpress $report El objeto reporte.
     */
    public function evaluate($report) {
        $this->value = $report->analyse($this->expression());
    }

    /**
     * Resetea la variable si coincide con $type.
     * 
     * @param string $type El tipo de reset.
     * @param string $name El nombre del grupo en caso de que reset sea igual a group.
     * @return void
     */
    public function reset($type, $name = NULL) {

        if ($this->resetType() == $type) {
            if ($type == 'Group') {
                if ($name == (string) $this->data['resetGroup']) {
                    $this->value = 0;
                }
                return;
            }
            $this->value = 0;
        }
    }

}

?>
