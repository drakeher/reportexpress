<?php

namespace ReportExpress\Component;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TextProperty
 *
 * @author osley.rivera
 */
abstract class TextProperty extends Component {

    /**
     * @TODO No programada.
     * @var type 
     */
    protected $pdfEmbedded = FALSE;

    /**
     * @TODO No programada.
     * @var type (Center,Left,Right,Justified)
     */
    protected $horizontalAlignment = 'Left';

    /**
     * @TODO No programada.
     * @var type (Middle,Top,Bottom)
     */
    protected $verticalAlignment = 'Top';

    /**
     * Posicion hacia la que debe rotar.
     * @var type (UpsideDown,Left,Right)
     */
    protected $rotation = '';

    /**
     * @TODO No programada.
     * @var type (Single,1.5,Double)
     */
    protected $lineSpacing = 'Single';

    /**
     * @TODO No programada.
     * @var type 
     */
    protected $markup = '';

    public function __construct($data) {
        parent::__construct($data);
    }

    /**
     * Devuelve el nombre del tipo de letra.
     * 
     * @return string EL nombre.
     */
    public function fontName() {
        return isset($this->data->textElement->font->fontName) ? $this->data->textElement->font->fontName : 'times';
    }

    /**
     * Devuelve el estilo de la letra.
     * 
     * @return string El estilo.
     */
    public function fontStyle() {

        $style = '';

        $font = $this->data->textElement->font;

        if ((string) $font['isBold'] == 'true') {
            $style = 'B';
        }
        if ((string) $font['isItalic'] == 'true') {
            $style .= 'I';
        }
        if ((string) $font['isUnderline'] == 'true') {
            $style .= 'U';
        }
        if ((string) $font['isStrikeThrough'] == 'true') {
            $style .= 'D';
        }
        return $style;
    }

    /**
     * Tamaño de la letra.
     * 
     * @return int El valor.
     */
    public function size() {
        return (int) $this->data->textElement->font['size'] ? (int) $this->data->textElement->font['size'] : 10;
    }

    /**
     * Devuelve la alineacion del texto (L=>left,C=>center,R=>right).
     * 
     * @return string La alineacion.
     */
    public function aling() {
        return isset($this->data->textElement['textAlignment']) ? substr((string) $this->data->textElement['textAlignment'], 0, 1) : 'L';
    }

    /**
     * Indica si se debe rotar.
     * 
     * @return boolean TRUE o FALSE.
     */
    public function needRotate() {
        return isset($this->data->textElement['rotation']);
    }

    /**
     * Devuelve el angulo al que se debe rotar.
     * 
     * @return int Angulo.
     */
    public function angle() {
        //@TODO rotacion basica
        switch ($this->rotate()) {
            case "Left":
                return 90;
                break;
            case "Right":
                return 270;
                break;
            case "UpsideDown":
                return 180;
                break;
            default:
                return 0;
                break;
        }
    }

    /**
     * Devuelve el lado al que se debe rotar.
     * @return string Lado.
     */
    public function rotate() {
        return (string) $this->data->textElement['rotation'];
    }

    /**
     * Indica si la celda se debe adaptar al contenido maximizandose o
     * si el contenido se adapta a la celda.
     * 
     * @return boolean TRUE o FALSE.
     */
    public function isStretchWithOverflow() {
        return isset($this->data['isStretchWithOverflow']);
    }

    /**
     * Devuelve el momento que debe ser evaluado.
     * 
     * @return string El tiempo.
     */
    public function evaluationTime() {
        return (string) $this->data['evaluationTime'];
    }

}

?>
