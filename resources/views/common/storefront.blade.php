<?php
// Shared home view for all existing themes. Checkout and order routes stay intact.
$escape = static function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
$groups = $data ?? [];
$products = [];
foreach ($groups as $group) {
    foreach ($group['goods'] ?? [] as $goods) {
        $goods['_group'] = (string) $group['id'];
        $goods['_category'] = $group['gp_name'];
        $products[] = $goods;
    }
}
$featured = array_slice(array_values(array_filter($products, static function ($goods) {
    return (int) ($goods['in_stock'] ?? 0) > 0;
})), 0, 3);
$siteName = dujiaoka_config_get('text_logo') ?: dujiaoka_config_get('title', '数字小铺');
$logo = dujiaoka_config_get('img_logo');
$price = static function ($goods) { return number_format((float) $goods['actual_price'], 2, '.', ''); };
?>
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= $escape(dujiaoka_config_get('description', '发现好用的数字商品')) ?>">
    <meta name="keywords" content="<?= $escape(dujiaoka_config_get('keywords', '')) ?>">
    <meta name="color-scheme" content="light dark">
    <title><?= $escape($siteName) ?> · 商品中心</title>
    <link rel="icon" href="/assets/style/favicon.ico">
    <link rel="stylesheet" href="/assets/storefront/storefront.css?v=1">
    <script src="/assets/storefront/storefront.js?v=1" defer></script>
</head>
<body>
<a class="skip-link" href="#products">跳至商品</a>
<header class="site-header">
    <div class="header-inner">
        <a class="brand" href="/" aria-label="<?= $escape($siteName) ?> 首页">
            <?php if ($logo): ?><img class="brand-mark" src="<?= $escape(picture_ulr($logo)) ?>" alt=""><?php else: ?><span class="brand-mark">w<span>✦</span></span><?php endif; ?>
            <span class="brand-copy"><strong><?= $escape($siteName) ?></strong><small>DIGITAL STORE</small></span>
        </a>
        <nav class="desktop-nav" aria-label="主导航"><a class="current" href="/" aria-current="page">商品中心</a><a href="<?= $escape(url('order-search')) ?>">查询订单 <span>↗</span></a><button type="button" data-notice>站点公告</button></nav>
        <div class="header-actions"><button class="icon-button" type="button" data-theme aria-label="切换深色模式" title="切换深色模式">◐</button><a class="order-button" href="<?= $escape(url('order-search')) ?>"><span>▤</span> 我的订单</a></div>
    </div>
