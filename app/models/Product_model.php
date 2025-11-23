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
        $this->db->query('SELECT * FROM ' . $this->table . ' ORDER BY kategori, product_name');
        // Eksekusi dan kembalikan hasilnya
        return $this->db->resultSet();
    }

    /**
     * Method untuk mengambil produk berdasarkan kategori
     */
    public function getProductsByCategory($kategori) {
        $this->db->query('SELECT * FROM ' . $this->table . ' WHERE kategori = :kategori ORDER BY product_name');
        $this->db->bind(':kategori', $kategori);
        return $this->db->resultSet();
    }

    /**
     * Method untuk menambah produk baru
     */
    public function addProduct($data) {
        $this->db->query('INSERT INTO ' . $this->table . ' (product_name, kategori, price, cost_of_goods, stock_quantity) 
                          VALUES (:product_name, :kategori, :price, :cost_of_goods, :stock_quantity)');
        
        $this->db->bind(':product_name', $data['product_name']);
        $this->db->bind(':kategori', $data['kategori']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':cost_of_goods', $data['cost_of_goods']);
        $this->db->bind(':stock_quantity', $data['stock_quantity']);
        
        if ($this->db->execute()) {
            return ['status' => true, 'message' => 'Produk berhasil ditambahkan'];
        } else {
            return ['status' => false, 'message' => 'Gagal menambahkan produk'];
        }
    }

    /**
     * Method untuk mengubah produk
     */
    public function updateProduct($id, $data) {
        $this->db->query('UPDATE ' . $this->table . ' 
                          SET product_name = :product_name, 
                              kategori = :kategori, 
                              price = :price, 
                              cost_of_goods = :cost_of_goods, 
                              stock_quantity = :stock_quantity
                          WHERE product_id = :product_id');
        
        $this->db->bind(':product_id', $id);
        $this->db->bind(':product_name', $data['product_name']);
        $this->db->bind(':kategori', $data['kategori']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':cost_of_goods', $data['cost_of_goods']);
        $this->db->bind(':stock_quantity', $data['stock_quantity']);
        
        if ($this->db->execute()) {
            return ['status' => true, 'message' => 'Produk berhasil diubah'];
        } else {
            return ['status' => false, 'message' => 'Gagal mengubah produk'];
        }
    }

    /**
     * Method untuk menghapus produk
     */
    public function deleteProduct($id) {
        $this->db->query('DELETE FROM ' . $this->table . ' WHERE product_id = :product_id');
        $this->db->bind(':product_id', $id);
        
        if ($this->db->execute()) {
            return ['status' => true, 'message' => 'Produk berhasil dihapus'];
        } else {
            return ['status' => false, 'message' => 'Gagal menghapus produk'];
        }
    }

    /**
     * Method untuk menambah stock produk (kurangi atau tambah)
     */
    public function updateStock($id, $quantity) {
        $this->db->query('UPDATE ' . $this->table . ' 
                          SET stock_quantity = stock_quantity + :quantity
                          WHERE product_id = :product_id');
        
        $this->db->bind(':product_id', $id);
        $this->db->bind(':quantity', $quantity);
        
        if ($this->db->execute()) {
            return ['status' => true, 'message' => 'Stock berhasil diperbarui'];
        } else {
            return ['status' => false, 'message' => 'Gagal memperbarui stock'];
        }
    }

    /**
     * Method untuk mendapatkan kategori unik
     */
    public function getCategories() {
        $this->db->query('SELECT DISTINCT kategori FROM ' . $this->table . ' ORDER BY kategori');
        return $this->db->resultSet();
    }
}
