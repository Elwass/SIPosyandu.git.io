<?php
class SimplePdf
{
    private array $lines = [];
    private string $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function addLine(string $text): void
    {
        $this->lines[] = $text;
    }

    private function escape(string $text): string
    {
        return str_replace(['\\', '(', ')', "\r", "\n"], ['\\\\', '\\(', '\\)', ' ', ' '], $text);
    }

    public function output(string $filename = 'report.pdf'): void
    {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');

        $content = "BT\n/F1 16 Tf\n1 0 0 1 72 770 Tm (" . $this->escape($this->title) . ") Tj\n/F1 12 Tf\n";
        $index = 0;
        foreach ($this->lines as $line) {
            $y = 740 - ($index * 16);
            $content .= sprintf("1 0 0 1 72 %.2f Tm (%s) Tj\n", $y, $this->escape($line));
            $index++;
        }
        $content .= "ET";

        $objects = [];
        $objects[] = "1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj";
        $objects[] = "2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 /MediaBox [0 0 612 792] >> endobj";
        $objects[] = "3 0 obj << /Type /Page /Parent 2 0 R /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >> endobj";
        $objects[] = "4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj";
        $objects[] = "5 0 obj << /Length " . strlen($content) . " >> stream\n" . $content . "\nendstream\nendobj";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object . "\n";
        }

        $xrefPosition = strlen($pdf);
        $pdf .= 'xref\n0 ' . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer << /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefPosition . "\n%%EOF";

        echo $pdf;
        exit;
    }
}
