<?php

class History extends Controller {
    public function index() {
        // FIX: Tambahkan header untuk mencegah browser caching
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        // FIX: Set timezone ke 'Asia/Jakarta' untuk menyamakan waktu server dan aplikasi
        date_default_timezone_set('Asia/Jakarta');

        $data['judul'] = 'Riwayat Transaksi';
        
        // 1. Ambil semua transaksi
        $allTransactions = $this->model('Transaction_model')->getAllTransactions();
        
        // 2. Untuk setiap transaksi, ambil detail produknya
        $transactionModel = $this->model('Transaction_model');
        foreach ($allTransactions as &$trx) {
            $trx['items'] = $transactionModel->getTransactionDetails($trx['transaction_id']);
        }
        
        // 3. Siapkan wadah
        $data['today'] = [];
        $data['past'] = [];
        
        // 4. Dapatkan tanggal hari ini (Format Y-m-d)
        $todayDate = date('Y-m-d');

        // 5. Pisahkan data
        foreach ($allTransactions as $trx) {
            // Ambil tanggal saja dari timestamp (contoh: 2025-10-21 09:30:00 -> 2025-10-21)
            $trxDate = date('Y-m-d', strtotime($trx['transaction_time']));
            
            if ($trxDate === $todayDate) {
                $data['today'][] = $trx;
            } else {
                // Kelompokkan histori berdasarkan tanggal agar rapi
                $data['past'][$trxDate][] = $trx;
            }
        }

        $this->view('history/index', $data);
    }
}