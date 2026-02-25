<?php

require_once MODEL_PATH . '/database.php';

class UserModel
{
    public function create($data)
    {
        $data['id'] = uniqid();
        $data['created_at'] = date('Y-m-d H:i:s');

        $db = Database::connect();

        $sql = "INSERT INTO users (id, name, email, password, created_at)
                VALUES (:id, :name, :email, :password, :created_at)";

        $stmt = $db->prepare($sql);
        $stmt->execute($data);
    }

    public function findByEmail($email)
    {
        $db = Database::connect();

        $sql = "SELECT id, name, email, password, created_at
                FROM users
                WHERE email = :email
                LIMIT 1";

        $stmt = $db->prepare($sql);
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }
}
