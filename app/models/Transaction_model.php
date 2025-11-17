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
        
        // Ambil data pembayaran baru dari $data ($_POST)
        $paymentReceived = $data['payment_received'];
        $paymentChange = $data['payment_change'];
        
        $totalAmount = 0;

        // Hitung ulang total di sisi server untuk keamanan
        foreach ($cartData as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        // 1. Mulai Transaction (agar jika ada kegagalan bisa dibatalkan semua)
        $this->db->beginTransaction();

        try {
            // 2. Simpan ke tabel 'transactions' (INI BAGIAN YANG DIPERBARUI)
            $query = "INSERT INTO transactions (total_amount, payment_received, payment_change) 
                      VALUES (:total, :payment_received, :payment_change)";
            
            $this->db->query($query);
            
            // Bind semua data
            $this->db->bind('total', $totalAmount);
            $this->db->bind('payment_received', $paymentReceived); // <-- BARIS BARU
            $this->db->bind('payment_change', $paymentChange);   // <-- BARIS BARU
            
            $this->db->execute();

            // 3. Ambil ID transaksi terakhir yang baru saja dibuat
            $transactionId = $this->db->lastInsertId();

            // 4. Loop dan simpan setiap item ke tabel 'transaction_details' (Tetap sama)
            foreach ($cartData as $productId => $item) {
                $detailQuery = "INSERT INTO transaction_details (transaction_id, product_id, quantity, subtotal) 
                                VALUES (:trx_id, :product_id, :qty, :subtotal)";
                $this->db->query($detailQuery);
                $this->db->bind('trx_id', $transactionId);
                $this->db->bind('product_id', $productId);
                $this->db->bind('qty', $item['quantity']);
                $this->db->bind('subtotal', $item['price'] * $item['quantity']);
                $this->db->execute();
            }

            // 5. Jika semua berhasil, konfirmasi transaksi
            $this->db->commit();
            return true; // Berhasil

        } catch (Exception $e) {
            // 6. Jika ada satu saja yang gagal, batalkan semua proses simpan
            $this->db->rollBack();
            error_log($e->getMessage()); // Catat error untuk developer
            return false; // Gagal
        }
    }
}