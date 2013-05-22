<?php

namespace Component;

use Core\Point;


/**
 * TextField Class
 * 
 * This class contains the logic of the TextField component.
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Component
 * @version     1.0 In development. Very unstable.
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Creative Commons (CC) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
class TextField extends TextProperty {

   /**
    * @TODO Not programmed.
    * @var boolean 
    */
   private $blankWhenNull = FALSE;

   /**
    * Time when the component is evaluated (Now,Report,Page,Column,Group,Band,Auto).
    * @TODO No esta bien programado los tiempos.
    * @var string
    */
   private $evaluationTime = 'Now';

   /**
    * Group name which is evaluated.
    * @TODO Not programmed
    * @var string 
    */
   private $evaluationGroup = '';

   /**
    * Java date format and similar in php.
    * @var array
    */
   private $java_format_date = array(
       '/GG/' => '',
       '/yyyy/' => 'Y',
       '/yy/' => 'y',
       '/MMMMM|MMMM/' => 'F',
       '/MMM/' => 'M',
       '/MM/' => 'm',
       '/M/' => 'n',
       '/dd/' => 'd',
       '/d/' => 'j',
       '/hh|kk/' => 'h',
       '/h|k/' => 'g',
       '/HH|KK/' => 'H',
       '/H|K/' => 'G',
       '/mm|m/' => 'i',
       '/ss|s/' => 's',
       '/SSS/' => 'u',
       '/EEEEE|EEEE/' => 'l',
       '/EE/' => 'D',
       '/DDD|D/' => 'z',
       '/F/' => 'N',
       '/w|W/' => 'W',
       '/aa/' => 'A',
       '/a/' => 'a',
       '/zzzz|zzz|z/' => 'T'
   );

   /**
    * {@inheritdoc}
    */
   public function __construct($data) {
      parent::__construct($data);
   }

   /**
    * Return the pattern applied to the text.
    * 
    * @return string The pattern
    */
   public function pattern() {
      return isset($this->data['pattern']) ? (string) $this->data['pattern'] : NULL;
   }

   /**
    * Validates the number format that contains the text.
    * 
    * @param string $number The text to format.
    * @param string $pattern The pattern used by the ireport.
    * @return string Text formatted.
    */
   public function formatNumber($number, $pattern) {

      $separador = '.';

      //p[0] numero positivo, p[1] numero negativo
      $p = preg_split('/;/', $pattern);

      //Porcentaje
      if (count($p) == 1) {

	 $ceros = preg_split('/\./', $pattern);

	 $sym = $ceros[1][strlen($ceros[1]) - 1];

	 $decimals = count($ceros) > 1 ? count(preg_split('/(0)/', $ceros[1])) - 1 : 0;

	 return number_format($number, $decimals, ',', $separador) . ' ' . ($sym == '%' ? $sym : '‰');

	 //Numero
      } else {

	 //numero positivo
	 $np = $p[0];

	 //separador
	 if ($np[1] != ',')
	    $separador = '';

	 //cantidad de numeros despues del separadores
	 $ceros = preg_split('/\./', $np);
	 $decimals = count($ceros) == 1 ? 0 : strlen($ceros[1]);

	 $number = number_format($number, $decimals, ',', $separador);

	 if ($number < 0) {
	    //normalizamos el numero
	    $number *= -1;

	    //numero negativo
	    $nn = $p[1];

	    if ($nn[0] == '-') {
	       return "-$number";
	    } elseif ($nn[strlen($nn) - 1] == '-') {
	       return "$number-";
	    } elseif ($nn[0] == '(') {

	       if ($nn[1] == '-') {
		  return "(-$number)";
	       } elseif ($nn[strlen($nn) - 2] == '-') {
		  return "($number-)";
	       }
	       return "($number)";
	    }
	 }

	 return $number;
      }
   }

   /**
    * Converts a java date pattern to a pattern php.
    * 
    * @param string $pattern Pattern java date.
    * @return string Pattern date in php.
    */
   public function formatDate($pattern) {

      foreach ($this->java_format_date as $key => $val) {

	 $chunk = preg_split($key, $pattern);

	 $total = count($chunk);

	 if ($total > 1) {
	    break;
	 }
      }

      $new_pattern = "";

      if ($total > 1) {

	 foreach ($chunk as $ch => $v) {

	    if ($v != "") {
	       $new_pattern .= $this->formatDate($v);
	    }
	    if ($ch + 1 < $total) {
	       $new_pattern .= $this->java_format_date[$key];
	    }
	 }
      } else {
	 $new_pattern = $pattern;
      }

      return $new_pattern;
   }

   /**
    * It evaluates the content to display in the textfield.
    * 
    * @param \ReportExpress $report The report.
    * @return string The text to display.
    */
   public function text($report) {

      if ((string) $this->data->textFieldExpression == "new java.util.Date()") {
	 return date($this->formatDate($this->pattern()));
      }

      $result = $report->analyse((string) $this->data->textFieldExpression);

      //obtenemos el patron que usa para los datos
      $pattern = $this->pattern();

      //@TODO solo evaluamos patrones numericos mejorar esto 
      //para todo tipo de patrones
      //evaluamos el patron
      if ($pattern && $pattern[0] == '#' && is_numeric($result)) {
	 return $this->formatNumber($result, $pattern);
      }
      //analizamos para capturar expresiones
      return $result;
   }

   /**
    * {@inheritdoc}
    */
   public function render($report, $x, $y) {

      //@TODO programar cuando $time sea = Report
      $time = $this->evaluationTime();

      //no se puede evaluar hasta terminar la pagina
      if ($report->get('readyPage') == FALSE && $time == 'Page') {

	 //adicionamos al registro para que sea evaluada al final
	 $evaluate = $report->get('evaluatePage');
	 $evaluate [] = array('point' => new Point($x, $y), 'index' => $report->get('ownvariables', 'REPORT_COUNT'), 'object' => $this);
	 $report->set('evaluatePage', $evaluate);

	 //detemos la ejecucion del metodo
	 return FALSE;
      }

      //estilo 
      $report->get('pdf')->SetFont($this->fontName(), $this->fontStyle(), $this->size());

      //color 
      $color = $this->fontColor();
      $report->get('pdf')->SetTextColor($color[0], $color[1], $color[2]);

      //rotacion
      if ($this->needRotate() == 1) {
	 $report->get('pdf')->StartTransform();
	 $report->get('pdf')->Rotate($this->angle(), $this->x() + $x, $this->y() + $y);
	 $report->get('pdf')->StopTransform();
      }

      //background
      $bgcolor = $this->backgroundColor();
      $report->get('pdf')->SetFillColor($bgcolor[0], $bgcolor[1], $bgcolor[2]);

      //celda       
      $report->get('pdf')->MultiCell($this->width(), $this->height(), $this->text($report), 0, $this->aling(), 1, 1, $this->x() + $x, $this->y() + $y, TRUE, 0, FALSE, TRUE, $this->isStretchWithOverflow() == 0 ? $this->height() + 1 : 0);

      return TRUE;
   }

}

?>
