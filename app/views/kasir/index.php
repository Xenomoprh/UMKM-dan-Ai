<?php require_once '../app/views/templates/header.php'; ?>

<div class="container content">
    <div class="kasir-container">
        <div class="product-grid-container">
            
            <h3>Jajanan & Makanan</h3>
            <div class="product-grid">
                <?php foreach ($data['makanan'] as $product) : ?>
                    <div class="product-card add-to-cart-btn" 
                         data-id="<?= $product['product_id']; ?>"
                         data-name="<?= $product['product_name']; ?>"
                         data-price="<?= $product['price']; ?>">
                        
                        <i data-lucide="<?= $product['icon']; ?>" class="product-card-icon"></i>
                        <span class="product-card-name"><?= htmlspecialchars($product['product_name']); ?></span>
                        <span class="product-card-price">Rp <?= number_format($product['price']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <h3 class="mt-4">Minuman</h3>
            <div class="product-grid">
                <?php foreach ($data['minuman'] as $product) : ?>
                    <div class="product-card add-to-cart-btn" 
                         data-id="<?= $product['product_id']; ?>"
                         data-name="<?= $product['product_name']; ?>"
                         data-price="<?= $product['price']; ?>">
                        
                        <i data-lucide="<?= $product['icon']; ?>" class="product-card-icon"></i>
                        <span class="product-card-name"><?= htmlspecialchars($product['product_name']); ?></span>
                        <span class="product-card-price">Rp <?= number_format($product['price']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($data['lainnya'])) : ?>
                <h3 class="mt-4">Lainnya</h3>
                <div class="product-grid">
                    <?php foreach ($data['lainnya'] as $product) : ?>
                        <div class="product-card add-to-cart-btn" 
                             data-id="<?= $product['product_id']; ?>"
                             data-name="<?= $product['product_name']; ?>"
                             data-price="<?= $product['price']; ?>">
                            
                            <i data-lucide="<?= $product['icon']; ?>" class="product-card-icon"></i>
                            <span class="product-card-name"><?= htmlspecialchars($product['product_name']); ?></span>
                            <span class="product-card-price">Rp <?= number_format($product['price']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

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