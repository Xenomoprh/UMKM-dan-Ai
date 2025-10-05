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
}