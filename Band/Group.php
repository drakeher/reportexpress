<?php

namespace ReportExpress\Band;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Group
 *
 * @author osley.rivera
 */
class Group {

    /**
     * Nombre del grupo.
     * @var string 
     */
    private $name;

    /**
     * La expresion que indica comienzo y fin del grupo.
     * @var string 
     */
    private $groupExpression;

    /**
     * Si comienza en una nueva pagina.
     * @TODO No programada.
     * @var boolean 
     */
    private $isStartNewPage;

    /**
     * Si resetea el numero de paginas.
     * @TODO No programada.
     * @var boolean 
     */
    private $isResetPageNumber;

    /**
     * @TODO No programada.
     * @var boolean 
     */
    private $isReprintHeaderOnEachPage;

    /**
     * Se comporta como una banda y contiene los componentes a mostrar
     * dentro del grupo.
     * @var Band 
     */
    private $groupHeader = NULL;

    /**
     * Se comporta como una banda y contiene los componentes a mostrar
     * dentro del grupo.
     * @var Band 
     */
    private $groupFooter = NULL;

    /**
     * Contiene los valor despues de analizado el grupo.
     * @var array 
     */
    private $value = array('header' => NULL, 'footer' => NULL);

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
     * Devuelve el nombre del grupo
     * @return string nombre.
     */
    public function name() {
        return $this->name;
    }

    /**
     * Devuelve la expresion del grupo.
     * @return string la expresion
     */
    public function groupExpression() {
        return $this->groupExpression;
    }

    /**
     * IsStartNewPage
     * @return boolean TRUE o FALSE
     */
    public function isStartNewPage() {
        return $this->isStartNewPage;
    }

    /**
     * IsResetPageNumber
     * @return boolean TRUE o FALSE
     */
    public function isResetPageNumber() {
        return $this->isResetPageNumber;
    }

    /**
     * IsReprintHeaderOnEachPage
     * @return boolean TRUE o FALSE
     */
    public function isReprintHeaderOnEachPage() {
        return $this->isReprintHeaderOnEachPage;
    }

    /**
     * Devuelve el group header del grupo.
     * @return Band
     */
    public function header() {
        return $this->groupHeader;
    }

    /**
     * Devuelve el group footer del grupo.
     * @return Band
     */
    public function footer() {
        return $this->groupFooter;
    }

    /**
     * Devuelve el valor que tiene el grupo.
     * 
     * @param string $name El nombre del grupo
     * @return mixed El valor.
     */
    public function value($name) {
        return $this->value[$name];
    }

    /**
     * Cambia el valor del grupo pasado en name.
     * 
     * @param string $name El nombre del grupo.
     * @param mixed $value El nuevo valor.
     */
    public function setValue($name, $value) {
        $this->value[$name] = $value;
    }

    /**
     * Renderea el grupo pasado en name.
     * 
     * @param \ReportExpress\Core\ReportExpress $report EL reporte donde se renderea.
     * @param \ReportExpress\Core\Point $point El punto donde comienza a renderearse.
     * @param string $name Nombre del grupo.
     * @return boolean TRUE si se rendereo completamente sino FALSE. 
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
