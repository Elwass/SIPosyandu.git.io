<?php
class PatientController
{
    private BpjsProfile $bpjsProfiles;
    private PatientChild $patientChildren;
    private Immunization $immunizations;
    private Measurement $measurements;
    private Resident $residents;

    public function __construct()
    {
        $this->bpjsProfiles = new BpjsProfile();
        $this->patientChildren = new PatientChild();
        $this->immunizations = new Immunization();
        $this->measurements = new Measurement();
        $this->residents = new Resident();
    }

    private function currentPatient(): array
    {
        require_role(['pasien']);
        return user();
    }

    public function dashboard(): void
    {
        $patient = $this->currentPatient();
        $bpjs = $this->bpjsProfiles->getByUserId($patient['id']);
        $children = $this->patientChildren->listWithResidents($patient['id']);
        $childIds = array_column($children, 'id');

        $latestMeasurements = $this->measurements->latestByResidents($childIds);
        $upcomingImmunizations = $this->immunizations->upcomingForResidents($childIds);

        include __DIR__ . '/../Views/patient/dashboard.php';
    }

    public function profile(): void
    {
        $patient = $this->currentPatient();
        $bpjs = $this->bpjsProfiles->getByUserId($patient['id']);
        $children = $this->patientChildren->listWithResidents($patient['id']);
        $latestMeasurements = $this->measurements->latestByResidents(array_column($children, 'id'));

        include __DIR__ . '/../Views/patient/profile.php';
    }

    public function updateBpjs(): void
    {
        $patient = $this->currentPatient();

        $data = [
            'no_bpjs' => $_POST['no_bpjs'] ?? null,
            'status_bpjs' => $_POST['status_bpjs'] ?? 'tidak_diketahui',
            'jenis_bpjs' => $_POST['jenis_bpjs'] ?? null,
            'faskes_tingkat_1' => $_POST['faskes_tingkat_1'] ?? null,
            'tanggal_validasi' => $_POST['tanggal_validasi'] ?? null,
            'keterangan' => $_POST['keterangan'] ?? null,
            'source_system' => 'manual',
        ];

        $this->bpjsProfiles->upsertForUser($patient['id'], $data);
        flash('success', 'Data BPJS berhasil diperbarui.');
        redirect('?page=patient-profile');
    }

    public function storeChild(): void
    {
        $patient = $this->currentPatient();

        $data = [
            'name' => $_POST['name'],
            'nik' => $_POST['nik'],
            'family_number' => $_POST['family_number'],
            'address' => $_POST['address'],
            'phone' => $_POST['phone'],
            'birth_date' => $_POST['birth_date'],
            'gender' => $_POST['gender'],
            'category' => 'toddler',
        ];

        $residentId = $this->residents->create($data);
        $this->patientChildren->attach($patient['id'], $residentId);

        flash('success', 'Data balita berhasil ditambahkan ke akun Anda.');
        redirect('?page=patient-profile');
    }
}
