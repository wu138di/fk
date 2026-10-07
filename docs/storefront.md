# 数字小铺首页

参考 ji8.ai 的柔和底色、圆角卡片、层叠数字卡与清晰的价格/库存层级。没有复制其代码、图片、商标或商品数据。

## 集成范围

- `unicorn`、`luna`、`hyper` 的首页统一引用 `common.storefront`，不依赖后台当前选择的主题。
- 原控制器和 GoodsService 提供 `$data`，按后台排序展示分类、价格、商品图片和库存。精选取最前面的三件有货商品，不虚构热销或销量。
- `type=1` 标记自动发货，其他类型标记人工处理。售罄保留展示但没有购买链接。
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

打开 `http://127.0.0.1:4180`。此预览直接渲染生产共享模板，仅数据和配置为 fixture；购买/查单链接进入明确的预览说明页，不冒充真实服务。`tests/storefront` 不被任何生产路由引用。

## 部署

先备份文件，将本次变更部署到现有 Laravel 项目，然后在服务器执行 `php artisan view:clear`。不需要迁移数据库、不替换 `.env`。检查真实商品图片、名称和富文本公告；下单/支付/查单仍需在服务器验证。

## 回滚

对本次 UI 提交使用 `git revert <UI_COMMIT>`，再运行 `php artisan view:clear`。回滚仅涉及三套主题首页、共享视图、独立静态资源和测试/文档，不涉及订单数据。
