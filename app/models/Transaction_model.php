<?php

// File: app/models/Transaction_model.php

class Transaction_model {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function simpanTransaksi($data) {
        // Data yang diterima adalah JSON dari JavaScript, ubah menjadi array PHP
        $cartData = json_decode($data['cart'], true);
        
        // Validasi cart data
        if (!is_array($cartData) || empty($cartData)) {
            error_log('Cart data invalid or empty: ' . print_r($data['cart'], true));
            return false;
        }
        
        // Ambil data pembayaran dari $data ($_POST)
        $paymentReceived = floatval($data['payment_received'] ?? 0);
        $paymentChange = floatval($data['payment_change'] ?? 0);
        
        $totalAmount = 0;

        // Handle dua format cart:
        // 1. Associative array: {"1": {name, price, quantity}, "2": {...}}
        // 2. List array: [{product_id: 1, price: ..., quantity: ...}, ...]
        
        // Cek apakah first item punya product_id property
        $firstItem = reset($cartData);
        $isListFormat = isset($firstItem['product_id']);
        
        // Hitung ulang total
        if ($isListFormat) {
            // Format list: setiap item sudah punya product_id
            foreach ($cartData as $item) {
                $totalAmount += floatval($item['price']) * intval($item['quantity']);
            }
        } else {
            // Format associative: key adalah product_id
            foreach ($cartData as $productId => $item) {
                $totalAmount += floatval($item['price']) * intval($item['quantity']);
            }
        }

        // 1. Mulai Transaction
        $this->db->beginTransaction();

        try {
            // 2. Simpan ke tabel 'transactions'
            $query = "INSERT INTO transactions (total_amount, payment_received, payment_change) 
                      VALUES (:total, :payment_received, :payment_change)";
            
            $this->db->query($query);
            $this->db->bind('total', $totalAmount);
            $this->db->bind('payment_received', $paymentReceived);
            $this->db->bind('payment_change', $paymentChange);
            $this->db->execute();

            // 3. Ambil ID transaksi terakhir
            $transactionId = $this->db->lastInsertId();

            // 4. Loop dan simpan setiap item
            if ($isListFormat) {
                // Format list
                foreach ($cartData as $item) {
                    $productId = intval($item['product_id']);
                    $quantity = intval($item['quantity']);
                    $subtotal = floatval($item['price']) * $quantity;
                    
                    $this->insertTransactionDetail($transactionId, $productId, $quantity, $subtotal);
                }
            } else {
                // Format associative
                foreach ($cartData as $productId => $item) {
                    $productId = intval($productId);
                    $quantity = intval($item['quantity']);
                    $subtotal = floatval($item['price']) * $quantity;
                    
                    $this->insertTransactionDetail($transactionId, $productId, $quantity, $subtotal);
                }
            }

            // 5. Jika semua berhasil, konfirmasi transaksi
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            // 6. Jika ada satu saja yang gagal, batalkan semua proses simpan
            $this->db->rollBack();
            error_log('Transaction error: ' . $e->getMessage());
            return false;
        }
    }

    private function insertTransactionDetail($transactionId, $productId, $quantity, $subtotal) {
        $detailQuery = "INSERT INTO transaction_details (transaction_id, product_id, quantity, subtotal) 
                        VALUES (:trx_id, :product_id, :qty, :subtotal)";
        $this->db->query($detailQuery);
        $this->db->bind('trx_id', $transactionId);
        $this->db->bind('product_id', $productId);
        $this->db->bind('qty', $quantity);
        $this->db->bind('subtotal', $subtotal);
        $this->db->execute();
    }

    public function getAllTransactions() {
        // Ambil semua transaksi diurutkan dari yang terbaru (DESC)
        $this->db->query('SELECT * FROM transactions ORDER BY transaction_time DESC');
        return $this->db->resultSet();
    }

    public function getTransactionDetails($transactionId) {
        // Ambil detail produk untuk transaksi tertentu
        $query = "SELECT 
                    td.detail_id,
                    td.transaction_id,
                    td.product_id,
                    td.quantity,
                    td.subtotal,
                    p.product_name,
                    p.price
                  FROM transaction_details td
                  JOIN products p ON td.product_id = p.product_id
                  WHERE td.transaction_id = :trx_id
                  ORDER BY td.detail_id ASC";
        
        $this->db->query($query);
        $this->db->bind('trx_id', $transactionId);
        return $this->db->resultSet();
    }
}