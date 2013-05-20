<?php

namespace ReportExpress\Component\Charts;

/**
 * Description of PieChar
 *
 * @author humbe
 */
class PieChart extends Chart {

    function __construct($plot, $dataSet, $chart) {
        parent::__construct($plot, $dataSet, $chart);
    }

    /**
     * Devuelve la cantidad de secciones que debe tener o False en caso de que
     * no esté configurada esta propiedad.
     * 
     * @return mixed
     */
    public function maxCount() {
        return isset($this->dataSet['maxCount']) ? (integer) $this->dataSet['maxCount'] : FALSE;
    }

    public function configData($report) {

        $data = $report->get('values');

        $max = $this->maxCount();

        $label = $this->dataSet->labelExpression ? (string) $this->dataSet->labelExpression : (string) $this->dataSet->keyExpression;

        $pointer = $report->get('ownvariables', 'REPORT_COUNT');

        for ($i = 0; $i < count($data); $i++) {

            $report->set('ownvariables', $i, 'REPORT_COUNT');

            $v = $report->analyse((string) $this->dataSet->valueExpression);
            $l = $report->analyse($label);

            if (!$max) {
                $points [] = $v;
                $desc [] = $l;
            } else {
                if ($i < $max) {
                    $points [] = $v;
                    $desc [] = $l;
                } else {
                    if ($i == $max) {
                        $points[] = $v;
                        $desc[] = $report->analyse((string) $this->dataSet->otherLabelExpression);
                    }
                    else
                        $points [$max] += $v;
                }
            }
        }


        $report->set('ownvariables', $pointer, 'REPORT_COUNT');

        $this->charData->AddPoint($points, "SerieValues");
        $this->charData->AddPoint($desc, "SerieAbsciseLabels");

        $this->charData->AddAllSeries();

        $this->charData->SetAbsciseLabelSerie("SerieAbsciseLabels");

        $a = array();

        foreach ($this->charData->GetData() as $v)
            $a[$v['SerieAbsciseLabels']] = $v['SerieAbsciseLabels'];

        return $a;
    }

    public function render($report, $x, $y) {

        $this->preRender($report);

        // Draw the pie chart                

        $this->charObj->drawBasicPieGraph($this->charData->GetData(), $this->charData->GetDataDescription(), ($this->dimencion['width'] - $this->legend_dimencion['width']) / 2, $this->dimencion['heigth'] / 2, $this->dimencion['heigth'] / 4, PIE_LABELS, 255, 255, 218);

        $this->charObj->clearShadow();

        $this->dibujarPieLegend();
        $this->titulo($report);

        $this->reportAdd($report, $x, $y, $this->charObj);
    }

    /**
     * Dibuja la leyenda de las gráficas de tipo Pie.
     * Por defecto se dibuja con el fondo en blanco y con el texto en negro.
     */
    public function dibujarPieLegend() {
        $this->charObj->drawPieLegend($this->dimencion['width'] - $this->legend_dimencion['width'], 0, $this->charData->GetData(), $this->charData->GetDataDescription(), 250, 250, 250);
    }

}

?>
