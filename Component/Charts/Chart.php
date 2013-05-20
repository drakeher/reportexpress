<?php

namespace ReportExpress\Component\Charts;

require_once(dirname(__FILE__) . '/pChart.php');

use ReportExpress\Component\Component,
    ReportExpress\Component\Charts\pChart;

/**
 * Description of Chart
 *
 * @author humbe
 */
abstract class Chart extends Component {

    /**
     * @var \SimpleXMLElement Estilos de los elementos de la gráfica 
     */
    protected $plot;

    /**
     * @var \SimpleXMLElement Template paraa parsear los datos
     */
    protected $dataSet;

    /**
     * @var \pData Objeto con los datos a graficar 
     */
    protected $charData;

    /**
     * @var \pChart Objeto para graficar
     */
    protected $charObj;

    /**
     * @var Array --Array['width'] = whidth -- Array['height'] = height --
     */
    protected $dimencion;

    /**
     * @var Array --Array['width'] = whidth -- Array['height'] = height --
     */
    protected $legend_dimencion;

    /**
     * 
     * @param \SimpleXMLElement $plot
     * @param \SimpleXMLElement $dataSet
     * @param \SimpleXMLElement $chart
     * 
     */
    public function __construct($plot, $dataSet, $chart) {
        $this->plot = $plot;
        $this->dataSet = $dataSet;
        $this->charData = new \pData();
        parent::__construct($chart);

        $this->dimencion = $this->calculate_dimension();

        $this->legend_dimencion = array('width' => 0, 'height' => 0);
    }

    /**
     * @return Array --Array['width'] = whidth -- Array['height'] = height --  
     */
    public function calculate_dimension() {
        $width = $this->width() + 10;
        $heigth = $this->height() + 10;

        return array('width' => $width, 'heigth' => $heigth);
    }

    /**
     * Dibuja el titulo de la gráfica.
     * 
     * @param \ReportExpress\Core\ReportExpress $report 
     */
    public function titulo($report) {
        $title = $report->analyse((string) $this->data->chartTitle->titleExpression);
        
        $letraSize = isset($this->data->chartTitle->font['size']) ? (float) $this->data->chartTitle->font['size']: 12;

        if ($title != '') {
            $tcolor = isset($this->data->chartTitle['color']) ? $this->toColor((string) $this->data->chartTitle['color']) : array(0, 0, 0);
            $this->charObj->setFontProperties("Vendor/pChart/Fonts/tahoma.ttf", $letraSize);
            $this->charObj->drawTitle(10, 20, $title, $tcolor[0], $tcolor[1], $tcolor[2]);            
        }
    }

    /**
     * Devuelve la opacidad con que se dibujarán las series.
     * 
     * @return float La Opacidad de las areas dibujadas por las series un
     * valor entre 0 y 100
     */
    public function foregroundAlpha() {
        return isset($this->plot->plot['foregroundAlpha']) ? (float) $this->plot->plot['foregroundAlpha'] * 100 : 100;
    }

    /**
     * Establece el gradiente de colores con que se debe dibujar
     * la gráfica.
     */
    public function gradientColor() {
        $color1 = array(110, 100, 10);
        $color2 = array(50,200, 255);

        $this->charObj->createColorGradientPalette($color1[0], $color1[1], $color1[2], $color2[0], $color2[1], $color2[2], 5);
    }

    /**
     * Dibuja la leyenda de la gráfica.
     * Por defecto se dibuja con el fondo en blanco y con el texto en negro.
     */
    public function leyenda() {
        if ($this->isShowLegend()) {
            // Text Label Color
            $TLcolor = isset($this->data->chartLegend) && isset($this->data->chartLegend['textColor']) ? (string) $this->data->chartLegend['textColor'] : FALSE;

            //Label Background Color
            $LBcolor = isset($this->data->chartLegend) && isset($this->data->chartLegend['backgroundColor']) ? (string) $this->data->chartLegend['backgroundColor'] : FALSE;

            if ($TLcolor)
                $TLcolor = $this->toColor($TLcolor);
            if ($LBcolor)
                $LBcolor = $this->toColor($LBcolor);

            $this->charObj->setFontProperties("Vendor/pChart/Fonts/tahoma.ttf", 10);
            $this->charObj->drawLegend($this->dimencion['width'] - $this->legend_dimencion['width'] + 6, 50, $this->charData->GetDataDescription(), $LBcolor[0] ? $LBcolor[0] : 255, $LBcolor[1] ? $LBcolor[1] : 255, $LBcolor[2] ? $LBcolor[2] : 255, 255, 255, 255, $TLcolor[0] ? $TLcolor[0] : 0, $TLcolor[1] ? $TLcolor[1] : 0, $TLcolor[2] ? $TLcolor[2] : 0);
        }
    }

