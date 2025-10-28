<?php
class MeasurementController
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
        require_role(['super_admin', 'admin', 'midwife', 'kader']);
        $measurements = $this->measurements->all();
        $residents = $this->residents->all();
        include __DIR__ . '/../Views/measurements/index.php';
    }

    public function store(): void
    {
        require_role(['super_admin', 'admin', 'midwife', 'kader']);
        $resident = $this->residents->find((int)$_POST['resident_id']);
        if (!$resident) {
            flash('error', 'Warga tidak ditemukan.');
            redirect('?page=measurements');
        }

        $status = $this->determineStatus($resident['category'], (float)$_POST['weight'], (float)$_POST['height']);

        $data = [
            'resident_id' => $_POST['resident_id'],
            'weight' => $_POST['weight'],
            'height' => $_POST['height'],
            'muac' => $_POST['muac'] !== '' ? $_POST['muac'] : null,
            'nutritional_status' => $status,
            'notes' => isset($_POST['notes']) && $_POST['notes'] !== '' ? $_POST['notes'] : null,
            'measured_at' => !empty($_POST['measured_at']) ? $_POST['measured_at'] : date('Y-m-d'),
        ];

        $this->measurements->create($data);
        flash('success', 'Data penimbangan berhasil disimpan.');
        redirect('?page=measurements');
    }

    private function determineStatus(string $category, float $weight, float $height): string
    {
        if ($height <= 0) {
            return 'belum_dinilai';
        }

        $heightMeter = $height / 100;
        $bmi = $weight / ($heightMeter * $heightMeter);

        if ($category === 'toddler') {
            if ($bmi < 14) {
                return 'gizi_kurang';
            }
            if ($bmi <= 18) {
                return 'gizi_baik';
            }
            return 'gizi_lebih';
        }

        if ($bmi < 18.5) {
            return 'kurang';
        }
        if ($bmi <= 24.9) {
            return 'normal';
        }
        if ($bmi <= 29.9) {
            return 'berlebih';
        }

        return 'obesitas';
    }
}
