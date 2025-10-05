<?php

// File: app/core/Database.php (Versi Baru)

class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $db_name = DB_NAME;

    private $dbh; // Database Handler
    private $stmt; // Statement

    public function __construct() {
        // Data source name
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name;

        $option = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $option);
        } catch(PDOException $e) {
            die($e->getMessage());
        }
    }

    // Method untuk menjalankan query
    public function query($sql) {
        $this->stmt = $this->dbh->prepare($sql);
    }

    // Method untuk mengeksekusi statement
    public function execute() {
        $this->stmt->execute();
    }

    // Method untuk mengambil banyak hasil (array)
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method untuk mengambil satu hasil saja
    public function single() {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }
}