<?php
/** @var array $cryptocurrencies */
/** @var string $title */
?>
<h2><?= \App\Core\View::escape($title) ?></h2>

<table class="crypto-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Ticker</th>
            <th>Price</th>
            <th>24h Change</th>
            <th>Market Cap</th>
            <th>Volume (24h)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($cryptocurrencies as $index => $crypto): ?>
        <tr>
            <td><?= $index + 1 ?></td>
            <td>
                <a href="/cryptocurrencies/<?= \App\Core\View::escape($crypto['ticker']) ?>">
                    <?= \App\Core\View::escape($crypto['name']) ?>
                </a>
            </td>
            <td><?= \App\Core\View::escape($crypto['ticker']) ?></td>
            <td>$<?= number_format($crypto['price'], 2) ?></td>
            <td class="<?= $crypto['change_24h'] >= 0 ? 'positive' : 'negative' ?>">
                <?= number_format($crypto['change_24h'], 2) ?>%
            </td>
            <td>$<?= number_format($crypto['market_cap']) ?></td>
            <td>$<?= number_format($crypto['trading_volume']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>