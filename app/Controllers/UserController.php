<?php
class UserController
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    public function index(): void
    {
        require_role(['super_admin']);
        $users = $this->users->all();
        include __DIR__ . '/../Views/users/index.php';
    }

    public function store(): void
    {
        require_role(['super_admin']);
        $data = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'password' => password_hash($_POST['password'], PASSWORD_BCRYPT),
            'role' => $_POST['role'],
        ];
        $this->users->create($data);
        flash('success', 'Pengguna baru berhasil dibuat.');
        redirect('?page=users');
    }
}
