# pinduoduo-api-bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](
https://img.shields.io/packagist/v/tourze/pinduoduo-api-bundle.svg?style=flat-square)](
https://packagist.org/packages/tourze/pinduoduo-api-bundle)
[![PHP Version](
https://img.shields.io/packagist/php-v/tourze/pinduoduo-api-bundle.svg?style=flat-square)](
https://packagist.org/packages/tourze/pinduoduo-api-bundle)
[![License](
https://img.shields.io/packagist/l/tourze/pinduoduo-api-bundle.svg?style=flat-square)](
https://packagist.org/packages/tourze/pinduoduo-api-bundle)
[![Build Status](
https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/ci.yml?style=flat-square)](
https://github.com/tourze/php-monorepo/actions)
[![Quality Score](
https://img.shields.io/scrutinizer/g/tourze/php-monorepo.svg?style=flat-square)](
https://scrutinizer-ci.com/g/tourze/php-monorepo)
[![Code Coverage](
https://img.shields.io/scrutinizer/coverage/g/tourze/php-monorepo.svg?style=flat-square)](
https://scrutinizer-ci.com/g/tourze/php-monorepo)
[![Total Downloads](
https://img.shields.io/packagist/dt/tourze/pinduoduo-api-bundle.svg?style=flat-square)](
https://packagist.org/packages/tourze/pinduoduo-api-bundle)

用于集成拼多多（PDD）API 的 Symfony bundle，提供全面的电商功能，
包括商品管理、订单处理和物流集成。

## 功能特性

- **商品管理**：同步商品详情、分类、规格和计量单位
- **订单处理**：完整的订单生命周期管理和实时同步
- **店铺管理**：多店铺支持和访问令牌管理
- **物流集成**：运费模板和物流协调
- **控制台命令**：16 个全面的 CLI 命令用于自动化
- **定时任务集成**：带调度的自动化后台任务
- **实体管理**：所有拼多多数据结构的 Doctrine ORM 实体

## 安装

```bash
composer require tourze/pinduoduo-api-bundle
```

## 快速开始

### 1. 基础配置

```php
<?php
// config/services.yaml
parameters:
    pinduoduo_api.app_id: '%env(PINDUODUO_APP_ID)%'
    pinduoduo_api.app_secret: '%env(PINDUODUO_APP_SECRET)%'
```

### 2. 使用示例

```php
<?php

use PinduoduoApiBundle\Service\PinduoduoClient;
use PinduoduoApiBundle\Repository\MallRepository;

class YourController
{
    public function __construct(
        private PinduoduoClient $pinduoduoClient,
        private MallRepository $mallRepository
    ) {}

    public function syncProducts(): void
    {
        $mall = $this->mallRepository->findOneBy(['name' => 'Your Mall']);
        $response = $this->pinduoduoClient->requestByMall($mall, 'pdd.goods.list.get');
        // 处理响应...
    }
}
```

## 控制台命令

此 bundle 提供 16 个控制台命令用于各种拼多多操作：

### 访问令牌管理
- pdd:refresh-access-token - 刷新访问令牌（每 5 分钟运行）
- pdd:refresh-cps-protocol-status - 更新 CPS 协议状态

### 商品管理
- pdd:sync-goods-detail - 同步指定商品详情
- pdd:sync-mall-goods-list - 同步店铺商品列表
- pdd:loop-sync-goods-category - 循环同步商品分类
- pdd:get-category-rule - 获取分类规则
- pdd:sync-spec-list - 同步商品规格
- pdd:sync-measurement-list - 同步计量单位

### 订单管理
- pdd:sync-full-order-list - 同步全量订单列表（每 6 小时运行）
- pdd:sync-basic-order-list - 同步基础订单信息
- pdd:sync-increment-order-list - 同步增量订单更新

### 系统数据
- pdd:sync-country-list - 同步国家/地区数据（每 2 小时运行）
- pdd:sync-auth-categories - 同步授权分类
- pdd:sync-logistics-template-list - 同步物流模板
- pdd:sync-mall-info-list - 同步店铺信息

### 工具命令
- pdd:upload-image - 测试图片上传功能

## 高级用法

### 自定义 API 请求

```php
<?php

use PinduoduoApiBundle\Service\PinduoduoClient;
use PinduoduoApiBundle\Repository\MallRepository;

// 带参数的直接 API 调用
$response = $this->pinduoduoClient->requestByMall($mall, 'pdd.goods.list.get', [
    'page' => 1,
    'page_size' => 100
]);
```

### 定时任务配置

该 bundle 包含自动定时任务调度：

```bash
# 这些命令会自动运行：
# */5 * * * * - 访问令牌刷新
# 20 */2 * * * - 国家同步
# 45 */6 * * * - 订单同步
```

## 实体结构

该 bundle 提供全面的 Doctrine 实体：

- `Mall` - 店铺信息
- `AuthLog` - 认证日志
- `Goods\Goods` - 商品信息
- `Goods\Sku` - 商品规格
- `Goods\Category` - 商品分类
- `Order\Order` - 订单信息
- `Country` - 地理数据
- `LogisticsTemplate` - 运费模板

## 依赖要求

此包需要：

- PHP 8.1 或更高版本
- Symfony 6.4 或更高版本
- Doctrine ORM 3.0 或更高版本
- tourze/http-client-bundle （内部 HTTP 客户端）

## 贡献

1. Fork 仓库
2. 创建功能分支
3. 进行修改
4. 为新功能添加测试
5. 提交 Pull Request

## 安全

此包处理敏感的 API 凭证和商户数据。请确保：

- 使用环境变量安全存储 API 凭证
- 所有 API 通信使用 HTTPS
- 为管理操作实施适当的访问控制
- 定期更新依赖项以解决安全漏洞
- 遵循 OWASP Web 应用程序安全指南

如果您发现安全漏洞，请通过邮件发送至 security@tourze.com，而不是使用公共问题跟踪器。

## 许可证

MIT 许可证（MIT）。请查看 [License File](LICENSE) 了解更多信息。

## 支持

如有问题和疑问：
- GitHub Issues: https://github.com/tourze/pinduoduo-api-bundle/issues
- 文档: https://docs.tourze.com/pinduoduo-api-bundle
