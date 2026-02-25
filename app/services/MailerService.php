<?php

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
        $message = $this->buildOtpMessage($otpCode);

        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $this->formatFromHeader(),
            'Reply-To: ' . $this->fromEmail,
            'X-Mailer: PHP/' . phpversion(),
        ];

        return mail($toEmail, $subject, $message, implode("\r\n", $headers));
    }

    private function buildOtpMessage(string $otpCode): string
    {
        $otpCode = htmlspecialchars($otpCode, ENT_QUOTES, 'UTF-8');

        return "
            <h2>Verificación de inicio de sesión</h2>
            <p>Tu código OTP es:</p>
            <p style=\"font-size: 24px; font-weight: bold; letter-spacing: 2px;\">{$otpCode}</p>
            <p>Este código expira en 5 minutos.</p>
        ";
    }

    private function formatFromHeader(): string
    {
        $fromName = trim($this->fromName);

        if ($fromName === '') {
            return $this->fromEmail;
        }

        return sprintf('%s <%s>', $fromName, $this->fromEmail);
    }
}
