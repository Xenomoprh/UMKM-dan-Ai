<?php

// File: app/controllers/Kasir.php

class Kasir extends Controller {
    public function index() {
        // Siapkan data untuk view
        $data['judul'] = 'Halaman Kasir';

        // Panggil model untuk mendapatkan data produk
        $data['products'] = $this->model('Product_model')->getAllProducts();

        // Muat view dan kirimkan datanya
        $this->view('kasir/index', $data);
    }

    public function prosesTransaksi() {
        // Pastikan method ini hanya bisa diakses via POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $transactionModel = $this->model('Transaction_model');

            if ($transactionModel->simpanTransaksi($_POST)) {
                // Jika berhasil, kirim respon sukses
                echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil disimpan!']);
            } else {
                // Jika gagal, kirim respon error
                echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan transaksi.']);
            }
        } else {
            // Jika diakses langsung via URL, alihkan ke halaman kasir
            header('Location: ' . BASEURL . '/kasir');
            exit;
        }
    }
}