<?php

namespace ReportExpress\Component;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Puede que en un futuro esta clase provea algunos metodos para 
 * manipular los datos. Por ahora es solo recipiente para los dataset
 * utilizados en el reporte.
 *
 * @author osley.rivera
 */
class DataSet {

    /**
     * Nombre del dataset.
     * @var string 
     */
    private $name = '';

    /**
     * Datos del dataset.
     * @var array 
     */
    private $data = array();

    public function __construct($name, $data) {
        $this->name = $name;
        $this->data = $data;
    }

    /**
     * Devuelve el nombre del dataset.
     * 
     * @return string El nombre.
     */
    public function name() {
        return $this->name;
    }

    /**
     * Devuelve los datos.
     * 
     * @return array Datos.
     */
    public function getData() {
        return $this->data;
    }

}

?>
