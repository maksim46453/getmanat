<?php
require_once __DIR__ . '/includes/common.php';
require_once __DIR__ . '/includes/tracker.php';

track_visit('donate.php');
render_header('Донат', 'Підтримай розвиток [UA] Народний Паблік');

$products = [
    ['name' => 'VIP 30 днів', 'price' => '149₴', 'desc' => 'Пріоритетний слот, тег VIP, бонусні винагороди.'],
    ['name' => 'Premium VIP 30 днів', 'price' => '249₴', 'desc' => 'Розширені привілеї, бонусні рулетки, преміум-статус.'],
    ['name' => 'Premium VIP 90 днів', 'price' => '599₴', 'desc' => 'Максимально вигідний пакет для активних гравців.'],
    ['name' => 'Support Pack', 'price' => '99₴', 'desc' => 'Підтримка сервера + косметичні бонуси.'],
];
?>
<main class="container py-4">
    <section class="portal-card mb-4">
        <h1 class="section-title">Донат-магазин</h1>
        <p class="text-secondary">Усі покупки підтримують інфраструктуру сервера, античит, хостинг та розвиток нових режимів.</p>
    </section>

    <section class="row g-4">
        <?php foreach ($products as $product): ?>
            <div class="col-md-6 col-xl-3">
                <article class="portal-card h-100 product-card">
                    <h3><?php echo e($product['name']); ?></h3>
                    <p class="price"><?php echo e($product['price']); ?></p>
                    <p><?php echo e($product['desc']); ?></p>
                    <button class="btn btn-primary w-100" type="button">Купити</button>
                </article>
            </div>
        <?php endforeach; ?>
    </section>

    <section class="portal-card mt-4">
        <h2 class="section-title">Оплата та активація</h2>
        <ul>
            <li>Після оплати привілеї активуються на вказаний SteamID.</li>
            <li>Система підготовлена до інтеграції з платіжним шлюзом та ігровим плагіном.</li>
            <li>Для питань звертайтесь до адміністрації сервера.</li>
        </ul>
    </section>
</main>
<?php render_footer(); ?>
