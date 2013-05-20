<?php

namespace ReportExpress;

require_once(dirname(__FILE__) . '/Vendor/tcpdf/tcpdf.php');

use ReportExpress\Component\DataSet,
    ReportExpress\Variable\Variable,
    ReportExpress\Core\Property,
    ReportExpress\Core\Point,
    ReportExpress\Variable\Sum,
    ReportExpress\Variable\Average,
    ReportExpress\Variable\Count,
    ReportExpress\Band\Band,
    ReportExpress\Band\Detail,
    ReportExpress\Band\Group;

/**
 * ReportExpress
 *
 * @package		ReportExpress
 * @author		Sparkle Team
 * @copyright           Copyright (c) 2013, ReportExpress.
 * @license		http://reportexpress.com/license.html
 * @link		http://reportexpress.com
 * @since		Version 1.0
 * @filesource          ReportExpress/Core/ReportExpress.php
 */
class ReportExpress {

    /**
     * Version de la libreria.
     * @var string 
     */
    private $version = '1.0';

    /**
     * Objeto pdf.
     * @var \TCPDF 
     */
    private $pdf;

    /**
     * Los datos jrxml del reporte
     * @var \SimpleXMLElement 
     */
    private $xml;

    /**
     * Propiedade del reporte.
     * @var Property 
     */
    private $property;

    /**
     * Banda background.
     * @var Band 
     */
    private $background = NULL;

    /**
     * Banda title.
     * @var Band 
     */
    private $title = NULL;

    /**
     * Banda pageheader.
     * @var Band 
     */
    private $pageheader = NULL;

    /**
     * Banda columnheader.
     * @var Band 
     */
    private $columnheader = NULL;

    /**
     * Banda detail.
     * @var Detail 
     */
    private $detail = NULL;

    /**
     * banda columnfooter.
     * @var Band 
     */
    private $columnfooter = NULL;

    /**
     * Banda pagefooter.
     * @var Band 
     */
    private $pagefooter = NULL;

    /**
     * Banda summary.
     * @var Band 
     */
    private $summary = NULL;

    /**
     * Lista de grupos.
     * @var Group 
     */
    private $groups = array();

    /**
     * Los datos que utiliza el reporte.
     * @var array 
     */
    private $values = NULL;

    /**
     * Expresiones regulares para mapear las variables del ireport.
     * @var array
     */
    private $varexpr = array('/\$V\{PAGE_NUMBER\}/', '/\$V\{REPORT_COUNT\}/', '/\$V\{PAGE_COUNT\}/');

    /**
     * Variables del ireport on valores.
     * @var array
     */
    private $ownvariables = array('PAGE_NUMBER' => 0, 'REPORT_COUNT' => 0, 'PAGE_COUNT' => 1);

    /**
     * Variables externas definidas en el reporte.
     * @var array
     */
    private $variables = array();

    /**
     * Parametros que usa el reporte.
     * @var array 
     */
    private $parameter = array();

    /**
     * Guarda todos los componentes que requieren ser evaluados al final 
     * del rendereo de la pagina.
     * @var array
     */
    private $evaluatePage = array();

    /**
     * Indica que pueden si pueden ser evaluadas las variables de pagina.
     * @var boolean 
     */
    private $readyPage = FALSE;

    /**
     * La direccion hasta el reporte.
     * @var string 
     */
    private $path = '';

    /**
     * Extencion del reporte, por defecto se usa jrxml.
     * @var string 
     */
    private $ext = '';

    /**
     * Lista de dataset usados en el reporte.
     * @var \ReportExpress\Component\DataSet 
     */
    private $dataset = array();

    /**
     * Total de elementos en values.
     * @var int 
     */
    private $total = 0;

    public function __construct() {
        
    }

