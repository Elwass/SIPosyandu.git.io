<?php
class AuthController
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    public function showLogin(): void
    {
        include __DIR__ . '/../Views/auth/login.php';
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

        redirect('?page=dashboard');
    }

    public function logout(): void
    {
        session_destroy();
        redirect('?page=landing');
    }
}
