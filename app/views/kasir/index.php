<?php require_once '../app/views/templates/header.php'; ?>

<div class="container content">
    <div class="kasir-page-header">
        <h1><i data-lucide="shopping-cart" style="width: 32px; height: 32px; display: inline; vertical-align: middle; margin-right: 10px;"></i>Sistem Kasir</h1>
        <p class="header-subtitle">Pilih produk dan kelola pembayaran dengan mudah</p>
    </div>

    <div class="kasir-container">
        <!-- Kolom Produk -->
        <div class="product-grid-container">
            <!-- Tombol Manage Products -->
            <div class="manage-products-bar">
                <button class="btn-add-product" id="btn-add-product">
                    <i data-lucide="plus" style="width: 18px; height: 18px; display: inline; margin-right: 6px;"></i>
                    Tambah Produk
                </button>
                <button class="btn-manage-categories" id="btn-manage-categories">
                    <i data-lucide="tag" style="width: 18px; height: 18px; display: inline; margin-right: 6px;"></i>
                    Kelola Kategori
                </button>
                
                <!-- Toggle untuk Show/Hide Edit & Delete Buttons -->
                <label class="toggle-label" title="Toggle untuk tampilkan/sembunyikan tombol edit dan delete">
                    <input type="checkbox" id="toggle-edit-delete" class="toggle-checkbox">
                    <span class="toggle-slider"></span>
                    <span class="toggle-text">Tampilkan Edit/Delete</span>
                </label>
            </div>
            
            <!-- Kategori Dinamis -->
            <?php foreach ($data['categories'] as $kategori => $categoryData) : ?>
                <?php if (!empty($categoryData['products'])) : ?>
                    <div class="category-section">
                        <div class="category-header">
                            <i data-lucide="<?= htmlspecialchars($categoryData['icon']); ?>" class="category-icon"></i>
                            <h2><?= htmlspecialchars($categoryData['name']); ?></h2>
                        </div>
                        <div class="product-grid">
                            <?php foreach ($categoryData['products'] as $product) : ?>
                                <div class="product-card" data-category="<?= htmlspecialchars(strtolower(str_replace(' ', '-', $kategori))); ?>">
                                    <div class="product-card-main add-to-cart-btn" 
                                         data-id="<?= $product['product_id']; ?>"
                                         data-name="<?= $product['product_name']; ?>"
                                         data-price="<?= $product['price']; ?>"
                                         title="Klik untuk tambah ke keranjang">
                                        
                                        <i data-lucide="<?= htmlspecialchars($product['icon'] ?? 'package'); ?>" class="product-card-icon"></i>
                                        <span class="product-card-name"><?= htmlspecialchars($product['product_name']); ?></span>
                                        <span class="product-card-price">Rp <?= number_format($product['price']); ?></span>
                                    </div>
                                    <div class="product-actions">
                                        <button class="btn-edit-product" data-id="<?= $product['product_id']; ?>" title="Edit Produk" aria-label="Edit">
                                            ✏️
                                        </button>
                                        <button class="btn-delete-product" data-id="<?= $product['product_id']; ?>" title="Hapus Produk" aria-label="Hapus">
                                            🗑️
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>

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
                
                <!-- Tombol Preset Nominal -->
                <div class="preset-amount-buttons">
                    <button type="button" class="preset-btn" data-amount="10000">10K</button>
                    <button type="button" class="preset-btn" data-amount="20000">20K</button>
                    <button type="button" class="preset-btn" data-amount="50000">50K</button>
                    <button type="button" class="preset-btn" data-amount="75000">75K</button>
                    <button type="button" class="preset-btn" data-amount="100000">100K</button>
                </div>
                
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

<!-- Modal Hasil Pembayaran -->
<div id="payment-modal" class="modal hidden">
    <div class="modal-content">
        <div class="modal-header">
            <h2>✓ Pembayaran Berhasil</h2>
            <button class="modal-close" id="modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="payment-result">
                <div class="result-row">
                    <span class="result-label">Total Belanja:</span>
                    <span class="result-value" id="modal-total">Rp 0</span>
                </div>
                <div class="result-row">
                    <span class="result-label">Uang Diterima:</span>
                    <span class="result-value" id="modal-payment">Rp 0</span>
                </div>
                <div class="result-row highlight">
                    <span class="result-label">Kembalian:</span>
                    <span class="result-value result-change" id="modal-change">Rp 0</span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-modal-ok" id="btn-modal-ok">OK</button>
        </div>
    </div>
</div>

<!-- Modal Add/Edit Product -->
<div id="product-modal" class="modal hidden">
    <div class="modal-content modal-form">
        <div class="modal-header">
            <h2 id="product-modal-title">Tambah Produk Baru</h2>
            <button class="modal-close" id="product-modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <form id="product-form">
                <input type="hidden" id="product-id" name="product_id">
                
                <div class="form-group">
                    <label for="product-name">Nama Produk *</label>
                    <input type="text" id="product-name" name="product_name" required placeholder="Masukkan nama produk">
                </div>

                <div class="form-group">
                    <label for="product-category">Kategori *</label>
                    <select id="product-category" name="kategori" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Jajanan & Makanan">Jajanan & Makanan</option>
                        <option value="Minuman">Minuman</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="product-price">Harga Jual (Rp) *</label>
                    <input type="number" id="product-price" name="price" required placeholder="Masukkan harga jual" min="0" step="1000">
                </div>

                <div class="form-group">
                    <label for="product-cost">Harga Modal (Rp)</label>
                    <input type="number" id="product-cost" name="cost_of_goods" placeholder="Masukkan harga modal" min="0" step="1000">
                </div>

                <div class="form-group">
                    <label for="product-stock">Stock Awal</label>
                    <input type="number" id="product-stock" name="stock_quantity" placeholder="Masukkan jumlah stock" min="0" value="0">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" id="product-modal-cancel">Batal</button>
            <button class="btn-save" id="product-modal-save">Simpan</button>
        </div>
    </div>
</div>

<!-- Modal Manage Categories -->
<div id="category-modal" class="modal hidden">
    <div class="modal-content modal-form">
        <div class="modal-header">
            <h2>Kelola Kategori</h2>
            <button class="modal-close" id="category-modal-close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="category-list" id="category-list">
                <!-- Akan diisi oleh JavaScript -->
            </div>
            <div class="form-group">
                <label for="new-category">Tambah Kategori Baru</label>
                <div class="add-category-input">
                    <input type="text" id="new-category" placeholder="Nama kategori baru" maxlength="50">
                    <button class="btn-add-category" id="btn-add-category">Tambah</button>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-close-modal" id="category-modal-done">Selesai</button>
        </div>
    </div>
</div>

<?php require_once '../app/views/templates/footer.php'; ?>