    /**
     * Calcula las dimenciones de la leyenda.  
     * 
     * @return array --Array['width'] = whidth -- Array['height'] = height --
     */
    public function leyenda_dimencion($pieArray = FALSE) {
        $legendSize = $this->charObj->getLegendBoxSize($this->charData->GetDataDescription(),$pieArray ? $pieArray : FALSE);
        return array('width' => $legendSize[0] + 6, 'height' => $legendSize[1] + 6);
    }

    /**
     * Devuelve el valor de la propiedad -isShowLegend-.
     * 
     * @return boolean Valor de -isShowLegend-
     */
    public function isShowLegend() {
        return isset($this->data['isShowLegend']) &&
                (string) $this->data['isShowLegend'] == 'false' ? FALSE : TRUE;
    }

    /**
     * Configura los datos a graficar.
     * 
     * @param \ReportExpress\Core\ReportExpress $report 
     */
    public function configData($report) {

        $name = (string) $this->dataSet->dataset->datasetRun['subDataset'];

        if ($name == "") {
            $data = $report->get('values');
        } else {
            $data = $report->get('dataset', $name)->getData();
            $values = $report->get('values');
            $report->set('values', $data);
        }

        $pointer = $report->get('ownvariables', 'REPORT_COUNT');

        for ($index = 0; $index < count($data); $index++) {

            $report->set('ownvariables', $index, 'REPORT_COUNT');

            foreach ($this->dataSet->categorySeries as $c => $category) {
                $points[$report->analyse((string) $category->seriesExpression)][] = (float) $report->analyse((string) $category->valueExpression);
            }

            $categoryAxys [] = $report->analyse((string) $this->dataSet->categorySeries->categoryExpression);
        }

        //restauramos los valores originales
        $report->set('ownvariables', $pointer, 'REPORT_COUNT');
        if ($name != "") {
            $report->set('values', $values);
        }

        foreach ($points as $serie => $array) {
            $this->charData->AddPoint($array, $serie);
        }
        $this->charData->AddAllSeries();
        $this->charData->AddPoint($categoryAxys, 'Category');
        $this->charData->SetAbsciseLabelSerie('Category');

        foreach ($points as $serie => $array) {
            $this->charData->SetSerieName($serie, $serie);
        }
        return array();
    }

    /**
     * Prepara la imagen antes de dibujar la gáfica.
     * 
     * @param \ReportExpress\Core\ReportExpress $report 
     */
    public function preRender($report) {

        $daraDescription = $this->configData($report);

        $this->charObj = new pChart($this->dimencion['width'], $this->dimencion['heigth']);

        $this->charObj->setFontProperties("Vendor/pChart/Fonts/tahoma.ttf", 10);
        
        $this->legend_dimencion = $this->leyenda_dimencion($daraDescription ? $daraDescription : FALSE);

        $this->charObj->drawFilledRoundedRectangle(0, 0, $this->dimencion['width'], $this->dimencion['heigth'], 5, 255, 255, 255);

        $this->gradientColor();
    }

    /**
     * Prepara la imagen antes de dibujar la gáfica.
     * En el caso de las gráficas de Barra, Linea, Area.
     * 
     * @param \ReportExpress\Core\ReportExpress $report 
     * @param constant $scale Constante utilizada por pChart para crear
     * la escala.
     */
    public function preRenderBarLineArea($scale = SCALE_NORMAL) {

        $this->charObj->setGraphArea(50, 30, $this->dimencion['width'] - $this->legend_dimencion['width'], $this->dimencion['heigth'] - 20);

        $this->charObj->drawGraphArea(255, 255, 255, TRUE);

        $this->charObj->drawScale($this->charData->GetData(), $this->charData->GetDataDescription(), $scale, 150, 150, 150, TRUE, 0, 2, TRUE);

        $this->charObj->drawGrid(4, TRUE, 230, 230, 230, 50);

        // Draw the 0 line
        $this->charObj->setFontProperties("Vendor/pChart/Fonts/tahoma.ttf", 8);              
        $this->charObj->drawTreshold(0, 143, 55, 72, TRUE, TRUE);
    }

    /**    
     * Añade la imagen resultante de la gráfica al reporte.
     * 
     * @param \ReportExpress\Core\ReportExpress $report
     * @param int $x
     * @param int $y
     * @param \pChart $picture
     */
    public function reportAdd($report, $x, $y, $picture) {

        //creamos un nombre unico para el chart
        $resource = $report->get('path') . rand(0, time()) . '.JPEG';

        //lo creamos y lo guardamos
        $picture->render($resource);

        $report->get('pdf')->setJPEGQuality(100);

        //lo incluimos en el pdf
        $report->get('pdf')->Image($resource, $this->x() + $x, $this->y() + $y, $this->width(), $this->height(), '', '', '', true, 5000);

        //lo borramos
        @unlink($resource);
    }

}

?>
