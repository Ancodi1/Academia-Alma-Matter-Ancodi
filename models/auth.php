<?php
require_once(__DIR__ . '/mysqlConnect.php');

class AuthService {
    private $db;

    public function __construct() {
        $this->db = new mysqlConn();
    }

    public function findUserByUsername(string $username) {
        $stmt = $this->db->preparar('SELECT id, username, nombre, rol, password_hash FROM Usuario WHERE username = ? LIMIT 1');
        if (!$stmt) return null;
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user ?: null;
    }

    public function verifyPassword(string $password, string $hash): bool {
        if (strlen($hash) > 0 && strpos($hash, '$2y$') === 0) {
            return password_verify($password, $hash);
        }
        // Compatibilidad si existieran contraseñas en texto (no recomendado)
        return hash_equals($hash, $password);
    }
}
?>


