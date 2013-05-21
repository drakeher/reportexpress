<?php

namespace ReportExpress\Variable;

/**
 * Property Class
 * 
 * Used for controlling Property variable of the report.
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Variable
 * @version     1.0 In development. Very unstable.
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Creative Commons (CC) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
class Average extends Variable {

   /**
    * Averaging counter items.
    * @var int 
    */
   private $count = 0;

   public function __construct($data) {
      parent::__construct($data);
   }

   /**
    * Evaluates the variable.
    * 
    * @param \ReportExpress\ReportExpress $report The report.
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
    * Resets the variable $type if it matches.
    * 
    * @param string $type The type to reset.
    * @return void
    */
   public function reset($type) {
      if ($this->resetType() == $type) {
	 $this->value = 0;
	 $this->count = 0;
      }
   }

   /**
    * Return the value of variable.
    * 
    * @return mixed The value.
    */
   public function value() {
      return $this->value == 0 || $this->count == 0 ? 0 : $this->value / $this->count;
   }

}

?>