    /**
     * Devuelve una propiedad del reporte.
     * 
     * @param string $prop Nombre del atributo.
     * @param string $name Nombre de la propiedad del atributo.
     * @return mixed El valor en $prop o NULL si no lo encuentra.
     */
    public function get($prop, $name = NULL) {
        $prop = $this->$prop;
        return $name ? isset($prop[$name]) ? $prop[$name] : NULL  : $prop;
    }

    /**
     * Permite cambiar un atributo del reporte.
     * 
     * @param string $prop El atributo a cambiar.
     * @param mixed $value El valor del atributo.
     * @param string $name El nombre de la propiedad del atributo.
     */
    public function set($prop, $value, $name = NULL) {
        $prop = &$this->$prop;
        $name ? $prop[$name] = $value : $prop = $value;
    }

    /**
     * Configurar las propiedades del reporte a mostrar.
     *  
     * @param string $path La direccion del reporte.
     * @param string $ext La extencion del reporte.
     */
    public function setConfig($path, $ext = 'jrxml') {
        $this->path = $path;
        $this->ext = $ext;
    }

    /**
     * Se carga la configuracion del reporte.
     * 
     * @param string $name Nombre del fichero.
     * @param array $data Datos a usar en el reporte.
     * @param array $parameters Parametros que se usan en el reporte.
     */
    public function load($name, $data, $parameters = NULL) {

        //se carga el xml
        $this->xml = simplexml_load_file("$this->path$name.$this->ext");

        //se actualizan los datos a mostrar
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
     * Inicializa los parametros que correspondan al reporte.
     * 
     * @param array $parameters Los parametros a recolectar.
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
     * Se recolectan las variables foraneas.
     * 
     * @param \SimpleXMLElement $map El xml mapeado, al que se le extraen las variables.
     * @return array(\ReportExpress\Variable\Variable) Las variables recolectadas.F
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
     * Adiciona un dataset.
     * 
     * @param string $name Nombre del dataset.
     * @param array $data Datos del dataset.
     */
    public function addDataset($name, $data) {
        $this->dataset [$name] = new DataSet($name, $data);
    }

    /**
     * Parsea el xml obteniendo las distintas partes del reporte.
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
     * Contruye el reporte.
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

            $point = new Point($this->position['detail']->x, $this->detail->lasty(), $this->position['detail']->mxb);

            for ($i = count($this->groups) - 1; $i >= 0; $i--) {

                $group = $this->groups[$i];

                if ($group->render($this, $point, 'footer') == FALSE) {
                    $this->renderPageFooter();
                    $this->createPage();
                    $this->renderPageHeader();
                    $point = new Point($this->position['detail']->x, $this->position['detail']->y, $this->position['detail']->mxb);
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
     * Determina las posiciones de cada banda en la pagina actual.
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
     * Determina si existe la banda.
     * 
     * @param string $band Nombre de la banda.
     * @return boolean TRUE o FALSE.
     */
    public function exist($band) {
        return isset($this->xml->$band) && count($this->xml->$band->band->children()) > 0;
    }

    /**
     * Determina si existe grupos.
     * 
     * @return boolean TRUE 0 FALSE.
     */
    public function hasGroups() {
        return count($this->groups) > 0;
    }

    /**
     * Renderea los componentes que se dejaron para lo ultimo, porque contenian
     * variables que dependian de un valor final en pagina.
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
     * Evalua todas las variables.
     */
    public function evaluationVariable() {
        foreach ($this->variables as $var) {
            $var->evaluate($this);
        }
    }

    /**
     * Permite analizar la expresion enviada del ireport mostrando los valores
     * correctos y asociados a cada expresion.
     * 
     * @param string $expression La expresion a analizar.
     * @return mixed El valor resultante.
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
     * Convierte una cadena texto plano, eliminando las comillas y los signos
     * de + (concatenacion).
     * 
     * @ejemplo para esta cadena $V{total}+" Hola" devolveria $V{total} Hola
     * 
     * @param string $text El texto.
     * @return string El texto plano.
     */
    public function plainText($text) {
        //@TODO arreglar la expresion regular en un texto con comillas 
        //se las come entonces se necesita ponerlas doble para que funcione.
        return preg_replace(array('/^"|"$/', '/\}+\s*+\++(\s*+"|")/', '/"+\s*+\++\s*+\$/'), array('', '}', '$'), $text);
    }

    /**
     * Remplaza las llaves del objeto que aparecen en el string por su valor y 
     * si method tiene valor true llama al metodo value del objeto, este es el
     * caso en que $object es una lista de variables.
     * 
     * @param string $type El tipo (V=>Variable,P=>Parametro,F=>Field o Campo)
     * @param mixed $object El objeto con los valores a remplazar.
     * @param string $string La cadena de texto resultante.
     * @param boolean $method Si es distinto de NULL entonces llamar al metodo value.
     * @return string La cadena de texto resultante.
     */
    private function replaceKey($type, $object, $string, $method = NULL) {
        $keys = array_keys($object);
        foreach ($keys as $value) {
            $string = preg_replace('/\$' . $type . '\{' . $value . '\}/', $method ? $object[$value]->value() : $object[$value], $string);
        }
        return $string;
    }

    /**
     * Indica si el puntero esta recorriendo los datos.
     * 
     * @return boolean TRUE o FALSE.
     */
    public function workIndex() {
        return $this->ownvariables['REPORT_COUNT'] < count($this->values);
    }

    /**
     * Renderea el pdf.
     * 
     * @param string $method Indica la forma en que se muestra el reporte.
     */
    public function show($method = 'I', $path = NULL) {

        $p = $this->property;

        $format = $p->orientation == "P" ? array($p->pageWidth, $p->pageHeight) : array($p->pageHeight, $p->pageWidth);

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
     * Resetea los valores de las variables que sean del tipo $type.
     * 
     * @param string $type El tipo(Group,Report,Page,None,Column).
     * @param string $name Solo en caso de que $type sea Group aqui se indica
     * el nombre del grupo. Por defecto es NULL.
     */
    public function resetVariables($type, $name = NULL) {

        foreach ($this->variables as $var) {
            $var->reset($type, $name);
        }
    }

    /**
     * Devuelve la posicion del puntero que recorre los datos.
     * 
     * @return int El index.
     */
    public function index() {
        return $this->ownvariables['REPORT_COUNT'];
    }

    /**
     * Incrementa el indice en uno.
     */
    public function next() {
        $this->ownvariables['REPORT_COUNT']++;
    }

    /**
     * Devuelve el total de elementos que contiene values.
     * 
     * @return int El total.
     */
    public function total() {
        return $this->total;
    }

    /**
     * Renderea la banda title.
     */
    public function renderTitle() {
        if ($this->title) {
            $this->title->render($this, $this->position['title']);
        }
    }

    /**
     * Renderea la banda pageheader.
     */
    public function renderPageHeader() {
        if ($this->pageheader) {
            $this->pageheader->render($this, $this->position['pageheader']);
        }
    }

    /**
     * Renderea la banda columnheader.
     */
    public function renderColumnHeader() {
        if ($this->columnheader) {
            $this->columnheader->render($this, $this->position['columnheader']);
        }
    }

    /**
     * Permite crear una pagina en el pdf y ejecutar una logica recurrente en 
     * cada creacion.
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
     * Renderea las bandas coincidente en el fin y el inicio de una pagina.
     */
    public function newPage() {
        $this->renderColumnFooter();
        $this->renderPageFooter();
        $this->createPage();
        $this->renderPageHeader();
    }

    /**
     * Rederea la banda columnfooter.
     */
    public function renderColumnFooter() {
        if ($this->columnfooter) {
            $this->columnfooter->render($this, $this->position['columnfooter']);
        }
    }

    /**
     * Renderea la banda pagefooter.
     */
    public function renderPageFooter() {
        if ($this->pagefooter) {
            $this->pagefooter->render($this, $this->position['pagefooter']);
        }
    }

}

?>
