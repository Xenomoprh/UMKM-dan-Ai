<?php

// File: app/controllers/Analisis.php

class Analisis extends Controller {
    public function index() {
        $data['judul'] = 'Analisis AI';
        $this->view('analisis/index', $data);
    }

    public function tanyaAi() {
        // Pastikan method ini hanya diakses via POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Ambil pertanyaan yang dikirim oleh JavaScript
            $pertanyaan = $_POST['question'];

            // 1. Siapkan data untuk dikirim ke Python (dalam format JSON)
            $data = ['question' => $pertanyaan];
            $payload = json_encode($data);

            // 2. Gunakan cURL untuk mengirim permintaan ke server Python (Flask)
            
            // --- INI ADALAH BAGIAN YANG DIPERBAIKI ---
            // URL-nya harus string biasa, tanpa karakter markdown
            $ch = curl_init('http://127.0.0.1:5000/tanya_ai');
            // ------------------------------------------

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload)
            ]);
            
            // Tambahkan Timeout agar PHP tidak hang
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // Waktu tunggu koneksi 5 detik
            curl_setopt($ch, CURLOPT_TIMEOUT, 15); // Total waktu eksekusi 15 detik

            // 3. Eksekusi dan dapatkan balasan dari Python
            $result = curl_exec($ch);

            // 4. Cek jika ada error cURL
            if (curl_errno($ch)) {
                $error_msg = curl_error($ch);
                curl_close($ch);
                // Kirim balasan error ke JavaScript
                // Error yang paling umum adalah "Failed to connect" atau "Operation timed out"
                echo json_encode(['answer' => 'Error: Gagal terhubung ke server AI Python. Pesan: ' . $error_msg]);
                exit;
            }

            // 5. Tutup cURL dan kirimkan balasan (yang sudah JSON)
            curl_close($ch);
            
            // Set header JSON dan kirimkan hasilnya langsung ke JavaScript
            header('Content-Type: application/json');
            echo $result;

        } else {
            // Jika diakses langsung, alihkan
            header('Location: ' . BASEURL . '/analisis');
            exit;
        }
    }
}