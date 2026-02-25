<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class MailerService
{
    private string $fromEmail;
    private string $fromName;
    private string $smtpHost;
    private int $smtpPort;
    private string $smtpUser;
    private string $smtpPass;
    private string $smtpEncryption;
    private int $smtpTimeout;
    private string $lastError = '';

    public function __construct()
    {
        $this->fromEmail = MAIL_FROM_EMAIL;
        $this->fromName = MAIL_FROM_NAME;
        $this->smtpHost = SMTP_HOST;
        $this->smtpPort = SMTP_PORT;
        $this->smtpUser = SMTP_USER;
        $this->smtpPass = SMTP_PASS;
        $this->smtpEncryption = strtolower(SMTP_ENCRYPTION);
        $this->smtpTimeout = SMTP_TIMEOUT;

        $autoload = BASE_PATH . '/vendor/autoload.php';
        if (file_exists($autoload)) {
            require_once $autoload;
        }
    }

    public function sendOTP(string $toEmail, string $otpCode): bool
    {
        if (!class_exists(PHPMailer::class)) {
            $this->lastError = 'PHPMailer no está instalado. Ejecuta: composer require phpmailer/phpmailer';
            return false;
        }

        if ($this->smtpHost === '') {
            $this->lastError = 'Falta configurar SMTP_HOST en el entorno.';
            return false;
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $this->smtpHost;
            $mail->Port = $this->smtpPort;
            $mail->SMTPAuth = $this->smtpUser !== '';
            $mail->Username = $this->smtpUser;
            $mail->Password = $this->smtpPass;
            $mail->Timeout = $this->smtpTimeout;

            if ($this->smtpEncryption === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($this->smtpEncryption === 'tls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }

            $mail->CharSet = 'UTF-8';
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($toEmail);
            $mail->isHTML(true);
            $mail->Subject = 'Tu código OTP de acceso';
            $mail->Body = $this->buildOtpMessage($otpCode);
            $mail->AltBody = 'Tu código OTP es: ' . $otpCode . '. Este código expira en 5 minutos.';

            return $mail->send();
        } catch (Exception $e) {
            $this->lastError = $mail->ErrorInfo ?: $e->getMessage();
            return false;
        }
    }

    public function getLastError(): string
    {
        return $this->lastError;
    }

    private function buildOtpMessage(string $otpCode): string
    {
        $otpCode = htmlspecialchars($otpCode, ENT_QUOTES, 'UTF-8');

        return "
            <h2>Verificación de inicio de sesión</h2>
            <p>Tu código OTP es:</p>
            <p style=\"font-size:24px;font-weight:bold;letter-spacing:2px;\">{$otpCode}</p>
            <p>Este código expira en 5 minutos.</p>
        ";
    }
}
