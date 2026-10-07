<?php
// Curated presentation only. Existing database ids/prices/stock remain authoritative.
$catalogGroups = [];
$catalogProducts = [];
$hasJapanGpt = false;
foreach ($data ?? [] as $group) {
    $visibleGoods = [];
    foreach ($group['goods'] ?? [] as $goods) {
        $label = ($goods['gd_name'] ?? '') . ' ' . ($group['gp_name'] ?? '');
        $isFanbox = stripos($label, 'fanbox') !== false;
        $isJapanGpt = preg_match('/gpt/i', $label) && preg_match('/日区|日本|japan|\bjp\b/i', $label);
        if (!$isFanbox && !$isJapanGpt) continue;
        $hasJapanGpt = $hasJapanGpt || $isJapanGpt;
        $goods['_group'] = (string) $group['id'];
        $goods['_category'] = $isFanbox ? 'FANBOX' : 'GPT · 日区';
        $goods['_fanbox'] = $isFanbox;
        $goods['_pending'] = false;
        $goods['_cover'] = !empty($goods['picture']) ? picture_ulr($goods['picture']) : '';
        if ($isFanbox && !$goods['_cover'] && preg_match('/\b(980|480)\b/', $goods['gd_name'], $plan)) {
            $goods['_cover'] = '/assets/storefront/fanbox-' . $plan[1] . '.png';
        }
        $catalogProducts[] = $goods;
        $visibleGoods[] = $goods;
    }
    if ($visibleGoods) {
        $group['goods'] = $visibleGoods;
        $catalogGroups[] = $group;
    }
}
// A requested new service can be shown as a draft, never as a purchasable fake SKU.
if (!$hasJapanGpt) {
    $draft = ['id' => 0, 'gd_name' => 'GPT 日区充值', 'actual_price' => 135,
        'in_stock' => 0, 'type' => 2, '_group' => 'gpt-jp-draft', '_category' => 'GPT · 日区',
        '_fanbox' => false, '_pending' => true, '_cover' => ''];
    $catalogProducts[] = $draft;
    $catalogGroups[] = ['id' => 'gpt-jp-draft', 'gp_name' => 'GPT 日区', 'goods' => [$draft]];
}
return ['groups' => $catalogGroups, 'products' => $catalogProducts];
