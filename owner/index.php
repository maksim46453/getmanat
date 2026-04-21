<?php
require_once __DIR__ . '/admin_common.php';
owner_require_auth();
owner_db();
owner_render_header('Dashboard');
?>
<div class="row g-3">
    <div class="col-lg-6">
        <div class="portal-card h-100">
            <h3>Roulette Management</h3>
            <p>Керуйте нагородами Free та Advanced Roulette: шанси, тривалість, вмикання/вимикання.</p>
            <a class="btn btn-primary" href="rewards.php">Відкрити менеджер нагород</a>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="portal-card h-100">
            <h3>Website Analytics</h3>
            <p>Перегляд загальних візитів, популярних сторінок, трендів і країн відвідувачів.</p>
            <a class="btn btn-primary" href="analytics.php">Відкрити аналітику</a>
        </div>
    </div>
</div>
<div class="portal-card mt-3">
    <h4>Інтеграційні примітки</h4>
    <ul>
        <li>Playtime/eligibility для рулетки має надходити від CS2 плагіна.</li>
        <li>Щоденний reset о 04:00 Europe/Kyiv реалізується на стороні плагіна/cron-job.</li>
        <li>Поточна веб-панель готує структуру даних і адмін-управління.</li>
    </ul>
</div>
<?php owner_render_footer();
