<?php

namespace App\Model\System;

use Mpdf\Mpdf;
use Latte\Engine;

/**
 * Service for generating PDF documents from Latte templates.
 */
class PdfService
{
    private string $tempDir;

    public function __construct(string $tempDir)
    {
        $this->tempDir = $tempDir . '/mpdf';
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0777, true);
        }
    }

    /**
     * Generates a PDF from a template and data.
     *
     * @param string $templatePath Path to the .latte template file
     * @param array $params Data for the template
     * @param array $options mPDF configuration options (format, orientation, etc.)
     * @return string Binary PDF content
     */
    public function generate(string $templatePath, array $params = [], array $options = []): string
    {
        $latte = new Engine();
        // You can add custom filters here if needed
        $html = $latte->renderToString($templatePath, $params);

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $config = array_merge([
            'tempDir' => $this->tempDir,
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'mode' => 'utf-8',
        ], $options);

        $mpdf = new Mpdf($config);
        $mpdf->WriteHTML($html);

        return $mpdf->Output('', 'S');
    }
}
