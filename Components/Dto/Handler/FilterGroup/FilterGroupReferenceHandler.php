<?php declare(strict_types=1);

namespace SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\FilterGroup;

use Sitegeist\Goldengate\Dto\Structure\FilterGroupReference;

/**
 * Class FilterGroupReferenceHandler
 * @package SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\FilterGroup
 */
class FilterGroupReferenceHandler
{
    /**
     * @param FilterGroupReference[] $groups
     * @return array
     */
    public function createFromArray(array $groups): array
    {
        /** @var FilterGroupReference[] $filterGroupReferences */
        $filterGroupReferences = [];

        foreach ($groups as $group) {
            $filterGroupReference = new FilterGroupReference();
            $filterGroupReference->setId((string)$group['id']);
            $filterGroupReference->setLabel($group['name']);
            $filterGroupReferences[] = $filterGroupReference;
        }

        return $filterGroupReferences;
    }
}