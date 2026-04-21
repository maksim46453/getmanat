<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../class/config.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(?string $token): bool
{
    return is_string($token) && isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function current_page(): string
{
    $script = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
    return $script ?: 'index.php';
}

function nav_items(): array
{
    return [
        'index.php' => 'Основна',
        'skinchanger.php' => 'SkinChanger',
        'donate.php' => 'Донат',
        'roulette.php' => 'Рулетка',
    ];
}

function render_header(string $title, string $subtitle = ''): void
{
    $serverName = '[UA] Народний Паблік';
    $serverIp = '91.211.118.100:27119';
    $activePage = current_page();
    $items = nav_items();
    ?>
    <!doctype html>
    <html lang="uk" <?php if (defined('WEB_STYLE_DARK') && WEB_STYLE_DARK) { echo 'data-bs-theme="dark"'; } ?>>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo e($title); ?> — <?php echo e($serverName); ?></title>
        <meta name="description" content="Народний паблік для відпочинку. CS2 сервер із SkinChanger, рулеткою та донат-магазином.">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <header class="portal-nav-wrap">
        <nav class="navbar navbar-expand-lg navbar-dark portal-nav container">
            <a class="navbar-brand portal-brand" href="index.php">
                <span class="brand-badge">UA</span>
                <span>
                    <strong><?php echo e($serverName); ?></strong>
                    <small><?php echo e($serverIp); ?></small>
                </span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#portalNav" aria-controls="portalNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="portalNav">
                <ul class="navbar-nav ms-auto gap-2">
                    <?php foreach ($items as $href => $label): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $activePage === $href ? 'active' : ''; ?>" href="<?php echo e($href); ?>"><?php echo e($label); ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </nav>
        <?php if ($subtitle !== ''): ?>
            <div class="container">
                <p class="page-subtitle"><?php echo e($subtitle); ?></p>
            </div>
        <?php endif; ?>
    </header>
    <?php
}

function render_footer(): void
{
    ?>
    <footer class="portal-footer mt-5">
        <div class="container py-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <strong>[UA] Народний Паблік</strong>
                <p class="mb-0 text-secondary">Народний паблік для відпочинку · CS2 · IP: 91.211.118.100:27119</p>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-light btn-sm" href="skinchanger.php">SkinChanger</a>
                <a class="btn btn-primary btn-sm" href="donate.php">Підтримати сервер</a>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    </body>
    </html>
    <?php
}
