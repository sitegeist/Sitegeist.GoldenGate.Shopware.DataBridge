<?php declare(strict_types=1);

namespace SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\FilterGroup;

use Sitegeist\Goldengate\Dto\Structure\FilterGroup;

/**
 * Class FilterGroupHandler
 * @package SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\FilterGroup
 */
class FilterGroupHandler
{
    /** @var FilterGroupOptionReferenceHandler $filterGroupOptionReferenceHandler */
    private $filterGroupOptionReferenceHandler;

    /**
     * FilterGroupHandler constructor.
     * @param FilterGroupOptionReferenceHandler $filterGroupOptionReferenceHandler
     */
    public function __construct(FilterGroupOptionReferenceHandler $filterGroupOptionReferenceHandler)
    {
        $this->filterGroupOptionReferenceHandler = $filterGroupOptionReferenceHandler;
    }

    /**
     * @param array $group
     * @return FilterGroup
     */
    public function createFromArray(array $group):FilterGroup
    {
        $filterGroup = new FilterGroup();
        $filterGroup->setId((string)$group['id']);
        $filterGroup->setLabel($group['name']);
        $filterGroup->setOptions($this->filterGroupOptionReferenceHandler->createFromArray($group['values']));
        $filterGroups[] = $filterGroup;

        return $filterGroup;
    }
}