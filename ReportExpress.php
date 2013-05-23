<?php

namespace ReportExpress;

require_once(dirname(__FILE__) . '/Vendor/tcpdf/tcpdf.php');

use ReportExpress\Component\DataSet,
    ReportExpress\Core\Property,
    ReportExpress\Core\Point,
    ReportExpress\Variable\Variable,
    ReportExpress\Variable\Sum,
    ReportExpress\Variable\Average,
    ReportExpress\Variable\Count,
    ReportExpress\Band\Band,
    ReportExpress\Band\Detail,
    ReportExpress\Band\Group;


/**
 * ReportExpress Class
 * 
 * This class contains all control of the library.
 * 
 * @category    Library
 * @package     ReportExpress
 * @version     1.0
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Creative Commons (CC) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
class ReportExpress {

   /**
    * Version of the library. 
    * @var string 
    */
   private $version = '1.0';

   /**
    * Instance of the class TCPDF. 
    * @var \TCPDF
    */
   private $pdf;

   /**
    * jrxml data of report. 
    * @var \SimpleXMLElement
    */
   private $xml;

   /**
    * Properties of report. 
    * @var \ReportExpress\Core\Property 
    */
   private $property;

   /**
    * Background band. 
    * @var \ReportExpress\Band\Band 
    */
   private $background = NULL;

   /**
    * Title band. 
    * @var \ReportExpress\Band\Band  
    */
   private $title = NULL;

   /**
    * PageHeader band. 
    * @var \ReportExpress\Band\Band 
    */
   private $pageheader = NULL;

   /**
    * ColumnHeader band. 
    * @var \ReportExpress\Band\Band 
    */
   private $columnheader = NULL;

   /**
    * Detail band. 
    * @var \ReportExpress\Band\Band  
    */
   private $detail = NULL;

   /**
    * ColumnFooter band. 
    * @var \ReportExpress\Band\Band  
    */
   private $columnfooter = NULL;

   /**
    * PageFooter band. 
    * @var \ReportExpress\Band\Band  
    */
   private $pagefooter = NULL;

   /**
    * Summary band. 
    * @var \ReportExpress\Band\Band 
    */
   private $summary = NULL;

   /**
    * List of groups. 
    * @var \ReportExpress\Band\Group  
    */
   private $groups = array();

   /**
    * Report Data. 
    * @var array  
    */
   private $values = NULL;

   /**
    * Regular expressions for variables iReport map. 
    * @var array  
    */
   private $varexpr = array('/\$V\{PAGE_NUMBER\}/', '/\$V\{REPORT_COUNT\}/', '/\$V\{PAGE_COUNT\}/');

   /**
    * iReport variable values​​. 
    * @var array 
    */
   private $ownvariables = array('PAGE_NUMBER' => 0, 'REPORT_COUNT' => 0, 'PAGE_COUNT' => 1);

   /**
    * External variables defined in the report. 
    * @var array  
    */
   private $variables = array();

   /**
    * Parameters used by the report. 
    * @var array  
    */
   private $parameter = array();

   /**
    * Save all components that need to be evaluated at the end of the rendering of the page.
    * @var array  
    */
   private $evaluatePage = array();

   /**
    * Indicates that can if they can be evaluated variables page. 
    * @var boolean  
    */
   private $readyPage = FALSE;

   /**
    * Path to the report. 
    * @var string
    */
   private $path = '';

   /**
    * Extention of the report, it defaults jrxml. 
    * @var string|jrxml 
    */
   private $ext = 'jrxml';

   /**
    * List dataset used in the report. 
    * @var \ReportExpress\Component\DataSet  
    */
   private $dataset = array();

   /**
    * Total items in values. 
    * @var int
    */
   private $total = 0;

   public function __construct() {
      
   }

   /**
    * Returns a property of the report.
    * 
    * @param string $prop Name of attribute.
    * @param string $name [optional] Name attribute property.
    * @return mixed The value in $prop or NULL if not found.
    */
   public function get($prop, $name = NULL) {
      $prop = $this->$prop;
      return $name ? isset($prop[$name]) ? $prop[$name] : NULL  : $prop;
   }

   /**
    * Change attribute the report.
    * 
    * @param string $prop Name of attribute.
    * @param mixed $value The value of the attribute.
    * @param string $name [optional] The name of the attribute property.
    */
   public function set($prop, $value, $name = NULL) {
      $prop = &$this->$prop;
      $name ? $prop[$name] = $value : $prop = $value;
   }

   /**
    * Configure the properties of the report to display.
    *  
    * @param string $path Path to the report.
    * @param string $ext [optional] Extention of the report.
    */
   public function setConfig($path, $ext = '.jrxml') {
      $this->path = realpath($path);
      $this->ext = $ext;
   }

   /**
    * Load the configuration of the report.
    * 
    * @param string $name Filename
    * @param array $data Data of report.
    * @param array $parameters [optional] Parameters of report.
    */
   public function load($name, $data, $parameters = NULL) {
      //se carga el xml
      $path = $this->path . '/' . $name . $this->ext;

      if (file_exists($path)) {
	 $this->xml = simplexml_load_file($path);
      } else {
	 exit('Failed to open the XML: ' . $path . '.');
      }

      //se actualizan los datos a motrar
      $this->values = $data;

      //cantidad de elementos
      $this->total = count($data);

      //recolectamos los parametros
      if ($parameters) {
	 $this->collectParameters($parameters);
      }

      //recolectamos las variables
      $this->variables = $this->collectVariables($this->xml);

      //parseamos el xml para llevalo a clases php
      $this->parser();
   }

   /**
    * Initializes the parameters that apply to the report.
    * 
    * @param array $parameters The parameters to be collected.
    */
   public function collectParameters($parameters) {
      foreach ($this->xml->parameter as $value) {
	 $name = (string) $value['name'];
	 if (isset($parameters[$name])) {
	    $this->parameter[$name] = $parameters[$name];
	 }
      }
   }

   /**
    * Collect foreign variables.
    * 
    * @param \SimpleXMLElement $map The xml mapping, extract the variables.
    * @return array The collected variables
    */
   public static function collectVariables($map) {

      $variables = array();
      foreach ($map->variable as $variable) {

	 switch ($variable['calculation']) {
	    case 'Sum':
	       $var = new Sum($variable);
	       break;
	    case 'Average':
	       $var = new Average($variable);
	       break;
	    case 'Count':
	       $var = new Count($variable);
	       break;
	    default ://Nothing
	       $var = new Variable($variable);
	       break;
	 }
	 $variables[(string) $variable['name']] = $var;
      }

      return $variables;
   }

   /**
    * Add a dataset.
    * 
    * @param string $name Dataset name.
    * @param array $data Dataset data.
    */
   public function addDataset($name, $data) {
      $this->dataset [$name] = new DataSet($name, $data);
   }

   /**
    * Parse the xml getting different parts of the report.
    */
   public function parser() {

      //property
      $this->property = new Property($this->xml);

      //Background
      if ($this->exist('background')) {
	 $this->background = new Band($this->xml->background->band);
      }

      //Title
      if ($this->exist('title')) {
	 $this->title = new Band($this->xml->title->band);
      }

      //Page Header
      if ($this->exist('pageHeader')) {
	 $this->pageheader = new Band($this->xml->pageHeader->band);
      }

      //Column Header
      if ($this->exist('columnHeader')) {
	 $this->columnheader = new Band($this->xml->columnHeader->band);
      }

      //Detail
      if ($this->exist('detail')) {
	 $this->detail = new Detail($this->xml);
      }

      //Column Footer
      if ($this->exist('columnFooter')) {
	 $this->columnfooter = new Band($this->xml->columnFooter->band);
      }

      //Page Footer
      if ($this->exist('pageFooter')) {
	 $this->pagefooter = new Band($this->xml->pageFooter->band);
      }

      //Summary
      if ($this->exist('summary')) {
	 $this->summary = new Band($this->xml->summary->band);
      }

      //Groups
      foreach ($this->xml->group as $group) {
	 $this->groups [] = new Group($group);
      }
   }

   /**
    * Build the report.
    */
   public function build() {

      $this->createPage();

      $this->renderTitle();

      $this->renderPageHeader();

      $this->renderColumnHeader();

      if ($this->detail) {

	 $point = $this->position['detail'];

	 while (TRUE) {

	    //render detail
	    if ($this->detail->render($this, $point) == 'newpage') {
	       $this->newPage();
	       $this->renderColumnHeader();

	       $point = $this->position['detail'];
	       $this->detail->render($this, $point);
	    }

	    if ($this->index() + 1 >= $this->total) {
	       $this->renderColumnFooter();
	       break;
	    } else {
	       $this->next();
	    }

	    //queda espacio para otra fila ?
	    if ($this->detail->maxRow() >= $point->mxb) {
	       $this->newPage();
	       $this->renderColumnHeader();
	       $point = $this->position['detail'];
	    }

	    //se terminaron los groupfooter ?
	    if (!$this->detail->renderGroupFooter($this, $point)) {
	       $this->newPage();
	       $this->detail->renderGroupFooter($this, $point);
	    }
	 }
      }

      //si existen grupos verificamos que no queden footer por mostrar
      if ($this->hasGroups()) {

	 $point = new Point($this->position ['detail']->x, $this->detail->lasty(), $this->position['detail']->mxb);

	 for ($i = count($this->groups) - 1; $i >= 0; $i--) {

	    $group = $this->groups[$i];

	    if ($group->render($this, $point, 'footer') == FALSE) {
	       $this->renderPageFooter();
	       $this->createPage();
	       $this->renderPageHeader();
	       $point = new Point($this->position ['detail']->x, $this->position ['detail']->y, $this->position['detail']->mxb);
	       $group->render($this, $point, 'footer');
	    }
	 }

	 $this->detail->setLasty($point->y);
      }

      if ($this->summary) {
	 if (!$this->detail || $this->summary->height() < $this->position['detail']->mxb - $this->detail->lasty()) {
	    $this->summary->render($this, new Point($this->property->leftMargin, $this->detail ? $this->detail->lasty() : $this->position['summary']->y));
	 } else {
	    $this->createPage();
	    $this->renderPageHeader();
	    $this->summary->render($this, new Point($this->property->leftMargin, $this->detail ? $this->detail->lasty() : $this->position['summary']->y));
	 }
      }

      $this->renderPageFooter();
   }

   /**
    * Determine the positions of each band on the current page.
    */
   public function calculatedPositionBands() {

      $x = $this->property->leftMargin; // x top
      $y = $this->property->topMargin; // y top

      $name = $this->property->orientation == "P" ? 'pageHeight' : 'pageWidth';

      // max y
      $my = $this->property->$name - $this->property->bottomMargin;

      $this->position = array();

      if ($this->title) {
	 if ($this->ownvariables['PAGE_NUMBER'] == 1) {
	    $this->position['title'] = new Point($x, $y);
	    $y += $this->title->height();
	 }
      }

      if ($this->pageheader) {
	 $this->position['pageheader'] = new Point($x, $y);
	 $y += $this->pageheader->height();
      }

      if ($this->columnheader && $this->workIndex()) {
	 $this->position['columnheader'] = new Point($x, $y);
	 $y += $this->columnheader->height();
      }

      $mxb = ($my - (($this->pagefooter ? $this->pagefooter->height() : 0 ) + ($this->columnfooter && $this->workIndex() ? $this->columnfooter->height() : 0)));

      if ($this->detail) {

	 if (!$this->workIndex()) {
	    $this->detail->setLasty($y);
	 }
	 $this->position['detail'] = new Point($x, $y, $mxb);
	 $y = $mxb;
      } else {
	 $this->position['summary'] = new Point($x, $y);
      }

      if ($this->columnfooter && $this->workIndex()) {
	 $this->position['columnfooter'] = new Point($x, $y);
	 $y += $this->columnfooter->height();
      }

      if ($this->pagefooter) {
	 $this->position['pagefooter'] = new Point($x, $y);
      }
   }

   /**
    * Determine if there is the band.
    * 
    * @param string $band Band name
    * @return boolean TRUE if it exists, FALSE otherwise
    */
   public function exist($band) {
      return isset($this->xml->$band) && count($this->xml->$band->band->children()) > 0;
   }

   /**
    * Determine if there is the groups.
    * 
    * @return boolean TRUE if it exists, FALSE otherwise
    */
   public function hasGroups() {
      return count($this->groups) > 0;
   }

   /**
    * Render the components that are left for last, because it contained 
    * variables that were dependent on a final value in page.
    */
   public function evaluationTime() {

      //rendereamos los componenetes que se evaluan al final de la pagina
      if (count($this->evaluatePage) > 0) {

	 //obtenemos la posicion actual del puntero y la guardamos
	 $pointer = $this->ownvariables['REPORT_COUNT'];

	 //indicamos que pueden ser evaluadas las variables
	 $this->readyPage = TRUE;

	 foreach ($this->evaluatePage as $var) {
	    $this->ownvariables['REPORT_COUNT'] = $var['index'];
	    $var['object']->render($this, $var['point']->x, $var['point']->y);
	 }

	 //restauramos los valores iniciales del reporte
	 $this->ownvariables['REPORT_COUNT'] = $pointer;
	 $this->readyPage = FALSE;
	 $this->evaluatePage = array();
      }
   }

   /**
    * Evaluate all variables.
    */
   public function evaluationVariable() {
      foreach ($this->variables as $var) {
	 $var->evaluate($this);
      }
   }

   /**
    * Lets analyze the expression of ireport sent showing the correct values ​​
    * and associated with each expression.
    * 
    * @param string $expression The expression to be analyzed.
    * @return mixed The result value.
    */
   public function analyse($expression) {

      //convertimos a texto plano
      $plain = $this->plainText($expression);

      //sustituimos las variables nativas
      $native = preg_replace($this->varexpr, array('{:pnp:}', $this->ownvariables['REPORT_COUNT'] + 1, '{:ptp:}'), $plain);

      //sustituimos las variables foraneas
      $foreign = $this->replaceKey('V', $this->variables, $native, TRUE);

      // si es un evaluate los valores deven ser devueltos segun su tipo de datos
      //sustituimos los parametros
      $params = $this->replaceKey('P', $this->parameter, $foreign);

      //sustituimos los datos de la tupla
      $result = $this->replaceKey('F', $this->values[$this->ownvariables['REPORT_COUNT']], $params);

      //evaluamos la expresion en php si existe
      if (preg_match('/\$evaluate/', $result)) {

	 //disponemos algunas variables en contexto
	 $index = $this->ownvariables['REPORT_COUNT'];
	 $values = $this->values;
	 $params = $this->parameter;

	 error_reporting(0);
	 eval($result);
	 error_reporting(E_ALL);
      }

      return isset($evaluate) ? $evaluate : $result;
   }

   /**
    * Converts a string to plain text, removing the quotes and 
    * signs + (concatenation).
    * 
    * @param string $text The text.
    * @return string Plaintext.
    * @example For this chain $ V {all} + "Hello" return $ V {all} Hello.
    */
   public function plainText($text) {
      //@TODO arreglar la expresion regular en un texto con comillas 
      //se las come entonces se necesita ponerlas doble para que funcione.
      return preg_replace(array('/^"|"$/', '/\}+\s*+\++(\s*+"|")/', '/"+\s*+\++\s*+\$/'), array('', '}', '$'), $text);
   }

   /**
    * Replaces the object keys that appear in the string by value 
    * and has value true if method calls the object's value method,
    * this is the case where $ object is a list of variables.
    *
    * @param string $type The type <ul><li>V: Variable</li><li>P: Parameter</li><li>F: Field</li></ul>
    * @param mixed $object The object with the values ​​to replace.
    * @param string $string The resulting text string.
    * @param boolean $method [optional] If non-NULL value then call the method.
    * @return string The resulting text string.
    */
   public function replaceKey($type, $object, $string, $method = NULL) {
      $keys = array_keys($object);
      foreach ($keys as $value) {
	 $string = preg_replace('/\$' . $type . '\{' . $value . '\}/', $method ? $object[$value]->value() : $object [$value], $string);
      }
      return $string;
   }

   /**
    * Indicates if data pointer is traveling.
    * 
    * @return boolean TRUE if it is exist, FALSE otherwise.
    */
   public function workIndex() {
      return $this->ownvariables['REPORT_COUNT'] < count($this->values);
   }

   /**
    * Render the PDF
    * 
    * @param string $method Indicates how the report is displayed.
    * @param string $path [optional] Indicates where the report is saved.
    */
   public function show($method = 'I', $path = NULL) {

      $p = $this->property;

      $format = $p->orientation == "P" ? array($p->pageWidth, $p->pageHeight) :
	      array($p->pageHeight, $p->pageWidth);

      $this->pdf = new \TCPDF($p->orientation, $p->unit, $format, $p->unicode, $p->encoding, $p->diskcache);

      $this->pdf->setPrintHeader(FALSE);
      $this->pdf->setPrintFooter(FALSE);
      $this->pdf->SetLeftMargin($p->leftMargin);
      $this->pdf->SetRightMargin($p->rightMargin);
      $this->pdf->SetTopMargin($p->topMargin);
      $this->pdf->SetAutoPageBreak(FALSE);
      $this->pdf->getAliasNbPages();

      $this->build();
      $this->pdf->Output($path ? $path : 'ReportExpress.pdf', $method);
   }

   /**
    * Resets the values ​​of the variables that are of type $type.
    * 
    * @param string $type The type <ul><li>Group</li><li>Report</li><li>page</li><li>None</li><li>Column</li></ul>.
    * @param string $name [optional] Just in case that $type is Group, here 
    * indicates the group name.
    */
   public function resetVariables($type, $name = NULL) {
      foreach ($this->variables as $var) {
	 $var->reset($type, $name);
      }
   }

   /**
    * Returns the position of the  pointer that runs the data.
    * 
    * @return int The index.
    */
   public function index() {
      return $this->ownvariables['REPORT_COUNT'];
   }

   /**
    * Index increases by one.
    */ public function next() {
      $this->ownvariables['REPORT_COUNT']++;
   }

   /**
    * Returns the total number of elements containing values.
    * 
    * @return int The total.
    */
   public function total() {
      return $this->total;
   }

   /**
    * Render the title band.
    */
   public function renderTitle() {
      if ($this->title) {
	 $this->title->render($this, $this->position['title']);
      }
   }

   /**
    * Render the PageHeader band.
    */
   public function renderPageHeader() {
      if ($this->pageheader) {
	 $this->pageheader->render($this, $this->position['pageheader']);
      }
   }

   /**
    * Render the ColumnHeader band.
    * 
    * @return void
    */
   public function renderColumnHeader() {
      if ($this->columnheader) {
	 $this->columnheader->render($this, $this->position['columnheader']);
      }
   }

   /**
    * Render the ColumnFooter band.
    */
   public function renderColumnFooter() {
      if ($this->columnfooter) {
	 $this->columnfooter->render($this, $this->position['columnfooter']);
      }
   }

   /**
    * Render the PageFooter band.
    */
   public function renderPageFooter() {
      if ($this->pagefooter) {
	 $this->pagefooter->render($this, $this->position['pagefooter']);
      }
   }

   /**
    * Create a page in the pdf and run a recurring logic in each creation.
    */
   public function createPage() {
      //evaluamos los componentes que faltan antes de crear una nueva pagina
      $this->evaluationTime();

      $this->pdf->AddPage();
      $this->ownvariables['PAGE_NUMBER']++;

      //reset todas las variables de tipo page
      $this->resetVariables('Page');

      if ($this->background) {
	 $this->background->render($this, new Point($this->property->leftMargin, $this->property->topMargin));
      }

      //calculamos las posiciones de las bandas
      $this->calculatedPositionBands();
   }

   /**
    * Renders matching bands on the end and the beginning of a page.
    */
   public function newPage() {
      $this->renderColumnFooter();
      $this->renderPageFooter();
      $this->createPage();
      $this->renderPageHeader();
   }

}

?>
