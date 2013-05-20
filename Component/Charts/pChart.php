<?php

namespace ReportExpress\Component\Charts;

require_once(dirname(__FILE__) . '/../../Vendor/pChart/pChart/pData.class');
require_once(dirname(__FILE__) . '/../../Vendor/pChart/pChart/pChart.class');

/**
 * Description of pChart
 *
 * @author humberto.arencibia
 */
class pChart extends \pChart{       
    
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

     return(array($MaxWidth,$MaxHeight));
    }
}

?>
