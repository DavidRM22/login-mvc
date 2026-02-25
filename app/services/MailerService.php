<?php

require_once APP_PATH . '/libs/PHPMailer/src/Exception.php';
require_once APP_PATH . '/libs/PHPMailer/src/PHPMailer.php';
require_once APP_PATH . '/libs/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class MailerService
{
    private string $fromEmail;
    private string $fromName;

    public function __construct()
    {
        $this->fromEmail = MAIL_FROM_EMAIL;
        $this->fromName = MAIL_FROM_NAME;
    }

    public function sendOTP(string $toEmail, string $otpCode): bool
    {
        $subject = 'Tu código OTP de acceso';
        $message = $this->buildOtpMessage($otpCode, $toEmail);

        try {
            $mailer = new PHPMailer(true);
            $mailer->CharSet = 'UTF-8';
            $mailer->isSMTP();
            $mailer->Host = MAIL_HOST;
            $mailer->Port = MAIL_PORT;
            $mailer->SMTPAuth = MAIL_AUTH;

            if ($mailer->SMTPAuth) {
                $mailer->Username = MAIL_USERNAME;
                $mailer->Password = MAIL_PASSWORD;
            }

            if (MAIL_ENCRYPTION !== '') {
                $mailer->SMTPSecure = MAIL_ENCRYPTION;
            }

            $mailer->setFrom($this->fromEmail, $this->fromName);
            $mailer->addAddress($toEmail);
            $mailer->addReplyTo(MAIL_REPLY_TO, $this->fromName);
            $mailer->Sender = MAIL_RETURN_PATH;
            $mailer->MessageID = sprintf('<otp-%s@%s>', bin2hex(random_bytes(8)), $this->getDomain($this->fromEmail));

            $mailer->isHTML(true);
            $mailer->Subject = $subject;
            $mailer->Body = $message;
            $mailer->AltBody = "Tu código OTP es: {$otpCode}. Este código expira en 5 minutos.";

            return $mailer->send();
        } catch (Exception $exception) {
            error_log('Error al enviar OTP: ' . $exception->getMessage());
            return false;
        }
    }

    private function buildOtpMessage(string $otpCode, string $recipientEmail): string
    {
        $otpCode = htmlspecialchars($otpCode, ENT_QUOTES, 'UTF-8');
        $recipientEmail = htmlspecialchars($recipientEmail, ENT_QUOTES, 'UTF-8');

        return "
            <div style=\"font-family:Arial,sans-serif;line-height:1.5;color:#1f2937\">
                <h2 style=\"margin-bottom:8px;\">Verificación de inicio de sesión</h2>
                <p style=\"margin:0 0 10px;\">Hola, solicitaste iniciar sesión con la cuenta <strong>{$recipientEmail}</strong>.</p>
                <p style=\"margin:0 0 8px;\">Tu código OTP es:</p>
                <p style=\"font-size:24px;font-weight:bold;letter-spacing:2px;margin:0 0 10px;\">{$otpCode}</p>
                <p style=\"margin:0 0 10px;\">Este código expira en 5 minutos.</p>
                <p style=\"margin:0;font-size:12px;color:#6b7280;\">Si no solicitaste este código, ignora este mensaje.</p>
            </div>
        ";
    }

    private function getDomain(string $email): string
    {
        $parts = explode('@', $email);
        return $parts[1] ?? 'localhost';
    }
}
