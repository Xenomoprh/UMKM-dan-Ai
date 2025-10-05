<?php

// File: app/controllers/Home.php

// Pastikan Home mewarisi semua kemampuan dari Controller dasar
class Home extends Controller {
    public function index() {
        // Siapkan data yang akan dikirim ke view
        $data['judul'] = 'Halaman Utama';
        $data['nama'] = 'Pengguna'; // Anda bisa ganti dengan nama dari database nanti

        // Panggil method view dari Controller.php
        // Parameter pertama: path ke file view (tanpa .php)
        // Parameter kedua: data yang dikirim
        $this->view('home/index', $data);
    }
}