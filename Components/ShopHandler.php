<?php declare(strict_types=1);

namespace SitegeistGoldenGateShopwareDataBridge\Components;

use Shopware\Bundle\StoreFrontBundle\Service\Core\ContextService;
use Shopware\Bundle\StoreFrontBundle\Struct\Shop;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContext;

/**
 * Class ShopHandler
 * @package SitegeistGoldenGateShopwareDataBridge\Components
 */
class ShopHandler
{
    /** @var ContextService $contextService */
    private $contextService;

    /** @var ShopContext $shopContext */
    private $shopContext;

    /**
     * ShopHandler constructor.
     * @param ContextService $contextService
     */
    public function __construct(ContextService $contextService)
    {
        $this->contextService = $contextService;
    }

    /**
     * @param int $shopId
     */
    public function createShopContext(int $shopId)
    {
        $this->shopContext = $this->contextService->createShopContext($shopId);
    }

    /**
     * @return ShopContext
     */
    public function getShopContext(): ShopContext
    {
        return $this->shopContext;
    }

    /**
     * @return Shop
     */
    public function getShop(): Shop
    {
        /** @var Shop $shop */
        $shop = $this->shopContext->getShop();

        return $shop;
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        /** @var string $baseUrl */
        $baseUrl = 'http://' . $this->getShop()->getHost();
        if ($this->getShop()->getSecure()) {
            $baseUrl = 'https://' . $this->getShop()->getSecureHost();
        }

        return $baseUrl;
    }
}