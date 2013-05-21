<?php

namespace ReportExpress\Variable;

/**
 * Variable Class
 * 
 * Used for controlling the variables of the report.
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Variable
 * @version     1.0 In development. Very unstable.
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Creative Commons (CC) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
class Variable {

    /**
     * @var \SimpleXMLElement. 
     */
    protected $data = NULL;

    /**
     * Value of the variable.
     * @var mixed 
     */
    protected $value;

    /**
     * Indicates if Variable has already been evaluated.
     * @var boolean 
     */
    protected $evaluateReport;
    
    /**
     * Constructor of the class
     * 
     * @param type $data Tha data of the report.
     * @return void
     */
    public function __construct($data) {
        $this->data = $data;
        $this->evaluateReport = FALSE;
    }

    /**
     * Name of the Variable
     * 
     * @return string The name.
     */
    public function name() {
        return (string) $this->data['name'];
    }

    /**
     * Returns the type of calculation that uses the variable.
     * 
     * @return string Calculation type.
     */
    public function calculation() {
        return (string) $this->data['calculation'];
    }

    /**
     * Returns the expression that evaluates the variable.
     * 
     * @return string The expression.
     */
    public function expression() {
        return (string) $this->data->variableExpression;
    }

    /**
     * Return the reset type used
     * 
     * @return string The reset type.
     */
    public function resetType() {
        return isset($this->data['resetType']) ? (string) $this->data['resetType'] : 'Report';
    }

    /**
     * Return the value of the variable.
     * 
     * @return mixed El valor.
     */
    public function value() {
        return $this->value;
    }

    /**
     * sets the value of the variable.
     * 
     * @param mixed $value The new value.
     */
    public function setVaue($value) {
        $this->value = $value;
    }

    /**
     * Analyzes the expression and replaces the value of value.
     * 
     * @param \ReportExpress\ReportExpress $report The report.
     * @return void
     */
    public function evaluate($report) {
        $this->value = $report->analyse($this->expression());
    }

    /**
     * Resets the variable $type if it matches.
     * 
     * @param string $type The type of reset.
     * @param string $name The group's name in case group reset equals.
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
