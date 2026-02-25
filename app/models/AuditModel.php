<?php

require_once MODEL_PATH . '/database.php';

class AuditModel
{
    public function log($event, $email, $details = '')
    {
        $db = Database::connect();

        $log = [
            'event' => $event,
            'email' => $email,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A',
            'created_at' => date('Y-m-d H:i:s'),
            'details' => $details
        ];

        $sql = "INSERT INTO audit_logs
                (event, email, ip, user_agent, created_at, details)
                VALUES (:event, :email, :ip, :user_agent, :created_at, :details)";

        $stmt = $db->prepare($sql);
        $stmt->execute($log);
    }

    public function getAll()
    {
        $db = Database::connect();

        $sql = "SELECT event, email, ip, user_agent, created_at, details
                FROM audit_logs
                ORDER BY created_at DESC";

        $stmt = $db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
