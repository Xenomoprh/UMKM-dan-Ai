<?php

// File: app/models/Product_model.php

class Product_model {
    private $table = 'products';
    private $db;

    public function __construct() {
        // Instansiasi class Database kita
        $this->db = new Database;
    }

    /**
     * Method untuk mengambil semua data produk dari database
     */
    public function getAllProducts() {
        // Menyiapkan query
        $this->db->query('SELECT * FROM ' . $this->table);
        // Eksekusi dan kembalikan hasilnya
        return $this->db->resultSet();
    }
}