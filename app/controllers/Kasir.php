<?php

// File: app/controllers/Kasir.php

class Kasir extends Controller {
    public function index() {
        $data['judul'] = 'Halaman Kasir';

        // 1. Ambil semua produk dari model
        $allProducts = $this->model('Product_model')->getAllProducts();

        // 2. Siapkan array untuk kategori (INI BAGIAN PENTING YANG MEMPERBAIKI ERROR)
        // Kita harus membuat array kosong ini terlebih dahulu.
        $data['makanan'] = [];
        $data['minuman'] = [];
        $data['lainnya'] = [];

        // 3. Logika untuk memisahkan produk berdasarkan kategori
        // Ini adalah cara sederhana tanpa mengubah database
        if (!empty($allProducts)) {
            foreach ($allProducts as $product) {
                $namaProduk = strtolower($product['product_name']);
                
                // Tentukan kategori dan ikon
                if (str_contains($namaProduk, 'teh') || str_contains($namaProduk, 'kopi') || str_contains($namaProduk, 'es jeruk') || str_contains($namaProduk, 'air mineral')) {
                    $product['icon'] = 'coffee'; // Ikon untuk minuman
                    $data['minuman'][] = $product;
                } elseif (str_contains($namaProduk, 'bakwan') || str_contains($namaProduk, 'tahu') || str_contains($namaProduk, 'risol') || str_contains($namaProduk, 'pisang') || str_contains($namaProduk, 'onde') || str_contains($namaProduk, 'kue') || str_contains($namaProduk, 'lemper') || str_contains($namaProduk, 'dadar') || str_contains($namaProduk, 'bika')) {
                    $product['icon'] = 'utensils-crossed'; // Ikon untuk makanan
                    $data['makanan'][] = $product;
                } else {
                    $product['icon'] = 'package'; // Ikon default
                    $data['lainnya'][] = $product;
                }
            }
        }

        // 4. Muat view dan kirimkan data yang sudah dikategorikan
        // Sekarang $data['makanan'] dan $data['minuman'] dijamin ada (meskipun kosong)
        $this->view('kasir/index', $data);
    }

    public function prosesTransaksi() {
        // Method prosesTransaksi Anda tetap sama
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $transactionModel = $this->model('Transaction_model');
            
            if ($transactionModel->simpanTransaksi($_POST)) {
                echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil disimpan!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan transaksi.']);
            }
        } else {
            header('Location: ' . BASEURL . '/kasir');
            exit;
        }
    }
}