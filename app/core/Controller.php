<?php

// File: app/core/Controller.php

class Controller {
    /**
     * Method untuk memuat file View
     * @param string $view Nama file view di dalam folder 'views'
     * @param array $data Data yang ingin dikirimkan ke view
     */
    public function view($view, $data = []) {
        // Cek apakah file view-nya ada
        if (file_exists('../app/views/' . $view . '.php')) {
            require_once '../app/views/' . $view . '.php';
        } else {
            // Jika tidak ada, tampilkan pesan error
            die('View tidak ditemukan: ' . $view);
        }
    }

    /**
     * Method untuk memuat file Model
     * @param string $model Nama file model di dalam folder 'models'
     */
    public function model($model) {
        require_once '../app/models/' . $model . '.php';
        // Instansiasi model agar bisa langsung dipakai di controller
        return new $model;
    }
}
// Kurung kurawal ekstra di sini sudah dihapus