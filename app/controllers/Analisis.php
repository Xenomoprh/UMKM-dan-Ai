<?php

// File: app/controllers/Analisis.php

class Analisis extends Controller {
    public function index() {
        $data['judul'] = 'Analisis AI';
        $this->view('analisis/index', $data);
    }

    // METHOD BARU UNTUK MENERIMA PERTANYAAN DARI JAVASCRIPT
    public function tanyaAi() {
        // Pastikan method ini hanya diakses via POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Ambil pertanyaan yang dikirim oleh JavaScript
            $pertanyaan = $_POST['question'];

            // ---- DI SINILAH NANTI KITA AKAN MEMANGGIL PYTHON ----
            // Untuk sekarang, kita buat jawaban dummy dulu dari PHP
            $jawabanDummy = "PHP menerima pertanyaan Anda: '" . htmlspecialchars($pertanyaan) . "'. Langkah selanjutnya adalah menghubungkan ini ke Python.";

            // Kirim jawaban kembali ke JavaScript dalam format JSON
            header('Content-Type: application/json');
            echo json_encode(['answer' => $jawabanDummy]);

        } else {
            // Jika diakses langsung, alihkan
            header('Location: ' . BASEURL . '/analisis');
            exit;
        }
    }
}