<?php declare(strict_types=1);

namespace SitegeistGoldenGateShopwareDataBridge\Components;

use Shopware\Bundle\StoreFrontBundle\Service\Core\PriceCalculator as PriceCalculatorService;
use Shopware\Bundle\StoreFrontBundle\Struct\Tax;

/**
 * Class PriceCalculator
 * @package SitegeistGoldenGateShopwareDataBridge\Components
 */
class PriceCalculator
{
    /** @var PriceCalculatorService $priceCalculatorService */
    private $priceCalculatorService;

    /** @var ShopHandler $shopHandler */
    private $shopHandler;

    /**
     * PriceCalculator constructor.
     * @param PriceCalculatorService $priceCalculatorService
     * @param ShopHandler $shopHandler
     */
    public function __construct(PriceCalculatorService $priceCalculatorService,
        ShopHandler $shopHandler)
    {
        $this->priceCalculatorService = $priceCalculatorService;
        $this->shopHandler = $shopHandler;
    }

    /**
     * @param float $price
     * @param Tax $tax
     * @return string
     */
    public function calculatePrice(float $price, Tax $tax): string
    {
        $calculatedPrice = $this->priceCalculatorService->calculatePrice(
            $price,
            $tax,
            $this->shopHandler->getShopContext()
        );

        return number_format($calculatedPrice, 2, ',', '');
    }
}