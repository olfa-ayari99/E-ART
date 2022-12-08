<?php

namespace App\service;

use Dompdf\Dompdf;

class PdfService
{
    private $domPdf;

    public function __construct() {
        $this->domPdf = new Dompdf();

        $pdfOptions = new Options();

        $pdfOptions->set('defaultFont', 'Armond');

        $this->domPdf->setOptions($pdfOptions);
    }

    public function showPdfFile($html) {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->stream("details.pdf", [
            'Attachment' => true
        ]);
    }

    public function generateBinaryPDF($html) {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->output();
    }
}