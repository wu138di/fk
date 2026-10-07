# FANBOX / GPT 日区首页

按用户最终范围，仅保留 FANBOX，增加 GPT 日区充值。FANBOX 的文案、两幅原配图与预览价格来自用户提供的 `space-4.html`；GPT 卡片参考 ji8.ai 的柔和配色与层叠数字卡表现，自行实现。

## 集成范围

- `unicorn`、`luna`、`hyper` 的首页统一引用 `common.storefront`，不依赖后台当前选择的主题。
- 原控制器和 GoodsService 提供 `$data`；内容筛选器保留名称或分类包含 FANBOX 的商品，以及包含 GPT 且明确标记日区/日本/Japan/JP 的商品。其他商品只隐藏，不删除后台记录。
- FANBOX 980 / 480 的预览价格为 ¥58 / ¥28，真实页面始终以后台实际价格、库存、商品 ID 为准。保留号上直冲 / 文件发送、购买流程与 FAQ。
- 配图优先使用后台图片；FANBOX 980/480 无后台图片时使用用户原配图。
- GPT 日区未配置时呈现明确的“价格待配置 / 待上架”卡片，无购买链接、不虚构金额或库存；配置真实日区 GPT 后自动替换占位卡。
- 库存为 1–3 显示库存紧张，其他正库存显示有库存，售罄不提供购买链接。
- 保留 `/buy/{id}`、`/order-search`、站点名称、Logo、公告、页脚配置。
- 搜索、分类、库存、排序可组合；支持无结果、无商品、无库存、键盘导航和深浅色切换。
- 不新增购物车、账户、客服、支付接口，不修改数据库、支付、查单、购买页或环境配置。

## 本地验证

PHP 7.4 或更新版本，无需数据库即可验证真实首页模板：

```sh
php tests/storefront/verify.php
php -l resources/views/common/storefront.blade.php
node --check public/assets/storefront/storefront.js
php -S 127.0.0.1:4180 -t public tests/storefront/router.php
```

打开 `http://127.0.0.1:4180`。此预览直接渲染生产共享模板，仅数据和配置为 fixture。FANBOX 详情可测试两种交付选项及数量合计，但支付按钮禁用；查单进入明确的预览说明页。`tests/storefront` 不被任何生产路由引用。

## 部署

先备份文件，将本次变更部署到现有 Laravel 项目，然后在服务器执行 `php artisan view:clear`。不需要迁移数据库、不替换 `.env`。检查真实商品图片、名称和富文本公告；下单/支付/查单仍需在服务器验证。

在后台维护 FANBOX 980 / 480 的价格及库存，按真实商品说明配置交付方式字段。新增 GPT 日区商品时，名称或分类需明确包含 GPT 与日区/日本/Japan/JP；套餐、周期、价格与账号条件由经营者配置，模板不预设服务承诺。

## 回滚

PR 内有最初 UI 提交及最终 FANBOX 范围修正；可分别 `git revert <COMMIT>`，或使用随交付提供的累计 diff 反向恢复到 master 基线。然后运行 `php artisan view:clear`。不涉及订单数据。
