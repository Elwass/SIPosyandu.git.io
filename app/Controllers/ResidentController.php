<?php
class ResidentController
{
    private Resident $residents;

    public function __construct()
    {
        $this->residents = new Resident();
    }

    public function index(): void
    {
        require_role(['super_admin', 'admin', 'midwife', 'kader']);
        $residents = $this->residents->all();
        include __DIR__ . '/../Views/residents/index.php';
    }

    public function create(): void
    {
        require_role(['super_admin', 'admin', 'midwife']);
        $resident = null;
        include __DIR__ . '/../Views/residents/form.php';
    }

    public function store(): void
    {
        require_role(['super_admin', 'admin', 'midwife']);
        $data = [
            'name' => $_POST['name'],
            'nik' => $_POST['nik'],
            'family_number' => $_POST['family_number'],
            'address' => $_POST['address'],
            'phone' => $_POST['phone'],
            'birth_date' => $_POST['birth_date'],
            'gender' => $_POST['gender'],
            'category' => $_POST['category'],
        ];
        $this->residents->create($data);
        flash('success', 'Data warga berhasil ditambahkan.');
        redirect('?page=residents');
    }

    public function edit(): void
    {
        require_role(['super_admin', 'admin', 'midwife']);
        $id = (int)($_GET['id'] ?? 0);
        $resident = $this->residents->find($id);
        if (!$resident) {
            redirect('?page=residents');
        }
        include __DIR__ . '/../Views/residents/form.php';
    }

    public function update(): void
    {
        require_role(['super_admin', 'admin', 'midwife']);
        $id = (int)$_POST['id'];
        $data = [
            'name' => $_POST['name'],
            'nik' => $_POST['nik'],
            'family_number' => $_POST['family_number'],
            'address' => $_POST['address'],
            'phone' => $_POST['phone'],
            'birth_date' => $_POST['birth_date'],
            'gender' => $_POST['gender'],
            'category' => $_POST['category'],
        ];
        $this->residents->update($id, $data);
        flash('success', 'Data warga berhasil diperbarui.');
        redirect('?page=residents');
    }

    public function destroy(): void
    {
        require_role(['super_admin', 'admin']);
        $id = (int)($_GET['id'] ?? 0);
        $this->residents->delete($id);
        flash('success', 'Data warga berhasil dihapus.');
        redirect('?page=residents');
    }
}
