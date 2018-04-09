<?php declare(strict_types=1);

namespace SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\Product;

use Sitegeist\Goldengate\Dto\Structure\ProductDetailImageSize;
use SitegeistGoldenGateShopwareDataBridge\Components\ShopHandler;

/**
 * Class ProductDetailImageSizeHandler
 * @package SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\Product
 */
class ProductDetailImageSizeHandler
{
    /** @var ShopHandler $shopHandler */
    private $shopHandler;

    /**
     * ProductDetailImageSizeHandler constructor.
     * @param ShopHandler $shopHandler
     */
    public function __construct(ShopHandler $shopHandler)
    {
        $this->shopHandler = $shopHandler;
    }

    /**
     * @param array $thumbnails
     * @return ProductDetailImageSize[]
     */
    public function createFromArray(array $thumbnails): array
    {
        /** @var ProductDetailImageSize[] $productDetailImageSizes */
        $productDetailImageSizes = [];

        foreach ($thumbnails as $thumbnailKey => $thumbnail) {

            /** @var ProductDetailImageSize $productDetailImageSize */
            $productDetailImageSize = new ProductDetailImageSize();
            $productDetailImageSize->setUrl($this->shopHandler->getBaseUrl() . '/' . $thumbnail);
            $productDetailImageSize->setWidth((int)$thumbnailKey);
            $productDetailImageSize->setHeight((int)$thumbnailKey);
            $productDetailImageSizes[] = $productDetailImageSize;
        }

        return $productDetailImageSizes;
    }
}