<?php $content = ob_start(); ?>

<div class="error-page">
    <h2>404 Not Found</h2>
    <p>The page you requested could not be found.</p>
    <a href="/" class="back-link">← Go to Homepage</a>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/../layouts/main.php'; ?>