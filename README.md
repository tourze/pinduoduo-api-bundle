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

A Symfony bundle for integrating with Pinduoduo (PDD) API, providing comprehensive
e-commerce functionality including product management, order processing, and
logistics integration.

## Features

- **Product Management**: Sync product details, categories, specifications, and measurements
- **Order Processing**: Full order lifecycle management with real-time synchronization
- **Mall Management**: Multi-store support with access token management
- **Logistics Integration**: Shipping templates and logistics coordination
- **Console Commands**: 16 comprehensive CLI commands for automation
- **Cron Job Integration**: Automated background tasks with scheduling
- **Entity Management**: Doctrine ORM entities for all PDD data structures

## Installation

```bash
composer require tourze/pinduoduo-api-bundle
```

## Quick Start

### 1. Basic Configuration

```php
<?php
// config/services.yaml
parameters:
    pinduoduo_api.app_id: '%env(PINDUODUO_APP_ID)%'
    pinduoduo_api.app_secret: '%env(PINDUODUO_APP_SECRET)%'
```

### 2. Usage Example

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
        // Handle response...
    }
}
```

## Console Commands

This bundle provides 16 console commands for various PDD operations:

### Access Token Management
- pdd:refresh-access-token - Refresh access tokens (runs every 5 minutes)
- pdd:refresh-cps-protocol-status - Update CPS protocol status

### Product Management
- pdd:sync-goods-detail - Sync specific product details
- pdd:sync-mall-goods-list - Sync mall product list
- pdd:loop-sync-goods-category - Loop sync product categories
- pdd:get-category-rule - Get category rules
- pdd:sync-spec-list - Sync product specifications
- pdd:sync-measurement-list - Sync measurement units

### Order Management
- pdd:sync-full-order-list - Sync full order list (runs every 6 hours)
- pdd:sync-basic-order-list - Sync basic order information
- pdd:sync-increment-order-list - Sync incremental order updates

### System Data
- pdd:sync-country-list - Sync country/region data (runs every 2 hours)
- pdd:sync-auth-categories - Sync authorized categories
- pdd:sync-logistics-template-list - Sync logistics templates
- pdd:sync-mall-info-list - Sync mall information

### Utilities
- pdd:upload-image - Test image upload functionality

## Advanced Usage

### Custom API Requests

```php
<?php

use PinduoduoApiBundle\Service\PinduoduoClient;
use PinduoduoApiBundle\Repository\MallRepository;

// Direct API call with parameters
$response = $this->pinduoduoClient->requestByMall($mall, 'pdd.goods.list.get', [
    'page' => 1,
    'page_size' => 100
]);
```

### Cron Job Configuration

The bundle includes automatic cron job scheduling:

```bash
# These commands run automatically:
# */5 * * * * - Access token refresh
# 20 */2 * * * - Country sync
# 45 */6 * * * - Order sync
```

## Entity Structure

The bundle provides comprehensive Doctrine entities:

- `Mall` - Store information
- `AuthLog` - Authentication logs
- `Goods\Goods` - Product information
- `Goods\Sku` - Product variants
- `Goods\Category` - Product categories
- `Order\Order` - Order information
- `Country` - Geographic data
- `LogisticsTemplate` - Shipping templates

## Dependencies

This bundle requires:

- PHP 8.1 or higher
- Symfony 6.4 or higher
- Doctrine ORM 3.0 or higher
- tourze/http-client-bundle (internal HTTP client)

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## Security

This bundle handles sensitive API credentials and merchant data. Please ensure:

- Store API credentials securely using environment variables
- Use HTTPS for all API communications
- Implement proper access controls for admin operations
- Regularly update dependencies to address security vulnerabilities
- Follow OWASP guidelines for web application security

If you discover a security vulnerability, please report it via email to security@tourze.com
instead of using the public issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## Support

For issues and questions:
- GitHub Issues: https://github.com/tourze/pinduoduo-api-bundle/issues
- Documentation: https://docs.tourze.com/pinduoduo-api-bundle
