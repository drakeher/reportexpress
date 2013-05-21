<?php

namespace ReportExpress\Core;

/**
 * Loader Class
 * 
 * The Loader Class using SPL
 * 
 * @category    Library
 * @package     ReportExpress
 * @subpackage  Core
 * @version     1.0 In development. Very unstable.
 * @author      Yordis Prieto <yordis.prieto@gmail.com>
 * @copyright   Creative Commons (CC) 2013, Yordis Prieto.
 * @license     http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License.
 */
class Loader {

    /**
     * Path of library.
     * @var string 
     */
    private static $path;

    /**
     * Inicializa el $path y la funcion para cargar las clases perdidas
     * de la libreria.
     * 
     * @param type $path The path of labrary.
     * @return void
     */
    public static function register($path) {
        self::$path = $path;
        spl_autoload_register('self::load', true, false);
    }

    /**
     * Find the class within library.
     * 
     * @param string $class The class.
     * @return boolean TRUE It was founded, FALSE otherwise.
     */
    private static function load($class) {
	$class = str_replace("\\",DIRECTORY_SEPARATOR,$class);
        if (file_exists(self::$path . DIRECTORY_SEPARATOR . $class . ".php")) {
            require self::$path . DIRECTORY_SEPARATOR . $class . ".php";
            return true;
        }
    }

}
