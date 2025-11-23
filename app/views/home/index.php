<?php require_once '../app/views/templates/header.php'; ?>

<style>
    /* Tambahkan sedikit style untuk mempercantik tampilan home */
    .hero-section {
        background-color: #f8f9fa;
        padding: 4rem 2rem;
        border-radius: 12px;
        text-align: center;
        margin-bottom: 2rem;
    }
    .hero-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .hero-subtitle {
        font-size: 1.25rem;
        color: #6c757d;
        margin-bottom: 2rem;
    }
    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
    }
    .feature-card {
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .feature-icon {
        width: 48px;
        height: 48px;
        color: #0d6efd;
        margin-bottom: 1rem;
    }
    .feature-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .feature-description {
        color: #6c757d;
    }
    .btn-primary-custom {
        background-color: #0d6efd;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: background-color 0.2s;
    }
    .btn-primary-custom:hover {
        background-color: #0b5ed7;
    }
</style>

<div class="container content">
    <!-- Hero Section -->
    <div class="hero-section">
        <h1 class="hero-title">Selamat Datang, <?= htmlspecialchars($data['nama']); ?>!</h1>
        <p class="hero-subtitle">Aplikasi ini siap membantu Anda mengelola dan menganalisis penjualan UMKM Anda secara efisien.</p>
        <a href="<?= BASEURL; ?>/kasir" class="btn-primary-custom">
            <i data-lucide="shopping-cart" style="vertical-align: middle; margin-right: 8px;"></i>
            Mulai Transaksi Baru
        </a>
    </div>

    <!-- Features Grid -->
    <div class="features-grid">
        <!-- Fitur 1: Kasir -->
        <div class="feature-card">
            <i data-lucide="cash-register" class="feature-icon"></i>
            <h3 class="feature-title">Point of Sale (POS)</h3>
            <p class="feature-description">Sistem kasir yang cepat dan mudah digunakan untuk mencatat setiap transaksi penjualan Anda.</p>
        </div>

        <!-- Fitur 2: Riwayat Transaksi -->
        <div class="feature-card">
            <i data-lucide="history" class="feature-icon"></i>
            <h3 class="feature-title">Riwayat Penjualan</h3>
            <p class="feature-description">Lacak dan tinjau semua riwayat transaksi untuk memantau performa bisnis Anda dari waktu ke waktu.</p>
        </div>

        <!-- Fitur 3: Analisis AI -->
        <div class="feature-card">
            <i data-lucide="pie-chart" class="feature-icon"></i>
            <h3 class="feature-title">Analisis Cerdas</h3>
            <p class="feature-description">Dapatkan wawasan mendalam dari data penjualan Anda untuk membuat keputusan bisnis yang lebih baik.</p>
        </div>
    </div>
</div>

<?php require_once '../app/views/templates/footer.php'; ?>