</header>
<main class="store-shell">
    <?php if (!empty($previewMode)): ?><div class="preview-banner">设计预览 · 以下为演示商品，未连接线上数据库或支付</div><?php endif; ?>
    <section class="intro" aria-labelledby="intro-title">
        <div><p class="eyebrow"><span class="status-dot"></span> YOUR EVERYDAY DIGITAL ESSENTIALS</p><h1 id="intro-title">数字生活，<span>轻松一点。</span></h1><p class="intro-copy">精选好用的数字商品，让每一份热爱都触手可及。</p></div>
        <div class="intro-note"><span class="note-spark">✳</span><span>好东西，不必复杂。<small>发现 · 选择 · 即刻开启</small></span></div>
    </section>
    <?php if ($featured): ?>
    <section class="featured-section" aria-labelledby="featured-title">
        <div class="section-heading"><div><p class="eyebrow">THE SELECTION</p><h2 id="featured-title">值得一看<span class="heading-dot">.</span></h2></div><a class="text-link" href="#products">探索全部商品 <span>↗</span></a></div>
        <div class="featured-grid">
        <?php foreach ($featured as $index => $goods): ?>
            <a class="feature-card tone-<?= $index ?>" href="<?= $escape(url('buy/' . (int) $goods['id'])) ?>">
                <div class="feature-top"><span class="feature-category"><?= $escape($goods['_category']) ?></span><span class="round-arrow">↗</span></div>
                <h3><?= $escape($goods['gd_name']) ?></h3><p class="feature-description"><?= (int) $goods['type'] === 1 ? '自动发货 · 支付后查看卡密' : '人工处理 · 具体时效见商品说明' ?></p>
                <div class="card-art" aria-hidden="true"><div class="art-orbit"></div><div class="art-card art-back"><span>DIGITAL</span><b>Good things.</b><small>FOR YOUR EVERYDAY</small></div><div class="art-card art-front"><span><?= $escape($goods['_category']) ?></span><b><?= ['✳', '✧', '⌘'][$index] ?></b><small>MAKE IT YOURS <span>↗</span></small></div></div>
                <div class="feature-bottom"><span class="feature-price"><small>¥</small><?= $price($goods) ?></span><span class="feature-cta">查看商品 <span>→</span></span></div>
            </a>
        <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
    <section class="catalog" id="products" aria-labelledby="catalog-title">
        <div class="section-heading catalog-heading"><div><p class="eyebrow">FIND YOUR NEXT FAVORITE</p><h2 id="catalog-title">全部商品<span class="heading-dot">.</span></h2></div><span class="catalog-total" id="product-count" aria-live="polite"><?= count($products) ?> 件商品</span></div>
        <div class="catalog-toolbar"><div class="category-tabs" role="group" aria-label="商品分类"><button class="category-tab active" type="button" data-category="all" aria-pressed="true">全部 <span><?= count($products) ?></span></button><?php foreach ($groups as $group): ?><button class="category-tab" type="button" data-category="<?= $escape($group['id']) ?>" aria-pressed="false"><?= $escape($group['gp_name']) ?> <span><?= count($group['goods'] ?? []) ?></span></button><?php endforeach; ?></div>
        <div class="search-box"><span aria-hidden="true">⌕</span><input id="product-search" type="search" placeholder="搜索你想要的好物…" aria-label="搜索商品" autocomplete="off"><kbd>/</kbd></div></div>
        <div class="catalog-options"><label class="stock-filter"><input id="in-stock" type="checkbox"> 只看有货</label><label class="sort-label">排序 <select id="product-sort" aria-label="商品排序"><option value="default">默认推荐</option><option value="price-asc">价格从低到高</option><option value="price-desc">价格从高到低</option></select></label></div>
        <div class="product-grid" id="product-grid">
        <?php foreach ($products as $index => $goods): $available = (int) ($goods['in_stock'] ?? 0) > 0; ?>
            <article class="product-card <?= $available ? '' : 'sold-out' ?>" data-group="<?= $escape($goods['_group']) ?>" data-name="<?= $escape($goods['gd_name'] . ' ' . $goods['_category']) ?>" data-price="<?= $price($goods) ?>" data-stock="<?= $available ? '1' : '0' ?>" data-index="<?= $index ?>">
                <?php if ($available): ?><a class="product-link" href="<?= $escape(url('buy/' . (int) $goods['id'])) ?>"><?php else: ?><div class="product-link" aria-label="<?= $escape($goods['gd_name']) ?>，暂时售罄"><?php endif; ?>
                    <div class="product-top"><span class="product-icon icon-<?= $index % 4 ?>"><?php if (!empty($goods['picture'])): ?><img src="<?= $escape(picture_ulr($goods['picture'])) ?>" alt="" loading="lazy"><?php else: ?><span aria-hidden="true"><?= ['✳', '✧', '⌘', '◈'][$index % 4] ?></span><?php endif; ?></span><span class="product-category"><?= $escape($goods['_category']) ?></span></div>
                    <h3><?= $escape($goods['gd_name']) ?></h3>
                    <div class="product-tags"><span class="delivery-tag"><?= (int) $goods['type'] === 1 ? '自动发货' : '人工处理' ?></span><span>游客可购</span></div>
                    <div class="product-bottom"><span class="product-price"><small>¥</small><?= $price($goods) ?></span><span class="stock-state <?= $available ? '' : 'unavailable' ?>"><i></i><?= $available ? '有库存' : '暂时售罄' ?></span></div>
                <?php if ($available): ?></a><?php else: ?></div><?php endif; ?>
            </article>
        <?php endforeach; ?>
        </div>
        <div class="empty-state" id="empty-state" <?= $products ? 'hidden' : '' ?>><span aria-hidden="true">⌕</span><h3><?= $products ? '没有找到相关商品' : '好物正在准备中' ?></h3><p><?= $products ? '换个关键词，或试试其他分类。' : '商品上架后会在这里显示，欢迎稍后再来。' ?></p><button type="button" id="reset-filters">重置筛选</button></div>
    </section>
    <aside class="help-strip"><span class="help-symbol">✧</span><div><strong>购买之后，随时找回。</strong><p>使用下单邮箱或订单号，查询订单与交付信息。</p></div><a href="<?= $escape(url('order-search')) ?>">查询我的订单 <span>↗</span></a></aside>
    <footer class="site-footer"><div><span class="footer-brand"><?= $escape($siteName) ?></span><span>让数字生活，多一点美好。</span></div><div class="configured-footer"><?= dujiaoka_config_get('footer', '') ?></div><small>Powered by <a href="https://github.com/assimon/dujiaoka" rel="noopener noreferrer" target="_blank">独角数卡</a></small></footer>
</main>
<nav class="mobile-nav" aria-label="移动导航"><a href="/" class="active" aria-current="page"><span>▦</span>逛商店</a><a href="<?= $escape(url('order-search')) ?>"><span>▤</span>查订单</a><button type="button" data-notice><span>♧</span>看公告</button><a href="#products"><span>⌕</span>找好物</a></nav>
<dialog id="notice-dialog" aria-labelledby="notice-title"><div class="dialog-header"><div><p class="eyebrow">STORE NOTICE</p><h2 id="notice-title">站点公告</h2></div><button class="icon-button" type="button" data-close-notice aria-label="关闭公告">×</button></div><div class="notice-content"><?= dujiaoka_config_get('notice') ?: '暂无新公告，欢迎来到我们的小铺。' ?></div></dialog>
</body>
</html>
