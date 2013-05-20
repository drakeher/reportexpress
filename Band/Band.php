<?php

namespace ReportExpress\Band;

use ReportExpress\Component\Rectangle,
    ReportExpress\Component\StaticText,
    ReportExpress\Component\TextField,
    ReportExpress\Component\Line,
    ReportExpress\Component\Image,
    ReportExpress\Component\Ellipse,
    ReportExpress\Component\Charts\PieChart,
    ReportExpress\Component\Charts\BarChart,
    ReportExpress\Component\Charts\LineChart,
    ReportExpress\Component\Charts\StakedBarChart,
    ReportExpress\Component\Charts\AreaChart,
    ReportExpress\Component\Charts\Pie3DChart;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Band
 *
 * @author osley.rivera
 */
class Band {

    /**
     * Datos de la banda provenientes del jrxml.
     * @var \SimpleXMLElement 
     */
    protected $data = NULL;

    /**
     * Lista de componentes a renderear.
     * @var array 
     */
    protected $component = array();

    /**
     * La altura maxima que alcanzan los componentes.
     * @var int 
     */
    protected $maxy = 0;

    /**
     * Lista de componentes a renderear.
     * @var array 
     */
    protected $after = array();

    /**
     * 
     */
    public function __construct($band) {

        $this->data = $band;

        $this->component = $this->collect($band);

        $this->solveComponent();
    }

    /**
     * Colecciona los componentes que contiene la banda.
     * 
     * @param \SimpleXMLElement $band La banda a coleccionar.
     * @return array La lista de componentes recolectados.
     */
    public static function collect($band) {
        $component = array();
        foreach ($band->children() as $name => $elem) {
            switch ($name) {
                case 'rectangle':
                    $component[] = new Rectangle($elem);
                    break;
                case 'staticText':
                    $component[] = new StaticText($elem);
                    break;
                case 'textField':
                    $component[] = new TextField($elem);
                    break;
                case 'line':
                    $component[] = new Line($elem);
                    break;
                case 'ellipse':
                    $component[] = new Ellipse($elem);
                    break;
                case 'image':
                    $component[] = new Image($elem);
                    break;

                case 'pie3DChart':
                    $component[] = new Pie3DChart($elem->pie3DPlot, $elem->pieDataset, $elem->chart);
                    break;
                case 'pieChart':
                    $component[] = new PieChart($elem->piePlot, $elem->pieDataset, $elem->chart);
                    break;
                case 'lineChart':
                    $component[] = new LineChart($elem->linePlot, $elem->categoryDataset, $elem->chart);
                    break;
                case 'barChart':
                    $component[] = new BarChart($elem->barPlot, $elem->categoryDataset, $elem->chart);
                    break;
                case 'stackedBarChart':
                    $component[] = new StakedBarChart($elem->barPlot, $elem->categoryDataset, $elem->chart);
                    break;
                case 'areaChart':
                    $component[] = new AreaChart($elem->areaPlot, $elem->categoryDataset, $elem->chart);
                    break;
           
                default:
                    //no interesa procesar el tag
                    break;
            }
        }

        return $component;
    }

    /**
     * Davuelve la altura de la banda.
     * 
     * @return int La altura
     */
    public function height() {
        return (int) $this->data['height'];
    }

    /**
     * Devuelve el splitType de la banda.
     * 
     * @return string El splitType
     */
    public function split() {
        return (string) $this->data['splitType'];
    }

    /**
     * Devuelve el resultado de la expresion en el atributo printWhenExpression
     * si ha sido modificado, sino devuelve TRUE.
     * @param \ReportExpress\Core\ReportExpress $report
     * @return boolean El resultado de la expresion.
     */
    public function printWhenExpression($report) {
        return isset($this->data->printWhenExpression) ? (boolean) $report->analyse((string) $this->data->printWhenExpression) : TRUE;
    }

    /**
     * Renderea los componentes de la Banda.
     * 
     * @param \ReportExpress\Core\ReportExpress $report EL reporte donde se renderea.
     * @param \ReportExpress\Core\Point $point El punto donde comienza a renderearse.
     */
    public function render($report, $point) {

        $this->height = 0;

        //rendereamos component
        $this->realRender('component', $report, $point);
        //redereamos after
        $this->realRender('after', $report, $point);
    }

    /**
     * Maneja como se muestran los componentes dentro de la banda.
     * 
     * @param string $component El tipo de componente que se muestra (component o after).
     * @param \ReportExpress\Core\ReportExpress $report El reporte donde se renderea.
     * @param \ReportExpress\Core\Point $point El pundo
     */
    public function realRender($component, $report, $point) {

        foreach ($this->$component as $c) {

            //chequeamos que se pueda imprimir
            if ($c->printWhenExpression($report) == FALSE) {
                continue;
            }

            if ($c->render($report, $point->x, $component == 'component' ? $point->y : (($point->y + $this->height) - $c->height()) - $c->y()) == FALSE) {
                //este es el caso en que el componente debe ser rendeareado
                //al final de la pagina, asi nos saltamos los pasos siguientes
                //y lo mostramos al final.
                continue;
            }

            $lastheight = $report->get('pdf')->getY() - $point->y;

            if ($lastheight > $this->height) {
                $this->height = $lastheight;
            }
        }
    }

    /**
     * Separa los componentes en after y component, lo cual determina quien 
     * se debe mostrar primero.
     */
    public function solveComponent() {

        $component = array();

        foreach ($this->component as $c) {

            if ($c->positionType() == 'FixRelativeToBottom') {
                $this->after [] = $c;
            } else {
                $component [] = $c;
            }

            $nmaxy = $c->y() + $c->height();

            if ($nmaxy > $this->maxy) {
                $this->maxy = $nmaxy;
            }
        }

        //componentes que se renderean primero
        $this->component = $component;
    }

}

?>
