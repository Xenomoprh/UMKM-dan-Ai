<?php

// File: app/controllers/Analisis.php

class Analisis extends Controller {
    public function index() {
        $data['judul'] = 'Analisis AI';
        $this->view('analisis/index', $data);
    }
}