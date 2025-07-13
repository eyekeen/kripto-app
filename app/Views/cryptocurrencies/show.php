<?php $content = ob_start(); ?>

<div class="crypto-detail">
    <h2>
        <?= htmlspecialchars($crypto['name']) ?>
        <small>(<?= htmlspecialchars($crypto['ticker']) ?>)</small>
    </h2>
    
    <div class="crypto-info">
        <div class="info-item">
            <span class="label">Price:</span>
            <span class="value">$<?= number_format($crypto['price'], 2) ?></span>
        </div>
        
        <div class="info-item">
            <span class="label">24h Change:</span>
            <span class="value <?= $crypto['change_24h'] >= 0 ? 'positive' : 'negative' ?>">
                <?= number_format($crypto['change_24h'], 2) ?>%
            </span>
        </div>
        
        <div class="info-item">
            <span class="label">Market Cap:</span>
            <span class="value">$<?= number_format($crypto['market_cap']) ?></span>
        </div>
        
        <div class="info-item">
            <span class="label">24h Volume:</span>
            <span class="value">$<?= number_format($crypto['trading_volume']) ?></span>
        </div>
    </div>
    
    <?php if (!empty($crypto['chart_data'])): ?>
    <div class="crypto-chart">
        <h3>Price Chart</h3>
        <!-- Здесь будет график (позже добавим JS) -->
        <div id="chart-container" data-chart='<?= htmlspecialchars($crypto['chart_data']) ?>'></div>
    </div>
    <?php endif; ?>
    
    <a href="/cryptocurrencies" class="back-link">← Back to list</a>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layouts/main.php'; ?>