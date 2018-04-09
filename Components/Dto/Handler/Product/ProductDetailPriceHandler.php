<?php declare(strict_types=1);

namespace SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\Product;

use Shopware\Bundle\StoreFrontBundle\Struct\Tax;
use Sitegeist\Goldengate\Dto\Structure\ProductDetailPrice;
use SitegeistGoldenGateShopwareDataBridge\Components\PriceCalculator;
use SitegeistGoldenGateShopwareDataBridge\Components\ShopHandler;

/**
 * Class ProductDetailPriceHandler
 * @package SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\Product
 */
class ProductDetailPriceHandler
{
    /** @var ShopHandler $shopHandler */
    private $shopHandler;

    /** @var PriceCalculator $priceCalculator */
    private $priceCalculator;

    /**
     * ProductDetailPriceHandler constructor.
     * @param ShopHandler $shopHandler
     * @param PriceCalculator $priceCalculator
     */
    public function __construct(ShopHandler $shopHandler,
        PriceCalculator $priceCalculator)
    {
        $this->shopHandler = $shopHandler;
        $this->priceCalculator = $priceCalculator;
    }

    /**
     * @param ProductDetailPrice[] $prices
     * @param Tax $tax
     * @return array
     */
    public function createFromArray(array $prices, Tax $tax): array
    {
        /** @var ProductDetailPrice[] $productDetailPrices */
        $productDetailPrices = [];

        foreach ($prices as $price) {

            /** @var boolean $isMain */
            $isMain = $price['customerGroupKey'] === 'EK' ? true : false;

            /** @var ProductDetailPrice $productDetailPrice */
            $productDetailPrice = new ProductDetailPrice();
            $productDetailPrice->setId((string)$price['id']);
            $productDetailPrice->setMain($isMain);
            $productDetailPrice->setFrom($price['from']);
            $productDetailPrice->setTo($price['to']);
            $productDetailPrice->setCurrency($this->shopHandler->getShop()->getCurrency()->getCurrency());
            $productDetailPrice->setValue($this->priceCalculator->calculatePrice($price['price'], $tax));
            $productDetailPrice->setOriginalValue($this->priceCalculator->calculatePrice($price['pseudoPrice'], $tax));
            $productDetailPrice->setLabel($price['customerGroupKey']);

            $productDetailPrices[] = $productDetailPrice;
        }

        return $productDetailPrices;
    }
}