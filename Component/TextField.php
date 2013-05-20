<?php

namespace ReportExpress\Component;

use ReportExpress\Core\Point;

/**
 * Description of TextField
 *
 * @author Wickedsick
 */
class TextField extends TextProperty {

    /**
     * No mostrarlo si el resultado es NULL.
     * @TODO No programado.
     * @var boolean 
     */
    private $blankWhenNull = FALSE;

    /**
     * Tiempo en que se evalua el componente.
     * @TODO No esta bien programado los tiempos.
     * @var type (Now,Report,Page,Column,Group,Band,Auto)
     */
    private $evaluationTime = 'Now';

    /**
     * Nombre del grupo donde se evalua.
     * @TODO No esta programado.
     * @var string 
     */
    private $evaluationGroup = '';

    /**
     * Formato de fecha en java y su similar en php.
     * @var array
     */
    private $java_format_date = array(
        '/GG/' => '',
        '/yyyy/' => 'Y',
        '/yy/' => 'y',
        '/MMMMM|MMMM/' => 'F',
        '/MMM/' => 'M',
        '/MM/' => 'm',
        '/M/' => 'n',
        '/dd/' => 'd',
        '/d/' => 'j',
        '/hh|kk/' => 'h',
        '/h|k/' => 'g',
        '/HH|KK/' => 'H',
        '/H|K/' => 'G',
        '/mm|m/' => 'i',
        '/ss|s/' => 's',
        '/SSS/' => 'u',
        '/EEEEE|EEEE/' => 'l',
        '/EE/' => 'D',
        '/DDD|D/' => 'z',
        '/F/' => 'N',
        '/w|W/' => 'W',
        '/aa/' => 'A',
        '/a/' => 'a',
        '/zzzz|zzz|z/' => 'T'
    );

    public function __construct($data) {
        parent::__construct($data);
    }

    /**
     * Devuelve el patron a aplicar al texto.
     * 
     * @return string El patron
     */
    public function pattern() {
        return isset($this->data['pattern']) ? (string) $this->data['pattern'] : NULL;
    }

    /**
     * Valida el formato del numero que contiene el texto. 
     * 
     * @param string $number El texto a formatear.
     * @param string $pattern El patron usado por el ireport.
     * @return string El texto formateado.
     */
    public function formatNumber($number, $pattern) {

        $separador = '.';

        //p[0] numero positivo, p[1] numero negativo
        $p = preg_split('/;/', $pattern);

        //Porcentaje
        if (count($p) == 1) {

            $ceros = preg_split('/\./', $pattern);

            $sym = $ceros[1][strlen($ceros[1]) - 1];

            $decimals = count($ceros) > 1 ? count(preg_split('/(0)/', $ceros[1])) - 1 : 0;

            return number_format($number, $decimals, ',', $separador) . ' ' . ($sym == '%' ? $sym : '‰');

            //Numero
        } else {

            //numero positivo
            $np = $p[0];

            //separador
            if ($np[1] != ',')
                $separador = '';

            //cantidad de numeros despues del separadores
            $ceros = preg_split('/\./', $np);
            $decimals = count($ceros) == 1 ? 0 : strlen($ceros[1]);

            $number = number_format($number, $decimals, ',', $separador);

            if ($number < 0) {
                //normalizamos el numero
                $number *= -1;

                //numero negativo
                $nn = $p[1];

                if ($nn[0] == '-') {
                    return "-$number";
                } elseif ($nn[strlen($nn) - 1] == '-') {
                    return "$number-";
                } elseif ($nn[0] == '(') {

                    if ($nn[1] == '-') {
                        return "(-$number)";
                    } elseif ($nn[strlen($nn) - 2] == '-') {
                        return "($number-)";
                    }
                    return "($number)";
                }
            }

            return $number;
        }
    }

    /**
     * Convierte un patron de fecha en java a un patron de php.
     * 
     * @param string $pattern Patron date de java.
     * @return string Patron date en php.
     */
    public function formatDate($pattern) {

        foreach ($this->java_format_date as $key => $val) {

            $chunk = preg_split($key, $pattern);

            $total = count($chunk);

            if ($total > 1) {
                break;
            }
        }

        $new_pattern = "";

        if ($total > 1) {

            foreach ($chunk as $ch => $v) {

                if ($v != "") {
                    $new_pattern .= $this->formatDate($v);
                }
                if ($ch + 1 < $total) {
                    $new_pattern .= $this->java_format_date[$key];
                }
            }
        } else {
            $new_pattern = $pattern;
        }

        return $new_pattern;
    }

    /**
     * Se evalua el contenido a mostrar en el textfield.
     * 
     * @param \ReportExpress\Core\ReportExpress $report El reporte donde se renderea.
     * @return string El texto a mostrar.
     */
    public function text($report) {

        if ((string) $this->data->textFieldExpression == "new java.util.Date()") {
            return date($this->formatDate($this->pattern()));
        }

        $result = $report->analyse((string) $this->data->textFieldExpression);

        //obtenemos el patron que usa para los datos
        $pattern = $this->pattern();

        //@TODO solo evaluamos patrones numericos mejorar esto 
        //para todo tipo de patrones
        //evaluamos el patron
        if ($pattern && $pattern[0] == '#' && is_numeric($result)) {
            return $this->formatNumber($result, $pattern);
        }
        //analizamos para capturar expresiones
        return $result;
    }

    /**
     * Rendere el componente textfield.
     * 
     * @param \ReportExpress\Core\ReportExpress $report El reporte donde se renderea.
     * @param int $x La x del punto.
     * @param int $y La y del punto
     * @return boolean TRUE o NULL.
     */
    public function render($report, $x, $y) {

        //@TODO programar cuando $time sea = Report
        $time = $this->evaluationTime();

        //no se puede evaluar hasta terminar la pagina
        if ($report->get('readyPage') == FALSE && $time == 'Page') {

            //adicionamos al registro para que sea evaluada al final
            $evaluate = $report->get('evaluatePage');
            $evaluate [] = array('point' => new Point($x, $y), 'index' => $report->get('ownvariables', 'REPORT_COUNT'), 'object' => $this);
            $report->set('evaluatePage', $evaluate);

            //detemos la ejecucion del metodo
            return FALSE;
        }

        //estilo 
        $report->get('pdf')->SetFont($this->fontName(), $this->fontStyle(), $this->size());

        //color 
        $color = $this->fontColor();
        $report->get('pdf')->SetTextColor($color[0], $color[1], $color[2]);

        //rotacion
        if ($this->needRotate() == 1) {
            $report->get('pdf')->StartTransform();
            $report->get('pdf')->Rotate($this->angle(), $this->x() + $x, $this->y() + $y);
            $report->get('pdf')->StopTransform();
        }

        //background
        $bgcolor = $this->backgroundColor();
        $report->get('pdf')->SetFillColor($bgcolor[0], $bgcolor[1], $bgcolor[2]);

        //celda       
        $report->get('pdf')->MultiCell($this->width(), $this->height(), $this->text($report), 0, $this->aling(), 1, 1, $this->x() + $x, $this->y() + $y, TRUE, 0, FALSE, TRUE, $this->isStretchWithOverflow() == 0 ? $this->height() + 1 : 0);

        return TRUE;
    }

}

?>
