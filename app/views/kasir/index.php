<?php require_once '../app/views/templates/header.php'; ?>

<div class="container content">
    <div class="kasir-page-header">
        <h1><i data-lucide="shopping-cart" style="width: 32px; height: 32px; display: inline; vertical-align: middle; margin-right: 10px;"></i>Sistem Kasir</h1>
        <p class="header-subtitle">Pilih produk dan kelola pembayaran dengan mudah</p>
    </div>

    <div class="kasir-container">
        <!-- Kolom Produk -->
        <div class="product-grid-container">
            
            <!-- Kategori: Jajanan & Makanan -->
            <div class="category-section">
                <div class="category-header">
                    <i data-lucide="utensils" class="category-icon"></i>
                    <h2>Jajanan & Makanan</h2>
                </div>
                <div class="product-grid">
                    <?php foreach ($data['makanan'] as $product) : ?>
                        <div class="product-card add-to-cart-btn" 
                             data-id="<?= $product['product_id']; ?>"
                             data-name="<?= $product['product_name']; ?>"
                             data-price="<?= $product['price']; ?>"
                             title="Klik untuk tambah ke keranjang">
                            
                            <i data-lucide="<?= $product['icon']; ?>" class="product-card-icon"></i>
                            <span class="product-card-name"><?= htmlspecialchars($product['product_name']); ?></span>
                            <span class="product-card-price">Rp <?= number_format($product['price']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Kategori: Minuman -->
            <div class="category-section">
                <div class="category-header">
                    <i data-lucide="coffee" class="category-icon"></i>
                    <h2>Minuman</h2>
                </div>
                <div class="product-grid">
                    <?php foreach ($data['minuman'] as $product) : ?>
                        <div class="product-card add-to-cart-btn" 
                             data-id="<?= $product['product_id']; ?>"
                             data-name="<?= $product['product_name']; ?>"
                             data-price="<?= $product['price']; ?>"
                             title="Klik untuk tambah ke keranjang">
                            
                            <i data-lucide="<?= $product['icon']; ?>" class="product-card-icon"></i>
                            <span class="product-card-name"><?= htmlspecialchars($product['product_name']); ?></span>
                            <span class="product-card-price">Rp <?= number_format($product['price']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Kategori: Lainnya (jika ada) -->
            <?php if (!empty($data['lainnya'])) : ?>
                <div class="category-section">
                    <div class="category-header">
                        <i data-lucide="package" class="category-icon"></i>
                        <h2>Lainnya</h2>
                    </div>
                    <div class="product-grid">
                        <?php foreach ($data['lainnya'] as $product) : ?>
                            <div class="product-card add-to-cart-btn" 
                                 data-id="<?= $product['product_id']; ?>"
                                 data-name="<?= $product['product_name']; ?>"
                                 data-price="<?= $product['price']; ?>"
                                 title="Klik untuk tambah ke keranjang">
                                
                                <i data-lucide="<?= $product['icon']; ?>" class="product-card-icon"></i>
                                <span class="product-card-name"><?= htmlspecialchars($product['product_name']); ?></span>
                                <span class="product-card-price">Rp <?= number_format($product['price']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <!-- Kolom Keranjang (Sidebar) -->
        <div class="cart">
            <div class="cart-header">
                <i data-lucide="shopping-bag" style="width: 24px; height: 24px;"></i>
                <h3>Keranjang Belanja</h3>
            </div>

            <div id="cart-items" class="cart-items">
                <p class="empty-cart-message">
                    <i data-lucide="inbox" style="width: 32px; height: 32px; display: block; margin: 0 auto 10px; opacity: 0.5;"></i>
                    Keranjang masih kosong
                </p>
            </div>
            
            <div class="cart-summary">
                <div class="summary-row">
                    <span>Total Item:</span>
                    <span id="total-items">0</span>
                </div>
                <div class="summary-row total-row">
                    <span>Total Harga:</span>
                    <span id="cart-total">Rp 0</span>
                </div>
            </div>

            <div class="payment-section">
                <label for="payment-amount" class="payment-label">
                    <i data-lucide="credit-card" style="width: 16px; height: 16px; display: inline; margin-right: 5px;"></i>
                    Uang Tunai (Rp)
                </label>
                <input type="number" 
                       id="payment-amount" 
                       class="payment-input"
                       placeholder="Masukkan jumlah uang..." 
                       min="0"
                       step="1000">
                
                <div class="change-display">
                    <span class="change-label">Kembalian:</span>
                    <span id="change-display" class="change-amount">Rp 0</span>
                </div>
            </div>

            <button class="btn-bayar" id="btn-bayar">
                <i data-lucide="check-circle" style="width: 18px; height: 18px; display: inline; margin-right: 8px;"></i>
                Selesaikan Pembayaran
            </button>

            <button class="btn-clear-cart" id="btn-clear-cart">
                <i data-lucide="trash-2" style="width: 16px; height: 16px; display: inline; margin-right: 6px;"></i>
                Bersihkan Keranjang
            </button>
        </div>
    </div>
</div>

<?php require_once '../app/views/templates/footer.php'; ?>