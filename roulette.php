<?php
require_once __DIR__ . '/includes/common.php';
require_once __DIR__ . '/includes/tracker.php';

track_visit('roulette.php');
render_header('Рулетка', 'Система винагород Free/Advanced Roulette');
?>
<main class="container py-4">
    <section class="portal-card mb-4">
        <h1 class="section-title">Рулетка сервера</h1>
        <p>На порталі реалізована структура під 2 типи рулетки з подальшою інтеграцією з CS2-плагіном.</p>
    </section>

    <section class="row g-4">
        <div class="col-lg-6">
            <article class="portal-card h-100">
                <h3>Free Roulette</h3>
                <p>Розблокування після <strong>1 години</strong> ігрового часу.</p>
                <p>Підходить для базових винагород, бустерів і коротких VIP-бонусів.</p>
            </article>
        </div>
        <div class="col-lg-6">
            <article class="portal-card h-100">
                <h3>Advanced Roulette</h3>
                <p>Розблокування після <strong>3 годин</strong> ігрового часу.</p>
                <p>Розширені призи: Premium VIP, довші тривалості, рідкісні винагороди.</p>
            </article>
        </div>
    </section>

    <section class="portal-card mt-4">
        <h2 class="section-title">Правила синхронізації</h2>
        <ul>
            <li>Ігровий час скидається щодня о <strong>04:00 за Києвом</strong>.</li>
            <li>Фактичний підрахунок часу має надходити від серверного плагіна.</li>
            <li>Веб-частина підготовлена для читання playtime/eligibility з бази даних.</li>
        </ul>
    </section>
</main>
<?php render_footer(); ?>
