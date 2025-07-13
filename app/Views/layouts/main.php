<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crypto</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
    <header>
        <h1>Crypto Market Cap</h1>
        <nav>
            <a href="/">Home</a>
            <a href="/cryptocurrencies">Top Cryptocurrencies</a>
        </nav>
    </header>

    <main>
        <?php include $content; ?>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> Crypto Market Cap App</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/assets/js/chart.js"></script>
</body>

</html>