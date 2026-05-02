<?php
    class mysqlConn{
        private $server;
        private $user;
        private $password;
        private $database;
        private $msqli;

            //Constructor por defecto
        public function __construct(){
            $this->server = getenv('DB_HOST') !== false ? getenv('DB_HOST') : 'localhost';
            $this->user = getenv('DB_USER') !== false ? getenv('DB_USER') : 'root';
            $this->password = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '';
            $this->database = getenv('DB_NAME') !== false ? getenv('DB_NAME') : 'almamater';
            $this->msqli = new mysqli($this->server, $this->user, $this->password, $this->database);
            
            if ($this->msqli->connect_error) {
                die("Error de conexión: " . $this->msqli->connect_error);
            }
            
            $this->msqli->set_charset("utf8");
    }

    public function realizarConsultaSQL($consulta){
        $result = $this->msqli->query($consulta);
        if (!$result) {
            error_log("Error SQL: " . $this->msqli->error);
            return false;
        }
        return $result;
    }

    // Escapa cadenas para usarlas de forma segura en consultas SQL
    public function escapar($valor){
        return $this->msqli->real_escape_string($valor);
    }

    // Preparar una sentencia SQL de forma segura
    public function preparar($sql){
        try {
            return $this->msqli->prepare($sql);
        } catch (mysqli_sql_exception $e) {
            error_log("Error SQL al preparar sentencia: " . $e->getMessage());
            return false;
        }
    }

    public function getInsertId(){
        return $this->msqli->insert_id;
    }
    
    // Cerrar conexión
    public function cerrar(){
        if ($this->msqli) {
            $this->msqli->close();
        }
    }
    
    // Destructor para cerrar conexión automáticamente
    public function __destruct(){
        $this->cerrar();
    }
}
?>