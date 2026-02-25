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

        // 3. Cargar vista
        require VIEW_PATH . '/dashboard.php';
    }


    public function addEmployee()
    {
        authRequired();
        require VIEW_PATH . '/add_employee.php';
    }


    public function doAddEmployee()
    {
        authRequired();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            redirect(route('dashboard', 'addEmployee'));
        }

        $name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($name === '' || $email === '') {
            $_SESSION['employee_error_message'] = 'Completa los campos obligatorios para crear el empleado.';
            redirect(route('dashboard', 'addEmployee'));
        }

        $userModel = new UserModel();

        if ($userModel->findByEmail($email)) {
            $_SESSION['employee_error_message'] = 'Ya existe un usuario con ese correo electrónico.';
            redirect(route('dashboard', 'addEmployee'));
        }

        $temporaryPassword = $this->generateTemporaryPassword();

        $this->createEmployeeUser($name, $email, $temporaryPassword);


        $_SESSION['employee_success_message'] = 'Empleado creado exitosamente. Contraseña temporal: ' . $temporaryPassword . '. Guarde esta información.';
        redirect(route('dashboard', 'addEmployee'));
    }


    private function createEmployeeUser($name, $email, $temporaryPassword)
    {
        $usersFile = DATA_PATH . '/users.json';

        if (!file_exists($usersFile)) {
            file_put_contents($usersFile, json_encode([]));
        }

        $users = json_decode(file_get_contents($usersFile), true);
        if (!is_array($users)) {
            $users = [];
        }

        $users[] = [
            'id' => uniqid(),
            'name' => $name,
            'email' => $email,
            'password' => password_hash($temporaryPassword, PASSWORD_DEFAULT),
            'created_at' => date('Y-m-d H:i:s')
        ];

        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
    }

    private function generateTemporaryPassword($length = 12)
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%';
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $password;
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

}
