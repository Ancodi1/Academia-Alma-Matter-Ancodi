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

    public function findUserByEmail(string $email) {
        $stmt = $this->db->preparar('SELECT id, username, email, nombre, rol, password_hash FROM Usuario WHERE email = ? LIMIT 1');
        if (!$stmt) return null;
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user ?: null;
    }

    public function createPasswordResetToken(int $userId, int $ttlSeconds = 1800): ?string {
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expires = date('Y-m-d H:i:s', time() + $ttlSeconds);
        $stmt = $this->db->preparar('INSERT INTO PasswordReset (user_id, token_hash, expires_at) VALUES (?, ?, ?)');
        if (!$stmt) return null;
        $stmt->bind_param('iss', $userId, $tokenHash, $expires);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok ? $token : null;
    }

    public function findValidResetByToken(string $token) {
        $tokenHash = hash('sha256', $token);
        $stmt = $this->db->preparar('SELECT pr.id, pr.user_id, pr.expires_at, u.username, u.email FROM PasswordReset pr JOIN Usuario u ON u.id = pr.user_id WHERE pr.token_hash = ? AND pr.expires_at > NOW() LIMIT 1');
        if (!$stmt) return null;
        $stmt->bind_param('s', $tokenHash);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function consumeResetToken(int $resetId): void {
        $stmt = $this->db->preparar('DELETE FROM PasswordReset WHERE id = ?');
        if ($stmt) {
            $stmt->bind_param('i', $resetId);
            $stmt->execute();
            $stmt->close();
        }
    }

    public function updateUserPassword(int $userId, string $newPassword): bool {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->preparar('UPDATE Usuario SET password_hash = ? WHERE id = ?');
        if (!$stmt) return false;
        $stmt->bind_param('si', $hash, $userId);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
?>


