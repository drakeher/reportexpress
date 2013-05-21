<?php

namespace ReportExpress\Component\Charts;

require_once(dirname(__FILE__) . '/../../Vendor/pChart/pChart/pData.class');
require_once(dirname(__FILE__) . '/../../Vendor/pChart/pChart/pChart.class');

/**
 * pChart Class
 * 
 * This class contains the logic of the pChart.
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Chart
 * @version     1.0 In development. Very unstable.
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Creative Commons (CC) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
class pChart extends \pChart{       
    
    /**
     * Description.
     * 
     * @param type $DataDescription
     * @param type $pieArrayLegend
     * @return array
     */
    function getLegendBoxSize($DataDescription, $pieArrayLegend = false)
    {
        
       if ( !isset($DataDescription["Description"]) && !$pieArrayLegend)
      return(-1);
      
       if($pieArrayLegend)
           $DataDescription["Description"] = $pieArrayLegend;
       
     /* <-10->[8]<-4->Text<-10-> */
     $MaxWidth = 0; $MaxHeight = 8;
     foreach($DataDescription["Description"] as $Key => $Value)
      {
       $Position   = imageftbbox($this->FontSize,0,$this->FontName,$Value);
       $TextWidth  = $Position[2]-$Position[0];
       $TextHeight = $Position[1]-$Position[7];
       if ( $TextWidth > $MaxWidth) { $MaxWidth = $TextWidth; }
       $MaxHeight = $MaxHeight + $TextHeight + 4;
      }
     $MaxHeight = $MaxHeight - 3;
     $MaxWidth  = $MaxWidth + 32;

     return array($MaxWidth,$MaxHeight);
    }
}

?>
