<?php
$plan = (int) basename($path);
$amount = $plan === 980 ? 58 : 28;
?>
<!doctype html><html lang="zh-CN"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>FANBOX <?= $plan ?> · 方案预览</title><link rel="stylesheet" href="/assets/storefront/storefront.css"></head>
<body><main class="store-shell"><div class="preview-banner">设计预览 · 未连接真实订单或支付</div><p style="margin:24px 0"><a class="text-link" href="/">← 返回商品</a></p><section class="plan-detail"><div class="plan-detail-media"><img src="/assets/storefront/fanbox-<?= $plan ?>.png" alt="FANBOX <?= $plan ?> 原方案配图"></div><div class="plan-detail-content"><p class="eyebrow">FANBOX · <?= $plan ?> PLAN</p><h1>FANBOX <?= $plan ?> 方案</h1><p class="intro-copy">号上直冲 / 文件发送</p><p class="detail-price">¥<?= number_format($amount, 2) ?></p><fieldset><legend>交付方式</legend><label><input type="radio" name="delivery" value="号上直冲" checked> 号上直冲</label><label><input type="radio" name="delivery" value="文件发送"> 文件发送</label></fieldset><p class="delivery-note" id="delivery-note">请填写 FANBOX 账号标识或主页链接；请勿提交密码或验证码。</p><label class="detail-field" for="email">电子邮箱<input id="email" type="email" placeholder="用于接收订单通知或文件" autocomplete="email"></label><label class="detail-field" for="account">FANBOX 账号 / 主页链接<input id="account" type="text" placeholder="号上直冲时填写；请勿填写密码"></label><label class="detail-field" for="quantity">数量<select id="quantity"><?php for ($i=1;$i<=9;$i++): ?><option value="<?= $i ?>"><?= $i ?></option><?php endfor; ?></select></label><div class="preview-checkout"><span>合计 <strong id="detail-total">¥<?= number_format($amount, 2) ?></strong></span><button disabled>预览 · 暂未接入支付</button></div><p class="intro-copy">以上价格来自提供的方案预览。正式库存、交付内容与售后以真实订单说明为准。</p></div></section></main>
<script>
(() => {
    const radios = document.querySelectorAll('[name="delivery"]');
    radios.forEach(radio => radio.addEventListener('change', () => {
        const file = radio.value === '文件发送';
        document.getElementById('delivery-note').textContent = file
            ? '文件将通过订单邮箱交付，请确认邮箱可以正常接收附件。'
            : '请填写 FANBOX 账号标识或主页链接；请勿提交密码或验证码。';
        document.getElementById('account').disabled = file;
    }));
    document.getElementById('quantity').addEventListener('change', event => {
        document.getElementById('detail-total').textContent = '¥' + (<?= $amount ?> * Number(event.target.value)).toFixed(2);
    });
})();
</script></body></html>
