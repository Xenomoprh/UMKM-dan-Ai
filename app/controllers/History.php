<?php

class History extends Controller {
    public function index() {
        $data['judul'] = 'Riwayat Transaksi';
        
        // 1. Ambil semua transaksi
        $allTransactions = $this->model('Transaction_model')->getAllTransactions();
        
        // 2. Siapkan wadah
        $data['today'] = [];
        $data['past'] = [];
        
        // 3. Dapatkan tanggal hari ini (Format Y-m-d)
        $todayDate = date('Y-m-d');

        // 4. Pisahkan data
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