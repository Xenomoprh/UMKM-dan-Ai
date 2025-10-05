<?php require_once '../app/views/templates/header.php'; ?>

<div class="container content">
    <h2>Halaman Kasir</h2>
    <div class="kasir-container">
        <div class="product-list">
            <h3>Daftar Produk</h3>
            <?php foreach ($data['products'] as $product) : ?>
                <div class="product-item">
                    <span><?= $product['product_name']; ?></span>
                    <span>Rp <?= number_format($product['price']); ?></span>
                    <button class="add-to-cart-btn" 
                            data-id="<?= $product['product_id']; ?>"
                            data-name="<?= $product['product_name']; ?>"
                            data-price="<?= $product['price']; ?>">
                        Tambah
                    </button>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="cart">
            <h3>Keranjang</h3>
            <div id="cart-items" class="cart-items">
                <p>Keranjang masih kosong.</p>
            </div>
            <div class="cart-total">
                <h4 id="cart-total">Total: Rp 0</h4>
            </div>
            <button class="btn-bayar">Bayar</button>
        </div>
    </div>
</div>

<?php require_once '../app/views/templates/footer.php'; ?>