<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= \App\Core\View::escape($title ?? 'Error') ?></title>
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
        <?= $content ?? 'Error content not available' ?>
    </main>
    
    <footer>
        <p>&copy; <?= date('Y') ?> Crypto Market Cap App</p>
    </footer>
</body>
</html>