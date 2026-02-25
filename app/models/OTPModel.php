<?php

require_once MODEL_PATH . '/database.php';

class OTPModel
{
    public function generate($email)
    {
        $db = Database::connect();

        $code = (string) rand(100000, 999999);
        $expiresAt = date('Y-m-d H:i:s', time() + 300);

        $sql = "INSERT INTO otp_codes (email, code, expires_at, created_at)
                VALUES (:email, :code, :expires_at, NOW())
                ON DUPLICATE KEY UPDATE
                    code = VALUES(code),
                    expires_at = VALUES(expires_at),
                    created_at = NOW()";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            'email' => $email,
            'code' => $code,
            'expires_at' => $expiresAt
        ]);

        return $code;
    }

    public function verify($email, $code)
    {
        $db = Database::connect();

        $sql = "SELECT code, expires_at
                FROM otp_codes
                WHERE email = :email
                LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->execute(['email' => $email]);

        $otp = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$otp) {
            return false;
        }

        $isExpired = strtotime($otp['expires_at']) < time();
        if ($isExpired) {
            return false;
        }

        return $otp['code'] === (string) $code;
    }
}
