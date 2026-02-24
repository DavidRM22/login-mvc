<?php

require_once MODEL_PATH . '/AuditModel.php';
require_once MODEL_PATH . '/UserModel.php';
require_once MODEL_PATH . '/OTPModel.php';

class AuthController
{
    public function login()
    {
        require VIEW_PATH . '/login.php';
    }
    public function doLogin()
    {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $userModel = new UserModel();
    $user = $userModel->findByEmail($email);

    if (!$user || !password_verify($password, $user['password'])) {
        echo "❌ Credenciales incorrectas<br>";
        echo "<a href='index.php?controller=auth&action=login'>Volver</a>";
        return;
    }

    // Generar OTP
    $otpModel = new OTPModel();
    $otpModel->generate($email);

    // Guardar email temporal
    $_SESSION['otp_email'] = $email;

    // Auditoría
    $audit = new AuditModel();
    $audit->log('EVENT_LOGIN_ATTEMPT', $email, 'Credenciales válidas, OTP enviado');

    redirect('index.php?controller=auth&action=verify');
    }


    public function register()
    {
        require VIEW_PATH . '/register.php';
    }

    public function doRegister()
    {
        // 1. Recibir datos POST
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // 2. Crear usuario
        $userModel = new UserModel();
        $userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
        $audit = new AuditModel();
        $audit->log('EVENT_REGISTER', $email, 'Usuario registrado');



        // 3. Generar OTP
        $otpModel = new OTPModel();
        $otpCode = $otpModel->generate($email);

        // 4. Guardar email en sesión
        $_SESSION['otp_email'] = $email;

        // ⚠️ SOLO PARA PRUEBAS
        echo "OTP generado: <strong>$otpCode</strong><br>";

        // 5. Redirigir a verificación (luego)
        echo "<a href='index.php?controller=auth&action=verify'>Ir a verificar OTP</a>";
        $audit->log('EVENT_OTP_SENT', $email, 'OTP generado');

    }

    public function verify()
    {
        require VIEW_PATH . '/verify.php';
    }

    public function verifyOTP()
    {
    // 1. Obtener datos
    $code = $_POST['code'];
    $email = $_SESSION['otp_email'] ?? null;

    if (!$email) {
        die("Sesión expirada. Regístrese nuevamente.");
    }

    // 2. Verificar OTP
    $otpModel = new OTPModel();
    $isValid = $otpModel->verify($email, $code);
    


    if (!$isValid) {
        echo "❌ Código incorrecto o expirado<br>";
        echo "<a href='index.php?controller=auth&action=verify'>Intentar de nuevo</a>";
        $audit = new AuditModel();
        $audit->log('EVENT_FAILED_OTP', $email, 'Código incorrecto o expirado');
        return;
    }
    
    $audit = new AuditModel();
    $audit->log('EVENT_LOGIN', $email, 'Login exitoso');
    // 3. Crear sesión definitiva
    $_SESSION['user_id'] = $email;

    // 4. Limpiar OTP temporal
    unset($_SESSION['otp_email']);

    // 5. Redirigir al dashboard (siguiente fase)
    redirect('index.php?controller=dashboard&action=index');
}

}
