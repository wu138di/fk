<?php
// Shared home view for all existing themes. Checkout and order routes stay intact.
$escape = static function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
$catalog = require resource_path('views/common/storefront-catalog.php');
$groups = $catalog['groups'];
$products = $catalog['products'];
$featured = array_slice(array_values(array_filter($products, static function ($goods) {
    return (int) ($goods['in_stock'] ?? 0) > 0 || $goods['_pending'];
})), 0, 3);
$siteName = dujiaoka_config_get('text_logo') ?: dujiaoka_config_get('title', '数字小铺');
$logo = dujiaoka_config_get('img_logo');
$price = static function ($goods) { return number_format((float) $goods['actual_price'], 2, '.', ''); };
$gptPrice = '135.00';
foreach ($products as $product) {
    if (!$product['_fanbox']) { $gptPrice = $price($product); break; }
}
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
        <div><p class="eyebrow"><span class="status-dot"></span> FANBOX & GPT JAPAN</p><h1 id="intro-title">喜欢的创作，<span>好好支持。</span></h1><p class="intro-copy">FANBOX 980 / 480 方案 · 号上直冲 / 文件发送<br>另有 GPT 日区充值，具体套餐以商品说明为准。</p></div>
        <div class="intro-note"><span class="note-spark">✳</span><span>只留你需要的。<small>FANBOX · GPT 日区</small></span></div>
    </section>
    <?php if ($featured): ?>
    <section class="featured-section" aria-labelledby="featured-title">
        <div class="section-heading"><div><p class="eyebrow">PICK YOUR PLAN</p><h2 id="featured-title">选一个，刚刚好<span class="heading-dot">.</span></h2></div><a class="text-link" href="#products">查看方案 <span>↗</span></a></div>
        <div class="featured-grid">
        <?php foreach ($featured as $index => $goods): ?>
            <?php if (!$goods['_pending']): ?><a class="feature-card tone-<?= $index ?> <?= $goods['_fanbox'] ? 'fanbox-feature' : 'gpt-feature' ?>" href="<?= $escape(url('buy/' . (int) $goods['id'])) ?>"><?php else: ?><a class="feature-card tone-<?= $index ?> gpt-feature pending-feature" href="#gpt-info"><?php endif; ?>
                <div class="feature-top"><span class="feature-category"><?= $escape($goods['_category']) ?></span><span class="round-arrow">↗</span></div>
                <h3><?= $escape($goods['gd_name']) ?></h3><p class="feature-description"><?= $goods['_fanbox'] ? '号上直冲 / 文件发送 · 两种交付可选' : '日本区 · 充值方案 · 下单前确认套餐' ?></p>
                <?php if ($goods['_fanbox'] && $goods['_cover']): ?><div class="fanbox-art"><img src="<?= $escape($goods['_cover']) ?>" alt="<?= $escape($goods['gd_name']) ?> 原方案配图"><span>FANBOX <b><?= preg_match('/\b(980|480)\b/', $goods['gd_name'], $plan) ? $plan[1] : 'PLAN' ?></b></span></div><?php else: ?>
                <div class="card-art gpt-art" aria-hidden="true"><div class="art-orbit"></div><div class="art-card art-back"><span>JAPAN REGION</span><b>ChatGPT</b><small>YOUR NEXT IDEA</small></div><div class="art-card art-front"><span>ChatGPT <i class="japan-dot"></i></span><b>日区充值</b><small>JAPAN <span>↗</span></small></div></div>
                <?php endif; ?>
                <div class="feature-bottom"><?php if ($goods['_pending']): ?><span class="feature-price"><small>¥</small><?= $price($goods) ?></span><span class="feature-cta">查看说明 <span>→</span></span><?php else: ?><span class="feature-price"><small>¥</small><?= $price($goods) ?></span><span class="feature-cta">选择交付 <span>→</span></span><?php endif; ?></div>
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
        <?php foreach ($products as $index => $goods): $available = !$goods['_pending'] && (int) ($goods['in_stock'] ?? 0) > 0; ?>
            <article class="product-card <?= $available ? '' : 'sold-out' ?>" data-group="<?= $escape($goods['_group']) ?>" data-name="<?= $escape($goods['gd_name'] . ' ' . $goods['_category']) ?>" data-price="<?= $price($goods) ?>" data-pending="<?= $goods['_pending'] ? '1' : '0' ?>" data-stock="<?= $available ? '1' : '0' ?>" data-index="<?= $index ?>">
                <?php if ($available): ?><a class="product-link" href="<?= $escape(url('buy/' . (int) $goods['id'])) ?>"><?php else: ?><div class="product-link" aria-label="<?= $escape($goods['gd_name']) ?>，<?= $goods['_pending'] ? '待上架' : '暂时售罄' ?>"><?php endif; ?>
                    <div class="product-top"><span class="product-icon icon-<?= $index % 4 ?>"><?php if ($goods['_cover']): ?><img src="<?= $escape($goods['_cover']) ?>" alt="" loading="lazy"><?php else: ?><span aria-hidden="true"><?= $goods['_fanbox'] ? 'F' : '✳' ?></span><?php endif; ?></span><span class="product-category"><?= $escape($goods['_category']) ?></span></div>
                    <h3><?= $escape($goods['gd_name']) ?></h3>
                    <div class="product-tags"><?php if ($goods['_fanbox']): ?><span class="delivery-tag">号上直冲</span><span>文件发送</span><?php else: ?><span class="delivery-tag">日本区</span><span>充值服务</span><?php endif; ?></div>
                    <div class="product-bottom"><span class="product-price"><small>¥</small><?= $price($goods) ?></span><span class="stock-state <?= $available ? '' : 'unavailable' ?>"><i></i><?= $goods['_pending'] ? '待上架' : ($available ? ((int) $goods['in_stock'] <= 3 ? '库存紧张' : '有库存') : '暂时售罄') ?></span></div>
                <?php if ($available): ?></a><?php else: ?></div><?php endif; ?>
            </article>
        <?php endforeach; ?>
        </div>
        <div class="empty-state" id="empty-state" <?= $products ? 'hidden' : '' ?>><span aria-hidden="true">⌕</span><h3><?= $products ? '没有找到相关商品' : '好物正在准备中' ?></h3><p><?= $products ? '换个关键词，或试试其他分类。' : '商品上架后会在这里显示，欢迎稍后再来。' ?></p><button type="button" id="reset-filters">重置筛选</button></div>
    </section>
    <section class="service-guide" id="guide" aria-labelledby="guide-title"><div class="section-heading"><div><p class="eyebrow">BEFORE YOU ORDER</p><h2 id="guide-title">FANBOX 购买流程<span class="heading-dot">.</span></h2></div></div><div class="guide-grid"><article><span>01</span><h3>选好档位</h3><p>980 与 480 两档。确认方案与交付范围后，再进入商品详情。</p></article><article><span>02</span><h3>选择交付</h3><p>号上直冲 / 文件发送均支持。直冲填写账号标识或主页链接；文件发送填写接收邮箱。</p></article><article><span>03</span><h3>核对订单</h3><p>核对金额与交付信息，按真实订单页提示付款。不收集账号密码或验证码。</p></article></div></section>
    <section class="gpt-info" id="gpt-info" aria-labelledby="gpt-title"><div><p class="eyebrow">CHATGPT · JAPAN</p><h2 id="gpt-title">GPT 充值 · 日本区</h2><p>面向日区使用场景的充值方案。下单前请核对套餐、订阅周期、账号条件与交付方式。</p></div><div class="gpt-info-note"><strong>GPT 日区充值 · ¥<?= $gptPrice ?></strong><span>套餐周期与交付细节待确认，正式下单以后台商品配置为准。</span></div></section>
    <section class="fanbox-faq" aria-labelledby="faq-title"><p class="eyebrow">A LITTLE HELP</p><h2 id="faq-title">下单前，你可能想知道</h2><details><summary>980 和 480 都支持两种交付吗？</summary><p>支持。FANBOX 两档均可选择号上直冲或文件发送，具体范围以对应商品说明为准。</p></details><details><summary>号上直冲要提供密码吗？</summary><p>页面不收集账号密码或验证码。只填写账号标识或主页链接，后续按正式订单约定的方式完成交付。</p></details><details><summary>GPT 日区充值的套餐和价格是什么？</summary><p>展示价为 ¥<?= $gptPrice ?>。套餐周期与账号条件以商品说明为准，后台未上架时不开放付款。</p></details></section>
    <aside class="help-strip"><span class="help-symbol">✧</span><div><strong>购买之后，随时找回。</strong><p>使用下单邮箱或订单号，查询订单与交付信息。</p></div><a href="<?= $escape(url('order-search')) ?>">查询我的订单 <span>↗</span></a></aside>
    <footer class="site-footer"><div><span class="footer-brand"><?= $escape($siteName) ?></span><span>让数字生活，多一点美好。</span></div><div class="configured-footer"><?= dujiaoka_config_get('footer', '') ?></div><small>Powered by <a href="https://github.com/assimon/dujiaoka" rel="noopener noreferrer" target="_blank">独角数卡</a></small></footer>
</main>
<nav class="mobile-nav" aria-label="移动导航"><a href="/" class="active" aria-current="page"><span>▦</span>逛商店</a><a href="<?= $escape(url('order-search')) ?>"><span>▤</span>查订单</a><button type="button" data-notice><span>♧</span>看公告</button><a href="#products"><span>⌕</span>找好物</a></nav>
<dialog id="notice-dialog" aria-labelledby="notice-title"><div class="dialog-header"><div><p class="eyebrow">STORE NOTICE</p><h2 id="notice-title">站点公告</h2></div><button class="icon-button" type="button" data-close-notice aria-label="关闭公告">×</button></div><div class="notice-content"><?= dujiaoka_config_get('notice') ?: '暂无新公告，欢迎来到我们的小铺。' ?></div></dialog>
</body>
</html>
