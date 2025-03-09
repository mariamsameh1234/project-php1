<?php
class Database {
    private $pdo;

    public function __construct($host, $user, $pass, $dbname) {
        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $this->pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    
    public function getConnection() {
        return $this->pdo;
    }

    public function insert($table, $columns, $values) {
        $colNames = implode(", ", $columns);
        $placeholders = implode(", ", array_fill(0, count($values), "?"));
        $sql = "INSERT INTO $table ($colNames) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }

    public function selectAll($table) {
        $stmt = $this->pdo->query("SELECT * FROM $table");
        return $stmt->fetchAll();
    }

    public function select($table, $columns, $condition = "", $params = []) {
        $colNames = implode(", ", $columns);
        $sql = "SELECT $colNames FROM $table";
        if (!empty($condition)) {
            $sql .= " WHERE $condition";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function update($table, $columns, $values, $condition) {
        $setClause = implode(" = ?, ", $columns) . " = ?";
        $sql = "UPDATE $table SET $setClause WHERE $condition";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete($table, $condition, $params = []) {
        $sql = "DELETE FROM $table WHERE $condition";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
}
?>
