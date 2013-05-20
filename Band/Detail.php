<?php

namespace ReportExpress\Band;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Detail
 *
 * @author osley.rivera
 */
class Detail extends Band {

    /**
     * Ultimo valor que alcanza la y al renderearse una fila.
     * @var int 
     */
    private $lasty = 0;

    /**
     * Indica si en un salto de pagina existen grupos colgados.
     * @var boolean 
     */
    private $hangingFooter = FALSE;

    public function __construct($xml) {
        parent::__construct($xml->detail->band);
    }

    /**
     * Renderea los componentes de la Banda.
     * 
     * @param \ReportExpress\Core\ReportExpress $report EL reporte donde se renderea.
     * @param \ReportExpress\Core\Point $point El punto donde comienza a renderearse.
     * @return string El estado en que quedo renderear la fila.
     */
    public function render($report, $point) {

        //si existe algun groupfooter colgado lo redereamos
        if ($this->hangingFooter) {
            $this->renderGroupFooter($report, $point);
            $this->hangingFooter = FALSE;
        }

        //evaluamos la variables
        $report->evaluationVariable();

        //evaluamos los groupheader
        if (!$this->renderGroupHeader($report, $point)) {
            return 'newpage';
        }

        //si no hay suficiente espacio para la fila de detalle state:newpage
        if ($this->maxy + $point->y > $point->mxb) {
            return 'newpage';
        }

        //indica realmente cuanto crecio la fila al ser rendereada
        $this->height = 0;

        //rendereamos component
        $this->realRender('component', $report, $point);

        //redereamos after
        $this->realRender('after', $report, $point);

        //actualizamos la ultima posicion que obtuvo la el eje y
        $this->lasty = $point->y += $this->height;

        return 'otherrow';
    }

    /**
     * Devuelve el maximo valor alcanzado por la y mas la altura promedio
     * de cada fila. Este metodo se usa para conocer si queda espacio para 
     * mostrar la proxima fila.
     * 
     * @return int El valor.
     */
    public function maxRow() {
        return $this->maxy + $this->lasty;
    }

    /**
     * Devuelve el maximo valor alcanzado por la y.
     * @return int El valor.
     */
    public function lasty() {
        return $this->lasty;
    }

    /**
     * Cambia el valor de la ultima y.
     * 
     * @param int $value El nuevo valor de las y.
     */
    public function setLasty($value) {
        $this->lasty = $value;
    }

    /**
     * Renderea los grupos de cabecera.
     * 
     * @param \ReportExpress\Core\ReportExpress $report EL reporte donde se renderea.
     * @param \ReportExpress\Core\Point $point El punto donde comienza a renderearse.
     * 
     * @return boolean Devuelve TRUE o FALSE para indicar si se renderearon 
     * totalmente los grupos cabecera. 
     */
    public function renderGroupHeader($report, $point) {

        if ($report->hasGroups()) {

            foreach ($report->get('groups') as $group) {

                $result = $report->analyse($group->groupExpression());

                if ($result != $group->value('header')) {
                    if (!$group->render($report, $point, 'header')) {
                        return FALSE;
                    }
                    $group->setValue('header', $result);

                    if ($report->index() == 0) {
                        $group->setValue('footer', $result);
                    }
                }
            }
        }

        return TRUE;
    }

    /**
     * Renderea los grupos finales.
     * 
     * @param \ReportExpress\Core\ReportExpress $report EL reporte donde se renderea.
     * @param \ReportExpress\Core\Point $point El punto donde comienza a renderearse.
     * 
     * @return boolean Devuelve TRUE o FALSE para indicar si se renderearon 
     * totalmente los grupos finales. 
     */
    public function renderGroupFooter($report, $point) {

        if ($report->hasGroups()) {

            $groups = $report->get('groups');

            for ($i = count($groups) - 1; $i >= 0; $i--) {

                $result = $report->analyse($groups[$i]->groupExpression());

                if ($result != $groups[$i]->value('footer')) {
                    if (!$groups[$i]->render($report, $point, 'footer')) {
                        $this->hangingFooter = TRUE;
                        return FALSE;
                    }

                    $groups[$i]->setValue('footer', $result);

                    //reseteamos todas las variables de tipo Group
                    $report->resetVariables('Group', $groups[$i]->name());
                }
            }
        }

        return TRUE;
    }

}

?>
