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
        $employees = $userModel->all();
        $stats = $this->buildEmployeeStats($employees);

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
        $type = trim($_POST['type'] ?? '');
        $department = trim($_POST['department'] ?? '');
        $position = trim($_POST['position'] ?? '');
        $status = trim($_POST['status'] ?? 'Activo');

        if ($name === '' || $email === '' || $type === '') {
            $_SESSION['employee_error_message'] = 'Completa los campos obligatorios para crear el empleado.';
            redirect(route('dashboard', 'addEmployee'));
        }

        $userModel = new UserModel();

        if ($userModel->findByEmail($email)) {
            $_SESSION['employee_error_message'] = 'Ya existe un usuario con ese correo electrónico.';
            redirect(route('dashboard', 'addEmployee'));
        }

        $temporaryPassword = $this->generateTemporaryPassword();

        $userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($temporaryPassword, PASSWORD_DEFAULT),
            'type' => $type,
            'department' => $department,
            'position' => $position,
            'status' => $status,
        ]);


        $_SESSION['employee_success_message'] = 'Empleado creado exitosamente. Contraseña temporal: ' . $temporaryPassword . '. Guarde esta información.';
        redirect(route('dashboard', 'addEmployee'));
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

    private function buildEmployeeStats($employees)
    {
        $stats = [
            'total' => count($employees),
            'instructor' => 0,
            'desarrollador' => 0,
            'administrador' => 0,
            'asistente administrativo' => 0,
        ];

        foreach ($employees as $employee) {
            $type = strtolower(trim($employee['type'] ?? ''));

            if ($type !== '' && array_key_exists($type, $stats)) {
                $stats[$type]++;
            }
        }

        return $stats;
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
