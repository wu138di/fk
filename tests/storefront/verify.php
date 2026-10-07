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
check(substr_count($html, '<article class="product-card') === 3, 'Only FANBOX 980, FANBOX 480 and requested GPT Japan draft');
check(substr_count($html, 'class="feature-card ') === 3, 'Two artwork cards plus one GPT layered card');
check(strpos($html, 'href="/buy/980"') !== false && strpos($html, 'href="/buy/480"') !== false, 'FANBOX retains original product id routes');
check(strpos($html, 'data-price="58.00"') !== false && strpos($html, 'data-price="28.00"') !== false, 'Source prices 58 and 28 preserved in preview');
check(strpos($html, 'fanbox-980.png') !== false && strpos($html, 'fanbox-480.png') !== false, 'Both original illustrations retained');
check(strpos($html, '号上直冲') !== false && strpos($html, '文件发送') !== false, 'Both FANBOX delivery descriptions retained');
check(strpos($html, '库存紧张') !== false, 'Low stock reflects available inventory');
check(strpos($html, 'href="/order-search"') !== false, 'Original order lookup route retained');
check(strpos($html, 'data-price="135.00"') !== false && strpos($html, 'href="/buy/0"') === false, 'GPT price is 135 and draft has no fake purchase route');
check(strpos($html, '设计预览 ·') === false, 'Production view contains no fixture banner or fixture dependency');
$empty = render_store(null);
check(strpos($empty, 'href="/buy/') === false && strpos($empty, 'data-price="135.00"') !== false, 'Null database contains only a clearly marked non-purchasable GPT draft');
$mixed = storefront_fixtures();
$mixed[] = ['id' => 5, 'gp_name' => '其他商品', 'goods' => [
    ['id' => 30, 'gd_name' => 'Notion 学习资源', 'actual_price' => 9, 'type' => 1, 'in_stock' => 50],
    ['id' => 31, 'gd_name' => 'GPT 菲区充值', 'actual_price' => 10, 'type' => 1, 'in_stock' => 50],
]];
$html = render_store($mixed);
check(strpos($html, 'Notion') === false && strpos($html, '菲区') === false, 'Unrelated products and non-Japan GPT excluded');
check(strpos($html, 'data-category="5"') === false, 'Empty unrelated category excluded');
$japan = storefront_fixtures();
$japan[] = ['id' => 8, 'gp_name' => 'GPT 日区', 'goods' => [
    ['id' => 52, 'gd_name' => 'ChatGPT 日本区充值', 'actual_price' => 123.45, 'type' => 2, 'in_stock' => 4, 'picture' => ''],
]];
$html = render_store($japan);
check(strpos($html, 'href="/buy/52"') !== false && strpos($html, '123.45') !== false, 'Configured Japan GPT uses real backend id, stock and price');
check(strpos($html, 'pending-feature') === false, 'Real Japan GPT replaces draft instead of duplicating it');
$changed = storefront_fixtures(); $changed[0]['goods'][0]['actual_price'] = 65.25;
check(strpos(render_store($changed), 'data-price="65.25"') !== false, 'Production price is not hardcoded to source fixture price');
$sold = storefront_fixtures();
foreach ($sold as &$group) foreach ($group['goods'] as &$goods) $goods['in_stock'] = 0;
unset($group, $goods);
$html = render_store($sold);
check(strpos($html, 'href="/buy/') === false, 'All sold-out catalog has no purchase links');
$xss = storefront_fixtures();
$xss[0]['goods'][0]['gd_name'] = '<img src=x onerror=alert(1)>"';
$html = render_store($xss);
check(strpos($html, '<img src=x') === false && strpos($html, '&lt;img src=x') !== false, 'Product text and attributes escaped');
foreach (['unicorn', 'luna', 'hyper'] as $theme) {
    check(trim(file_get_contents(__DIR__ . "/../../resources/views/$theme/static_pages/home.blade.php")) === "@include('common.storefront')", "$theme uses the same shared production view");
}
echo "OK: $checks checks\n";
