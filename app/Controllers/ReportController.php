<?php
class ReportController
{
    private Measurement $measurements;
    private Resident $residents;

    public function __construct()
    {
        $this->measurements = new Measurement();
        $this->residents = new Resident();
    }

    public function index(): void
    {
        require_role(['super_admin', 'admin', 'midwife']);
        $measurements = $this->measurements->all();
        include __DIR__ . '/../Views/reports/index.php';
    }

    public function download(): void
    {
        require_role(['super_admin', 'admin', 'midwife']);
        $pdf = new SimplePdf('Laporan Penimbangan & Imunisasi');
        $pdf->addLine('Tanggal cetak: ' . date('d/m/Y H:i'));
        $pdf->addLine('');
        $pdf->addLine('Data Penimbangan:');
        foreach ($this->measurements->all() as $row) {
            $line = sprintf('%s - %s kg / %s cm - Status: %s (%s)',
                $row['resident_name'],
                $row['weight'],
                $row['height'],
                $row['nutritional_status'],
                $row['measured_at']
            );
            $pdf->addLine($line);
        }

        $pdf->addLine('');
        $pdf->addLine('Jadwal Imunisasi:');
        foreach ((new Immunization())->all() as $immunization) {
            $line = sprintf('%s - %s pada %s [%s]',
                $immunization['resident_name'],
                $immunization['vaccine_name'],
                $immunization['schedule_date'],
                $immunization['status']
            );
            $pdf->addLine($line);
        }

        $pdf->output('laporan-posyandu.pdf');
    }
}
