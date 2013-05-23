<?php

namespace ReportExpress\Band;

/**
 * Group Class
 * 
 * This class contains the logic of the Group bands.
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Band
 * @version     1.0 
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Creative Commons (CC) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
class Group {

   /**
    * Name of group.
    * @var string 
    */
   private $name;

   /**
    * The expression that indicates the beginning and end of the group.
    * @var string 
    */
   private $groupExpression;

   /**
    * If you start on a new page.
    * @var boolean 
    * 
    * @TODO No programmed.
    */
   private $isStartNewPage;

   /**
    * If resetting the number of pages.
    * @var boolean 
    * 
    * @TODO No programmed.
    */
   private $isResetPageNumber;

   /**
    * @var boolean  
    * 
    * @TODO No programmed.
    */
   private $isReprintHeaderOnEachPage;

   /**
    * It behaves like a band and contains the components 
    * to show within the group.
    * @var Band 
    */
   private $groupHeader = NULL;

   /**
    * It behaves like a band and contains the components 
    * to show within the group.
    * @var Band 
    */
   private $groupFooter = NULL;

   /**
    * Contains the value after analyzing the group.
    * @var array 
    */
   private $value = array('header' => NULL, 'footer' => NULL);

   /**
    * Construtor of the class
    * 
    * @param \SimpleXmlElment $data.
    * @return void
    */
   public function __construct($data) {

      //attribute - simple
      $this->name = (string) $data['name'];
      $this->isStartNewPage = (boolean) $data['isStartNewPage'];
      $this->isResetPageNumber = (boolean) $data['isResetPageNumber'];
      $this->isReprintHeaderOnEachPage = (boolean) $data['isReprintHeaderOnEachPage'];
      $this->groupExpression = isset($data->groupExpression) ? (string) $data->groupExpression : NULL;

      //attribute - object
      if ($data->groupHeader && count($data->groupHeader->band->children()) > 0) {
	 $this->groupHeader = new Band($data->groupHeader->band);
      }
      if ($data->groupFooter && count($data->groupFooter->band->children()) > 0) {
	 $this->groupFooter = new Band($data->groupFooter->band);
      }
   }

   /**
    * Return the name of group.
    * 
    * @return string nombre.
    */
   public function name() {
      return $this->name;
   }

   /**
    * Return the expression of the group.
    * 
    * @return string The expresion.
    */
   public function groupExpression() {
      return $this->groupExpression;
   }

   /**
    * IsStartNewPage.
    * 
    * @return boolean The value.
    */
   public function isStartNewPage() {
      return $this->isStartNewPage;
   }

   /**
    * IsResetPageNumber.
    * 
    * @return boolean The value.
    */
   public function isResetPageNumber() {
      return $this->isResetPageNumber;
   }

   /**
    * IsReprintHeaderOnEachPage.
    * 
    * @return boolean The value.
    */
   public function isReprintHeaderOnEachPage() {
      return $this->isReprintHeaderOnEachPage;
   }

   /**
    * Return the groupHeader of the group.
    * 
    * @return \ReportExpress\Band\Band The groupHeader band.
    */
   public function header() {
      return $this->groupHeader;
   }

   /**
    * Return the groupFooter of the group.
    * 
    * @return \ReportExpress\Band\Band The groupFooter band.
    */
   public function footer() {
      return $this->groupFooter;
   }

   /**
    * Return the value of the group.
    * 
    * @param string $name Name of the group.
    * @return mixed The value.
    */
   public function value($name) {
      return $this->value[$name];
   }

   /**
    * Change the value of the group.
    * 
    * @param string $name The name of group.
    * @param mixed $value The new value.
    */
   public function setValue($name, $value) {
      $this->value[$name] = $value;
   }

   /**
    * Render the group.
    * 
    * @param \ReportExpress\ReportExpress $report The report which is rendered.
    * @param \ReportExpress\Core\Point $point The point where it begins to render.
    * @param string $name Name of group.
    * @return boolean TRUE if it was render completely, FALSE otherwise. 
    */
   public function render($report, $point, $name) {

      $group = $this->$name();

      if ($group) {

	 $h = $point->y + $group->height();

	 if ($h < $point->mxb) {
	    $group->render($report, $point);
	    $point->y = $h;
	    return TRUE;
	 }
	 return FALSE;
      }
      return TRUE;
   }

}

?>
