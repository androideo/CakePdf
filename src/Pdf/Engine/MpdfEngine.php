<?php
declare(strict_types=1);

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
        $Mpdf = $this->_createInstance($options);
        
        if(!this->_Pdf->headerOnFirstPage())
            $Mpf->AddPage('','');
        
        $header = $this->_Pdf->header();
        foreach ($header as $location => $html) {
            if ($html !== null) {
                $Mpf->SetHTMLHeader($html);
            }
        }
        
        $footer = $this->_Pdf->footer();
        foreach ($footer as $location => $html) {
            if ($html !== null) {
                $Mpf->SetHTMLFooter($html);
            }
        }
        
        $Mpdf->WriteHTML($this->_Pdf->html());

        return $Mpdf->Output('', Destination::STRING_RETURN);
    }

    /**
     * Creates the Mpdf instance.
     *
     * @param array $options The engine options.
     * @return \Mpdf\Mpdf
     */
    protected function _createInstance($options): Mpdf
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

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $options = [
            'fontDir' => array_merge($fontDirs, [
                $this->_Pdf->getCustomFontDir(),
            ]),
            'fontdata' => 
                $fontData + 
                $this->_Pdf->getCustomFontArray()
            ],
            'default_font' => array_key_first($this->_Pdf->getCustomFontArray()),
            'mode' => $this->_Pdf->encoding(),
            'format' => $format,
            'orientation' => $orientation,
            'tempDir' => TMP,
        ];
        $options = array_merge($options, (array)$this->getConfig('options'));
                        
        return new Mpdf($options);
    }
}
