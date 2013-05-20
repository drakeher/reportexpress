<?php

namespace ReportExpress\Core;

class Loader {

    /**
     * La direccion de la libreria.
     * @var string 
     */
    private static $path;

    /**
     * Inicializa el $path y la funcion para cargar las clases perdidas
     * de la libreria.
     * 
     * @param type $path a direccion de la libreria
     */
    public static function register($path) {
        self::$path = $path;
        spl_autoload_register('self::load', true, false);
    }

    /**
     * Metodo para la autocarga de clases de la libreria.
     * 
     * @param string $class La clase a cargar.
     * @return boolean TRUE si se cargo la clase. Caso contrario no se encuentra 
     * y PHP lanza un error. 
     */
    private static function load($class) {
	$class = str_replace("\\",DIRECTORY_SEPARATOR,$class);
        if (file_exists(self::$path . DIRECTORY_SEPARATOR . $class . ".php")) {
            require self::$path . DIRECTORY_SEPARATOR . $class . ".php";
            return true;
        }
    }

}
