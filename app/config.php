<?php

// File: app/config.php

// Definisikan Base URL untuk mempermudah pemanggilan aset (CSS, JS, Gambar)
// BASEURL dinamis, otomatis menyesuaikan environment
if (isset($_SERVER['HTTP_HOST'])) {
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
	$base = $protocol . '://' . $_SERVER['HTTP_HOST'];
	// Ambil path ke folder public
	$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
	$publicPos = strpos($scriptName, '/public');
	if ($publicPos !== false) {
		$base .= substr($scriptName, 0, $publicPos + 7); // +7 = panjang '/public'
	}
	define('BASEURL', rtrim($base, '/'));
} else {
	define('BASEURL', 'http://localhost/Proyek_UMKM/public'); // fallback
}

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_umkm_ai');
