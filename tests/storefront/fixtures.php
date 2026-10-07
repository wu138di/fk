<?php
// Local UI fixtures only. Never loaded by an application route.
function dujiaoka_config_get($key, $default = null) {
    $settings = [
        'text_logo' => 'Wu 的数字小铺', 'title' => 'Wu 的数字小铺',
        'description' => '数字生活，轻松一点。',
        'notice' => '<p>欢迎来到 Wu 的数字小铺。</p><p>这里展示的是设计演示商品，未连接线上数据库或支付。正式商品说明、价格与交付规则以后台配置为准。</p>',
        'footer' => '',
    ];
    return $settings[$key] ?? $default;
}
function url($path = '') { return '/' . ltrim($path, '/'); }
function picture_ulr($path) { return '/' . ltrim($path, '/'); }
function storefront_fixtures() {
    $names = [
        ['效率工具', ['Notion 工作空间 · 效率进阶', 'Microsoft 365 · 个人订阅', '创意设计素材包 · 年度精选']],
        ['创作灵感', ['创意工具箱 · 开启新灵感', '灵感手账 · 数字模板合集', '视频剪辑素材 · 创作者精选']],
        ['学习成长', ['语言学习计划 · 每日进步', '编程学习资源 · 入门合集']],
    ];
    $prices = [29.90, 39.00, 12.00, 19.90, 9.90, 16.00, 25.00, 8.80];
    $groups = []; $id = 0;
    foreach ($names as $index => $group) {
        $goods = [];
        foreach ($group[1] as $name) {
            $goods[] = ['id' => ++$id, 'gd_name' => $name, 'actual_price' => $prices[$id - 1],
                'in_stock' => $id === 6 ? 0 : 12, 'type' => $id === 7 ? 2 : 1, 'picture' => ''];
        }
        $groups[] = ['id' => $index + 1, 'gp_name' => $group[0], 'goods' => $goods];
    }
    return $groups;
}
