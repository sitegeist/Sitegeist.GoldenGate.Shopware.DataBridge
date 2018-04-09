<?php declare(strict_types=1);

namespace SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\Category;

use Sitegeist\Goldengate\Dto\Structure\CategoryReference;

/**
 * Class CategoryReferenceHandler
 * @package SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\Category
 */
class CategoryReferenceHandler
{
    /**
     * @param array $categories
     * @return array
     */
    public function createFromArray(array $categories): array
    {
        /** @var CategoryReference[] $categoryReferences */
        $categoryReferences = [];

        /** @var array $category */
        foreach ($categories as $category) {

            /** @var  $categoryReference */
            $categoryReference = new CategoryReference();
            $categoryReference->setId((string)$category['id']);
            $categoryReference->setLabel($category['name']);

            $categoryReferences[] = $categoryReference;
        }

        return $categoryReferences;
    }
}