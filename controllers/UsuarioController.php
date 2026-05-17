<?php
require_once(__DIR__ . "/../models/mysqlConnect.php");

class UsuarioController {
    private $conexion;

    public function __construct() {
        $this->conexion = new mysqlConn();
    }

    public function getUsuarios() {
        return $this->conexion->realizarConsultaSQL("SELECT u.id, u.username, u.role, u.idAlumno, u.created_at, al.nombre, al.apellidos FROM Usuario u LEFT JOIN Alumno al ON al.id = u.idAlumno ORDER BY username ASC");
    }

    public function getAlumnos() {
        return $this->conexion->realizarConsultaSQL("SELECT id, nombre, apellidos FROM Alumno ORDER BY apellidos ASC, nombre ASC");
    }

    public function crearUsuario($username, $password, $role, $idAlumno = null) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $role = in_array($role, ['admin', 'teacher', 'student', 'family']) ? $role : 'teacher';
        $idAlumno = $idAlumno ? intval($idAlumno) : null;
        $stmt = $this->conexion->preparar("INSERT INTO Usuario (username, password, role, idAlumno) VALUES (?, ?, ?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param("sssi", $username, $hash, $role, $idAlumno);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function actualizarUsuario($id, $role, $password = '', $idAlumno = null) {
        $id = intval($id);
        $role = in_array($role, ['admin', 'teacher', 'student', 'family']) ? $role : 'teacher';
        $idAlumno = $idAlumno ? intval($idAlumno) : null;

        if ($password !== '') {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->conexion->preparar("UPDATE Usuario SET role = ?, password = ?, idAlumno = ? WHERE id = ?");
            if (!$stmt) return false;
            $stmt->bind_param("ssii", $role, $hash, $idAlumno, $id);
        } else {
            $stmt = $this->conexion->preparar("UPDATE Usuario SET role = ?, idAlumno = ? WHERE id = ?");
            if (!$stmt) return false;
            $stmt->bind_param("sii", $role, $idAlumno, $id);
        }

        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function eliminarUsuario($id) {
        $id = intval($id);
        $stmt = $this->conexion->preparar("DELETE FROM Usuario WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
?>
