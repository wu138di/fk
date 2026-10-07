<?php
// php -S 127.0.0.1:4180 -t public tests/storefront/router.php
require __DIR__ . '/fixtures.php';
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (strpos($path, '/assets/') === 0) return false;
header('Content-Type: text/html; charset=utf-8');
if ($path === '/') {
    $data = storefront_fixtures(); $previewMode = true;
    require __DIR__ . '/../../resources/views/common/storefront.blade.php';
    return;
}
// Intentional preview boundary: do not pretend checkout or order lookup is live.
if ($path === '/order-search' || preg_match('~^/buy/[0-9]+$~', $path)) {
    echo '<!doctype html><html lang="zh-CN"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>设计预览说明</title><link rel="stylesheet" href="/assets/storefront/storefront.css"><main class="store-shell"><section class="intro"><div><p class="eyebrow">DESIGN PREVIEW</p><h1>这里是设计预览。</h1><p class="intro-copy">商品链接已连接原有路由。当前预览没有线上数据库，不进行下单、查单或支付。</p><p style="margin-top:24px"><a class="order-button" href="/">← 返回商店</a></p></div></section></main></html>';
    return;
}
http_response_code(404); echo 'Preview route not found';
