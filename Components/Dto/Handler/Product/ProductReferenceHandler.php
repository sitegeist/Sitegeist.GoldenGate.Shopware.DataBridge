<?php declare(strict_types=1);

namespace SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\Product;

use Sitegeist\Goldengate\Dto\Structure\ProductReference;

/**
 * Class ProductReferenceHandler
 * @package SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\Product
 */
class ProductReferenceHandler
{
    /**
     * @param array $products
     * @return ProductReference[]
     */
    public function createFromArray(array $products): array
    {
        /** @var ProductReference[] $productReferences */
        $productReferences = [];

        /** @var array $product */
        foreach ($products as $product) {

            /** @var  $productReference */
            $productReference = new ProductReference();
            $productReference->setId($product['mainDetail']['number']);
            $productReference->setLabel($product['name']);

            $productReferences[] = $productReference;
        }

        return $productReferences;
    }
}