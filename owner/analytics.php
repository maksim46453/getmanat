<?php
require_once __DIR__ . '/admin_common.php';
owner_require_auth();
$db = owner_db();

$totalVisits = 0;
$popularPages = [];
$topCountries = [];
$trend = [];
$topEntries = [];
$error = '';

if (!$db->isConnected()) {
    $error = 'Аналітика недоступна без підключення до БД.';
} else {
    $totalVisits = $db->select('SELECT COUNT(*) AS cnt FROM website_visits')[0]['cnt'] ?? 0;
    $popularPages = $db->select('SELECT route, COUNT(*) AS views FROM website_visits GROUP BY route ORDER BY views DESC LIMIT 10');
    $topCountries = $db->select('SELECT country_code, COUNT(*) AS visits FROM website_visits GROUP BY country_code ORDER BY visits DESC LIMIT 10');
    $trend = $db->select('SELECT DATE(created_at) AS day, COUNT(*) AS visits FROM website_visits WHERE created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY) GROUP BY DATE(created_at) ORDER BY day ASC');
    $topEntries = $db->select('SELECT route, COUNT(*) AS entries FROM website_visits GROUP BY route ORDER BY entries DESC LIMIT 5');
}

owner_render_header('Analytics');
?>
<?php if ($error !== ''): ?><div class="alert alert-warning"><?php echo e($error); ?></div><?php endif; ?>
<div class="portal-card mb-3"><h2 class="section-title">Загальна статистика</h2><p class="display-6 mb-0"><?php echo e((string) $totalVisits); ?> <small class="text-secondary fs-6">візитів у системі</small></p></div>
<div class="row g-3"><div class="col-lg-6"><div class="portal-card h-100"><h3>Популярні сторінки</h3><ul class="mb-0"><?php foreach ($popularPages as $row): ?><li><?php echo e($row['route']); ?> — <?php echo e((string) $row['views']); ?> переглядів</li><?php endforeach; ?></ul></div></div><div class="col-lg-6"><div class="portal-card h-100"><h3>Країни (approx)</h3><ul class="mb-0"><?php foreach ($topCountries as $row): ?><li><?php echo e($row['country_code']); ?> — <?php echo e((string) $row['visits']); ?> візитів</li><?php endforeach; ?></ul></div></div><div class="col-lg-6"><div class="portal-card h-100"><h3>Тренд за 14 днів</h3><ul class="mb-0"><?php foreach ($trend as $row): ?><li><?php echo e($row['day']); ?> — <?php echo e((string) $row['visits']); ?> візитів</li><?php endforeach; ?></ul></div></div><div class="col-lg-6"><div class="portal-card h-100"><h3>Top entry pages</h3><ul class="mb-0"><?php foreach ($topEntries as $row): ?><li><?php echo e($row['route']); ?> — <?php echo e((string) $row['entries']); ?> входів</li><?php endforeach; ?></ul></div></div></div>
<?php owner_render_footer();
