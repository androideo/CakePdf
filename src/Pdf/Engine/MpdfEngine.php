<?php
namespace CakePdf\Pdf\Engine;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class MpdfEngine extends AbstractPdfEngine
{
    /**
     * Generates Pdf from html
     *
     * @return string raw pdf data
     */
    public function output(): string
    {
        $mpdf = $this->_createInstance();

        $mpdf->AddPage('','');
        $mpdf->SetHTMLHeader($this->_Pdf->header());
        $mpdf->SetHTMLFooter($this->_Pdf->footer());
 
        $mpdf->WriteHTML($this->_Pdf->html());

        $mpdf->Output();
    }

    /**
     * Creates the Mpdf instance.
     *
     * @param array $options The engine options.
     * @return \Mpdf\Mpdf
     */
    protected function _createInstance(): Mpdf
    {
        $orientation = $this->_Pdf->orientation() === 'landscape' ? 'L' : 'P';
        $format = $this->_Pdf->pageSize();
        if (
            is_string($format)
            && $orientation === 'L'
            && strpos($format, '-L') === false
        ) {
            $format .= '-' . $orientation;
        }
        
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];
        if(is_string($this->_Pdf->customFontDir()))
            $fontDirs = array_merge($fontDirs, [$this->_Pdf->customFontDir()]);

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];
        if(is_array($this->_Pdf->customFontArray()))
            $fontData = $fontData + $this->_Pdf->customFontArray();
        $default_font = (is_array($this->_Pdf->customFontArray())? array_key_first($this->_Pdf->customFontArray()): '');

        $options = [
            'debug' => true,
            'fontDir' => $fontDirs,
            'fontdata' => $fontData,
            'default_font' => $default_font,
            'mode' => $this->_Pdf->encoding(),
            'format' => $format,
            'orientation' => $orientation,
            'tempDir' => TMP,
            'setAutoTopMargin' => 'pad',
            'setAutoBottomMargin' => 'pad',
            'default_font_size' => 10
        ];
       
        $options = array_merge($options, (array)$this->getConfig('options'));
      
        return new \Mpdf\Mpdf($options);
    }
}
