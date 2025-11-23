<?php require_once '../app/views/templates/header.php'; ?>

<div class="container content">
    <!-- Header -->
    <div class="history-header">
        <div class="header-content">
            <h1><i data-lucide="history" class="icon-title"></i> Riwayat Transaksi</h1>
            <p class="header-subtitle">Pantau pemasukan harian dan arsip penjualan Anda</p>
        </div>
        <div class="header-stats">
            <div class="stat-card">
                <span class="stat-label">Transaksi Hari Ini</span>
                <span class="stat-value"><?= count($data['today']); ?></span>
            </div>
        </div>
    </div>

    <!-- BAGIAN 1: HARI INI -->
    <div class="history-section">
        <div class="section-title">
            <i data-lucide="calendar-check" class="section-icon text-green"></i>
            <h2>Transaksi Hari Ini</h2>
            <span class="badge-count"><?= count($data['today']); ?> Transaksi</span>
        </div>

        <?php if (empty($data['today'])) : ?>
            <div class="empty-state">
                <i data-lucide="coffee" class="empty-icon"></i>
                <p class="empty-text">Belum ada transaksi hari ini</p>
                <p class="empty-subtext">Semangat jualan! 💪</p>
            </div>
        <?php else : ?>
            <div class="transaction-list">
                <?php foreach ($data['today'] as $trx) : ?>
                    <div class="trx-card" data-trx-id="<?= $trx['transaction_id']; ?>">
                        <div class="trx-header" onclick="toggleTransactionDetails(this)">
                            <div class="trx-left">
                                <div class="trx-time"><?= date('H:i', strtotime($trx['transaction_time'])); ?></div>
                                <div class="trx-id">#<?= $trx['transaction_id']; ?></div>
                            </div>
                            <div class="trx-middle">
                                <span class="badge-success">✓ Berhasil</span>
                            </div>
                            <div class="trx-right">
                                <div class="trx-amount">Rp <?= number_format($trx['total_amount']); ?></div>
                                <div class="trx-payment text-muted">
                                    Tunai: Rp <?= number_format($trx['payment_received']); ?>
                                </div>
                            </div>
                            <div class="trx-expand">
                                <i data-lucide="chevron-down" class="expand-icon"></i>
                            </div>
                        </div>
                        
                        <!-- Detail Produk -->
                        <div class="trx-details">
                            <div class="items-list">
                                <?php foreach ($trx['items'] as $item) : ?>
                                    <div class="item-row">
                                        <span class="item-name"><?= htmlspecialchars($item['product_name']); ?></span>
                                        <span class="item-qty"><?= $item['quantity']; ?>x</span>
                                        <span class="item-price">Rp <?= number_format($item['subtotal']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- BAGIAN 2: RIWAYAT TERDAHULU (ACCORDION) -->
    <?php if (!empty($data['past'])) : ?>
        <div class="history-section">
            <button class="btn-toggle-history" id="btn-toggle-history">
                <span class="btn-toggle-content">
                    <i data-lucide="archive"></i>
                    <span>Lihat Riwayat Terdahulu</span>
                </span>
                <i data-lucide="chevron-down" id="icon-chevron" class="chevron-icon"></i>
            </button>

            <div id="history-content" class="history-content hidden">
                <?php foreach ($data['past'] as $date => $transactions) : ?>
                    <div class="history-group">
                        <h3 class="history-date">
                            <?= date('d F Y', strtotime($date)); ?>
                            <span class="text-muted small">(<?= count($transactions); ?> Transaksi)</span>
                        </h3>
                        <div class="transaction-list">
                            <?php foreach ($transactions as $trx) : ?>
                                <div class="trx-card trx-past" data-trx-id="<?= $trx['transaction_id']; ?>">
                                    <div class="trx-header" onclick="toggleTransactionDetails(this)">
                                        <div class="trx-left">
                                            <div class="trx-time"><?= date('H:i', strtotime($trx['transaction_time'])); ?></div>
                                            <div class="trx-id">#<?= $trx['transaction_id']; ?></div>
                                        </div>
                                        <div class="trx-middle">
                                            <span class="badge-success">✓ Berhasil</span>
                                        </div>
                                        <div class="trx-right">
                                            <div class="trx-amount">Rp <?= number_format($trx['total_amount']); ?></div>
                                            <div class="trx-payment text-muted">
                                                Tunai: Rp <?= number_format($trx['payment_received']); ?>
                                            </div>
                                        </div>
                                        <div class="trx-expand">
                                            <i data-lucide="chevron-down" class="expand-icon"></i>
                                        </div>
                                    </div>
                                    
                                    <!-- Detail Produk -->
                                    <div class="trx-details">
                                        <div class="items-list">
                                            <?php foreach ($trx['items'] as $item) : ?>
                                                <div class="item-row">
                                                    <span class="item-name"><?= htmlspecialchars($item['product_name']); ?></span>
                                                    <span class="item-qty"><?= $item['quantity']; ?>x</span>
                                                    <span class="item-price">Rp <?= number_format($item['subtotal']); ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../app/views/templates/footer.php'; ?>