<?php
require __DIR__ . '/fixtures.php';
$checks = 0;
function check($condition, $message) {
    global $checks;
    if (!$condition) { fwrite(STDERR, "FAIL: $message\n"); exit(1); }
    $checks++; echo "PASS: $message\n";
}
function render_store($data) {
    ob_start(); require __DIR__ . '/../../resources/views/common/storefront.blade.php'; return ob_get_clean();
}
$html = render_store(storefront_fixtures());
check(substr_count($html, '<article class="product-card') === 8, 'All 8 fixture goods rendered from grouped data');
check(substr_count($html, 'class="feature-card ') === 3, 'Three available goods selected, following backend order');
check(strpos($html, 'href="/buy/6"') === false, 'Sold-out good has no purchase link');
check(strpos($html, 'href="/buy/7"') !== false, 'Manual delivery good retains original buy route');
check(strpos($html, '人工处理') !== false, 'Delivery labels reflect actual goods.type');
check(strpos($html, 'href="/order-search"') !== false, 'Original order lookup route retained');
check(strpos($html, 'data-price="29.90"') !== false, 'Two decimal price formatting preserved');
check(strpos($html, 'data-category="3"') !== false, 'Actual category ids retained');
check(strpos($html, '设计预览 ·') === false, 'Production view contains no fixture banner or fixture dependency');
$empty = render_store(null);
check(strpos($empty, '好物正在准备中') !== false && strpos($empty, 'class="feature-card ') === false, 'Null catalog has empty state, no invented goods');
$sold = storefront_fixtures();
foreach ($sold as &$group) foreach ($group['goods'] as &$goods) $goods['in_stock'] = 0;
unset($group, $goods);
$html = render_store($sold);
check(strpos($html, 'href="/buy/') === false && strpos($html, 'class="feature-card ') === false, 'All sold-out catalog has no purchase links or featured cards');
$xss = storefront_fixtures();
$xss[0]['goods'][0]['gd_name'] = '<img src=x onerror=alert(1)>"';
$html = render_store($xss);
check(strpos($html, '<img src=x') === false && strpos($html, '&lt;img src=x') !== false, 'Product text and attributes escaped');
foreach (['unicorn', 'luna', 'hyper'] as $theme) {
    check(trim(file_get_contents(__DIR__ . "/../../resources/views/$theme/static_pages/home.blade.php")) === "@include('common.storefront')", "$theme uses the same shared production view");
}
echo "OK: $checks checks\n";
