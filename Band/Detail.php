<?php

namespace ReportExpress\Band;

/**
 * Detail Class
 * 
 * This class contains the logic of the Detail bands.
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Band
 * @version     1.0
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Creative Commons (CC) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
class Detail extends Band {

   /**
    * Last value that reaches the Y axis to renderearse a row. 
    * @var int
    */
   private $lasty = 0;

   /**
    * Indicates whether a page break hanging groups exist. 
    * @var boolean 
    */
   private $hangingFooter = FALSE;

   /**
    * Construtor of the class
    * 
    * @param \SimpleXmlElment $xml XML from band.
    * @return void
    */
   public function __construct($xml) {
      parent::__construct($xml->detail->band);
   }

   /**
    * {@inheritdoc}
    * @return The state of was rendered the row.
    */
   public function render($report, $point) {
      //si existe algun groupfooter colgado lo redereamos
      if ($this->hangingFooter) {
	 $this->renderGroupFooter($report, $point);
	 $this->hangingFooter = FALSE;
      }
      //evaluamos la variables
      $report->evaluationVariable();
      //evaluamos los groupheader
      if (!$this->renderGroupHeader($report, $point)) {
	 return 'newpage';
      }
      //si no hay suficiente espacio para la fila de detalle state:newpage
      if ($this->maxy + $point->y > $point->mxb) {
	 return 'newpage';
      }
      //indica realmente cuanto crecio la fila al ser rendereada
      $this->height = 0;
      //rendereamos component
      $this->realRender('component', $report, $point);
      //redereamos after
      $this->realRender('after', $report, $point);
      //actualizamos la ultima posicion que obtuvo la el eje y
      $this->lasty = $point->y += $this->height;
      return 'otherrow';
   }

   /**
    * Returns the maximum value reached by the (y) but the average height
    * of each row. This method is used to know If space 
    * remains to display the next row.
    * 
    * @return int The value
    */
   public function maxRow() {
      return $this->maxy + $this->lasty;
   }

   /**
    * Returns the maximum value reached by the (y).
    * 
    * @return int The value.
    */
   public function lasty() {
      return $this->lasty;
   }

   /**
    * sets the value of the last (y).
    * 
    * @param int $value The new value of  (y).
    */
   public function setLasty($value) {
      $this->lasty = $value;
   }

   /**
    * Render header groups.
    * 
    * @param \ReportExpress\ReportExpress $report The report which is rendered.
    * @param \ReportExpress\Core\Point $point The point where it begins to render.
    * @return boolean Return TRUE or FALSE to indicate whether 
    * the groups are rendered totally header.
    */
   public function renderGroupHeader($report, $point) {

      if ($report->hasGroups()) {

	 foreach ($report->get('groups') as $group) {

	    $result = $report->analyse($group->groupExpression());

	    if ($result != $group->value('header')) {
	       if (!$group->render($report, $point, 'header')) {
		  return FALSE;
	       }
	       $group->setValue('header', $result);

	       if ($report->index() == 0) {
		  $group->setValue('footer', $result);
	       }
	    }
	 }
      }

      return TRUE;
   }

   /**
    * Render the final groups.
    * 
    * @param \ReportExpress\ReportExpress $report The report which is rendered.
    * @param \ReportExpress\Core\Point $point The point where it begins to render.
    * @return boolean Return TRUE or FALSE to indicate whether fully 
    * rendered the final groups.
    */
   public function renderGroupFooter($report, $point) {

      if ($report->hasGroups()) {

	 $groups = $report->get('groups');

	 for ($i = count($groups) - 1; $i >= 0; $i--) {

	    $result = $report->analyse($groups[$i]->groupExpression());

	    if ($result != $groups[$i]->value('footer')) {
	       if (!$groups[$i]->render($report, $point, 'footer')) {
		  $this->hangingFooter = TRUE;
		  return FALSE;
	       }

	       $groups[$i]->setValue('footer', $result);

	       //reseteamos todas las variables de tipo Group
	       $report->resetVariables('Group', $groups[$i]->name());
	    }
	 }
      }

      return TRUE;
   }

}
?>