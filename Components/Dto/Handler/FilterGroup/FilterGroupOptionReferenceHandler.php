<?php declare(strict_types=1);

namespace SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\FilterGroup;

use Sitegeist\Goldengate\Dto\Structure\FilterGroupOptionReference;

/**
 * Class FilterGroupOptionReferenceHandler
 * @package SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\FilterGroup
 */
class FilterGroupOptionReferenceHandler
{
    /**
     * @param FilterGroupOptionReference[] $options
     * @return array
     */
    public function createFromArray(array $options): array
    {
        /** @var FilterGroupOptionReference[] $filterGroupOptionReferences */
        $filterGroupOptionReferences = [];

        foreach ($options as $option) {

            /** @var FilterGroupOptionReference $filterGroupOptionReference */
            $filterGroupOptionReference = new FilterGroupOptionReference();
            $filterGroupOptionReference->setId((string)$option['id']);
            $filterGroupOptionReference->setLabel($option['value']);
            $filterGroupOptionReferences[] = $filterGroupOptionReference;
        }

        return $filterGroupOptionReferences;
    }
}