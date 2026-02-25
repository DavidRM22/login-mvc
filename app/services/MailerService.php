<?php

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
    }

    public function sendOTP(string $toEmail, string $otpCode): bool
    {
        $subject = 'Tu código OTP de acceso';
        $htmlMessage = $this->buildOtpMessage($otpCode);

        return $this->sendViaSmtp($toEmail, $subject, $htmlMessage);
    }

    public function getLastError(): string
    {
        return $this->lastError;
    }

    private function sendViaSmtp(string $toEmail, string $subject, string $htmlMessage): bool
    {
        if ($this->smtpHost === '') {
            $this->lastError = 'Falta configurar SMTP_HOST en el entorno.';
            return false;
        }

        $transport = $this->smtpEncryption === 'ssl' ? 'ssl://' : '';
        $socket = @stream_socket_client(
            $transport . $this->smtpHost . ':' . $this->smtpPort,
            $errno,
            $errstr,
            $this->smtpTimeout
        );

        if (!$socket) {
            $this->lastError = "No se pudo conectar al SMTP ({$this->smtpHost}:{$this->smtpPort}): {$errstr} ({$errno})";
            return false;
        }

        stream_set_timeout($socket, $this->smtpTimeout);

        if (!$this->expectCode($socket, [220])) {
            fclose($socket);
            return false;
        }

        if (!$this->sendCommand($socket, 'EHLO localhost', [250])) {
            fclose($socket);
            return false;
        }

        if ($this->smtpEncryption === 'tls') {
            if (!$this->sendCommand($socket, 'STARTTLS', [220])) {
                fclose($socket);
                return false;
            }

            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                $this->lastError = 'No se pudo establecer canal TLS con el servidor SMTP.';
                fclose($socket);
                return false;
            }

            if (!$this->sendCommand($socket, 'EHLO localhost', [250])) {
                fclose($socket);
                return false;
            }
        }

        if ($this->smtpUser !== '') {
            if (!$this->sendCommand($socket, 'AUTH LOGIN', [334]) ||
                !$this->sendCommand($socket, base64_encode($this->smtpUser), [334]) ||
                !$this->sendCommand($socket, base64_encode($this->smtpPass), [235])) {
                fclose($socket);
                return false;
            }
        }

        if (!$this->sendCommand($socket, 'MAIL FROM:<' . $this->fromEmail . '>', [250])) {
            fclose($socket);
            return false;
        }

        if (!$this->sendCommand($socket, 'RCPT TO:<' . $toEmail . '>', [250, 251])) {
            fclose($socket);
            return false;
        }

        if (!$this->sendCommand($socket, 'DATA', [354])) {
            fclose($socket);
            return false;
        }

        $message = $this->buildMimeMessage($toEmail, $subject, $htmlMessage);
        fwrite($socket, $message . "\r\n.\r\n");

        if (!$this->expectCode($socket, [250])) {
            fclose($socket);
            return false;
        }

        $this->sendCommand($socket, 'QUIT', [221]);
        fclose($socket);

        return true;
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

    private function buildMimeMessage(string $toEmail, string $subject, string $htmlMessage): string
    {
        $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
        $headers = [
            'Date: ' . date('r'),
            'From: ' . $this->formatFromHeader(),
            'To: <' . $toEmail . '>',
            'Subject: ' . $encodedSubject,
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
        ];

        return implode("\r\n", $headers) . "\r\n\r\n" . $htmlMessage;
    }

    private function sendCommand($socket, string $command, array $expectedCodes): bool
    {
        fwrite($socket, $command . "\r\n");
        return $this->expectCode($socket, $expectedCodes, $command);
    }

    private function expectCode($socket, array $expectedCodes, string $command = ''): bool
    {
        $response = '';

        while (($line = fgets($socket, 515)) !== false) {
            $response .= $line;

            if (strlen($line) >= 4 && $line[3] === ' ') {
                break;
            }
        }

        $code = (int)substr($response, 0, 3);

        if (!in_array($code, $expectedCodes, true)) {
            $prefix = $command !== '' ? "Comando {$command}: " : '';
            $this->lastError = $prefix . 'respuesta SMTP inesperada: ' . trim($response);
            return false;
        }

        return true;
    }

    private function formatFromHeader(): string
    {
        $name = trim($this->fromName);

        if ($name === '') {
            return '<' . $this->fromEmail . '>';
        }

        return sprintf('%s <%s>', $name, $this->fromEmail);
    }
}
