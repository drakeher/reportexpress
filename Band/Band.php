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


/**
 * Band Class
 * 
 * This class contains the logic of the bands
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Band
 * @version     1.0 In development. Very unstable.
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Creative Commons (CC) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
class Band {

   /** @var \SimpleXMLElement Data from the jrxml band. */
   protected $data = NULL;

   /** @var array List of components to render. */
   protected $component = array();

   /** @var int The maximum height of the components. */
   protected $maxy = 0;

   /** @var array List of components to render. */
   protected $after = array();

   /**
    * Constructor of the class.
    * 
    * @param \SimpleXMLElement $band Data from the jrxml band.
    * @return void
    */
   public function __construct($band) {
      $this->data = $band;
      $this->component = $this->collect($band);
      $this->solveComponent();
   }

   /**
    * Collect components containing the band.
    * 
    * @param \SimpleXMLElement $band The band to collect.
    * @return array The list of components collected.
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
    * Return the height of the band.
    * 
    * @return int The Height.
    */
   public function height() {
      return (int) $this->data['height'];
   }

   /**
    * Return the splitType of the band.
    * 
    * @return string The splitType.
    */
   public function split() {
      return (string) $this->data['splitType'];
   }

   /**
    * Returns the result of the expression in printWhenExpression attribute has 
    * been modified, otherwise returns TRUE.
    * 
    * @param \ReportExpress\ReportExpress $report
    * @return boolean The result of the expression.
    */
   public function printWhenExpression($report) {
      return isset($this->data->printWhenExpression) ? (boolean) $report->analyse((string) $this->data->printWhenExpression) : TRUE;
   }

   /**
    * Render the components of the band.
    * 
    * @param \ReportExpress\ReportExpress $report The report which is rendered.
    * @param \ReportExpress\Core\Point $point The point where it begins to render.
    * @return void
    */
   public function render($report, $point) {
      $this->height = 0;
      //rendereamos component
      $this->realRender('component', $report, $point);
      //redereamos after
      $this->realRender('after', $report, $point);
   }

   /**
    * Manages as shows the components within the band.
    * 
    * @param string $component The component type shown (component or after).
    * @param \ReportExpress\ReportExpress $report Where it renders a report.
    * @param \ReportExpress\Core\Point $point The point.
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
    * Separate components and component after, which determines 
    * who is to be displayed first.
    * 
    * @return void
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
