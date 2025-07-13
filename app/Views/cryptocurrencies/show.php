<?php
/** @var array $crypto */
/** @var string $title */
?>

<div class="crypto-detail">
    <h2>
        <?= \App\Core\View::escape($crypto['name']) ?>
        <small>(<?= \App\Core\View::escape($crypto['ticker']) ?>)</small>
    </h2>
    
    <div class="crypto-info">
        <div class="info-item">
            <span class="label">Price:</span>
            <span class="value">$<?= number_format($crypto['price'], 2) ?></span>
        </div>
        
        <!-- Остальные поля аналогично -->
    </div>
    
    <?php if (!empty($crypto['chart_data'])): ?>
    <div class="crypto-chart">
        <h3>Price Chart</h3>
        <div id="chart-container" data-chart='<?= \App\Core\View::escape($crypto['chart_data']) ?>'></div>
    </div>
    <?php endif; ?>
    
    <a href="/cryptocurrencies" class="back-link">← Back to list</a>
</div>