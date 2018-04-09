<?php declare(strict_types=1);

namespace SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\Product;

use Sitegeist\Goldengate\Dto\Structure\Product;

/**
 * Class ProductHandler
 * @package SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\Product
 */
class ProductHandler
{
    /** @var ProductDetailHandler $productDetailHandler */
    private $productDetailHandler;

    /**
     * ProductHandler constructor.
     * @param ProductDetailHandler $productDetailHandler
     */
    public function __construct(ProductDetailHandler $productDetailHandler)
    {
        $this->productDetailHandler = $productDetailHandler;
    }

    /**
     * @param array $item
     * @return Product
     * @throws \Exception
     */
    public function createFromArray(array $item): Product
    {
        /** @var Product $product */
        $product = new Product();
        $product->setId($item['mainDetail']['number']);
        $product->setLabel($item['name']);
        $product->setName($item['name']);
        $product->setDescription($item['descriptionLong']);

        $product->setDetails($this->productDetailHandler->createFromArray(
            $item['details'],
            $item['mainDetailId'],
            $item['images'],
            $item['tax']
        ));

        return $product;
    }
}