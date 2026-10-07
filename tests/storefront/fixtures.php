<?php
// Source: user-provided space-4.html. Preview values, never database writes.
function dujiaoka_config_get($key, $default = null) {
    $settings = [
        'text_logo' => 'Wu 的小铺', 'title' => 'Wu 的小铺',
        'description' => 'FANBOX 980 / 480 · 号上直冲 / 文件发送 · GPT 日区充值',
        'notice' => '<p>FANBOX 980 / 480 两档均支持号上直冲或文件发送。</p><p>此页是设计预览，未连接线上订单或支付。GPT 日区充值展示价 ¥135，套餐周期待确认，不收集密码或验证码。</p>',
        'footer' => '',
    ];
    return $settings[$key] ?? $default;
}
function url($path = '') { return '/' . ltrim($path, '/'); }
function picture_ulr($path) { return '/' . ltrim($path, '/'); }
function resource_path($path = '') { return __DIR__ . '/../../resources/' . ltrim($path, '/'); }
function storefront_fixtures() {
    return [['id' => 1, 'gp_name' => 'FANBOX', 'goods' => [
        ['id' => 980, 'gd_name' => 'FANBOX 980 方案', 'actual_price' => 58,
            'in_stock' => 12, 'type' => 2, 'picture' => ''],
        ['id' => 480, 'gd_name' => 'FANBOX 480 方案', 'actual_price' => 28,
            'in_stock' => 2, 'type' => 2, 'picture' => ''],
    ]]];
}
