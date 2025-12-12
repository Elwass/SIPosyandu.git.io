<?php
class AuthController
{
    private User $users;
    private BpjsProfile $bpjsProfiles;

    public function __construct()
    {
        $this->users = new User();
        $this->bpjsProfiles = new BpjsProfile();
    }

    public function showLogin(): void
    {
        include __DIR__ . '/../Views/auth/login.php';
    }

    public function showPatientRegister(): void
    {
        include __DIR__ . '/../Views/auth/patient_register.php';
    }

    public function login(): void
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->users->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            flash('error', 'Email atau password tidak sesuai.');
            redirect('?page=login');
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];

        $target = $user['role'] === 'pasien' ? '?page=patient-dashboard' : '?page=dashboard';
        redirect($target);
    }

    public function logout(): void
    {
        session_destroy();
        redirect('?page=landing');
    }

    public function registerPatient(): void
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';
        $noBpjs = trim($_POST['no_bpjs'] ?? '');
        $statusBpjs = $_POST['status_bpjs'] ?? 'tidak_diketahui';

        if ($name === '' || $email === '' || $password === '') {
            flash('error', 'Nama, email, dan kata sandi wajib diisi.');
            redirect('?page=patient-register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Format email tidak valid.');
            redirect('?page=patient-register');
        }

        if ($password !== $passwordConfirmation) {
            flash('error', 'Konfirmasi kata sandi tidak sesuai.');
            redirect('?page=patient-register');
        }

        if ($this->users->findByEmail($email)) {
            flash('error', 'Email sudah terdaftar, silakan masuk.');
            redirect('?page=login');
        }

        $userId = $this->users->create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'pasien',
        ]);

        if ($noBpjs !== '' || $statusBpjs !== 'tidak_diketahui') {
            $this->bpjsProfiles->upsertForUser($userId, [
                'no_bpjs' => $noBpjs !== '' ? $noBpjs : null,
                'status_bpjs' => $statusBpjs,
                'source_system' => 'manual',
            ]);
        }

        $_SESSION['user'] = [
            'id' => $userId,
            'name' => $name,
            'email' => $email,
            'role' => 'pasien',
        ];

        flash('success', 'Akun pasien berhasil dibuat. Lengkapi profil balita Anda.');
        redirect('?page=patient-dashboard');
    }
}
