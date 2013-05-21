<?php

namespace ReportExpress\Component;

/**
 * Dataset Class
 * 
 * This class contains the logic of the Dataset component.
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Component
 * @version     1.0 In development. Very unstable.
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Creative Commons (CC) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
class DataSet {

   /**
    * Dataset name.
    * @var string 
    */
   private $name = '';

   /**
    * Dataset data.
    * @var array 
    */
   private $data = array();
   /**
    * Constructor of the class.
    * 
    * @param type $name Dataset name.
    * @param type $data Dataset data.
    */
   public function __construct($name, $data) {
      $this->name = $name;
      $this->data = $data;
   }

   /**
    * Return dataset name.
    * 
    * @return string Tha name.
    */
   public function name() {
      return $this->name;
   }

   /**
    * Return dataset data.
    * 
    * @return array The data.
    */
   public function getData() {
      return $this->data;
   }

}

?>
