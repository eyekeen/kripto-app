<?php

/** @var array $cryptocurrencies */
/** @var string $title */
?>
<h2><?= \App\Core\View::escape($title) ?></h2>

<div class="filters">
    <form method="get" action="/">
        <input type="text" name="search" placeholder="Search by name or ticker"
            value="<?= \App\Core\View::escape($searchQuery ?? '') ?>">
        <button type="submit">Search</button>
    </form>
</div>

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
                    <?= \App\Core\View::escape($crypto['name']) ?>
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

<div class="pagination">
    <?php if ($pagination['page'] > 1): ?>
        <a href="?page=<?= $pagination['page'] - 1 ?>" class="page-link">Previous</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
        <a href="?page=<?= $i ?>" class="page-link <?= $i === $pagination['page'] ? 'active' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>

    <?php if ($pagination['page'] < $pagination['totalPages']): ?>
        <a href="?page=<?= $pagination['page'] + 1 ?>" class="page-link">Next</a>
    <?php endif; ?>
</div>