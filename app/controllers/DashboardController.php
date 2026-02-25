<?php

require_once MODEL_PATH . '/UserModel.php';
require_once MODEL_PATH . '/AuditModel.php';

class DashboardController
{
    public function index()
    {
        // 1. Proteger ruta
        authRequired();

        // 2. Obtener usuario
        $email = $_SESSION['user_id'];

        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);
        $employees = $userModel->getAll();

        if ($user && !array_filter($employees, fn($employee) => ($employee['email'] ?? '') === $user['email'])) {
            $employees[] = $user;
        }

        // 3. Cargar vista
        require VIEW_PATH . '/dashboard.php';
    }


    public function addEmployee()
    {
        authRequired();

        $generatedPassword = $_SESSION['generated_password'] ?? null;
        $createdEmployee = $_SESSION['created_employee'] ?? null;

        unset($_SESSION['generated_password'], $_SESSION['created_employee']);

        require VIEW_PATH . '/add_employee.php';
    }

    public function storeEmployee()
    {
        authRequired();

        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $type = trim($_POST['type'] ?? 'Instructor');
        $department = trim($_POST['department'] ?? '');
        $position = trim($_POST['position'] ?? '');
        $status = trim($_POST['status'] ?? 'Activo');

        if ($fullName === '' || $email === '') {
            $_SESSION['employee_error'] = 'Nombre y correo son obligatorios.';
            redirect(route('dashboard', 'addEmployee'));
        }

        $userModel = new UserModel();

        if ($userModel->emailExists($email)) {
            $_SESSION['employee_error'] = 'Ya existe un usuario con ese correo.';
            redirect(route('dashboard', 'addEmployee'));
        }

        $temporaryPassword = $this->generateTemporaryPassword();

        $employee = $userModel->create([
            'name' => $fullName,
            'email' => $email,
            'password' => password_hash($temporaryPassword, PASSWORD_DEFAULT),
            'type' => $type,
            'department' => $department,
            'position' => $position,
            'status' => strtolower($status) === 'activo' ? 'active' : 'inactive'
        ]);

        $_SESSION['generated_password'] = $temporaryPassword;
        $_SESSION['created_employee'] = $employee;

        redirect(route('dashboard', 'addEmployee'));
    }

    public function logout()
    {
        session_destroy();
        redirect(route('auth', 'login'));
    }

    public function audit()
    {
    authRequired();

    $auditModel = new AuditModel();
    $logs = json_decode(file_get_contents(DATA_PATH . '/audit.json'), true);

    require VIEW_PATH . '/audit.php';
    }

    private function generateTemporaryPassword($length = 12)
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789@#$%';
        $maxIndex = strlen($alphabet) - 1;
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $alphabet[random_int(0, $maxIndex)];
        }

        return $password;
    }

}
