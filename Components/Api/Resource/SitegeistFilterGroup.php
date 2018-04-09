<?php declare(strict_types=1);

namespace SitegeistGoldenGateShopwareDataBridge\Components\Api\Resource;

use Doctrine\ORM\AbstractQuery;
use Shopware\Components\Api\Resource\Resource;
use Doctrine\ORM\QueryBuilder;
use Shopware\Models\Property\Option;
use Sitegeist\Goldengate\Dto\Serializer\FilterGroupReferenceSerializer;
use Sitegeist\Goldengate\Dto\Serializer\FilterGroupSerializer;
use Sitegeist\Goldengate\Dto\Structure\FilterGroup;
use Sitegeist\Goldengate\Dto\Structure\FilterGroupReference;
use SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\FilterGroup\FilterGroupHandler;
use SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\FilterGroup\FilterGroupReferenceHandler;

/**
 * Class SitegeistFilterGroup
 * @package SitegeistGoldenGateShopwareDataBridge\Components\Api\Resource
 */
class  SitegeistFilterGroup extends Resource
{
    /** @var FilterGroupReferenceSerializer $filterGroupReferenceSerializer */
    private $filterGroupReferenceSerializer;

    /** @var FilterGroupSerializer $filterGroupSerializer */
    private $filterGroupSerializer;

    /** @var FilterGroupReferenceHandler $filterGroupReferenceHandler */
    private $filterGroupReferenceHandler;

    /** @var FilterGroupHandler $filterGroupHandler */
    private $filterGroupHandler;

    /**
     * SitegeistFilterGroups constructor.
     * @param FilterGroupReferenceHandler $filterGroupReferenceHandler
     * @param FilterGroupHandler $filterGroupHandler
     */
    public function __construct(
        FilterGroupReferenceHandler $filterGroupReferenceHandler,
        FilterGroupHandler $filterGroupHandler
    ) {
        $this->filterGroupReferenceHandler = $filterGroupReferenceHandler;
        $this->filterGroupHandler = $filterGroupHandler;

        $this->filterGroupReferenceSerializer = new FilterGroupReferenceSerializer();
        $this->filterGroupSerializer = new FilterGroupSerializer();
    }

    /**
     * @return string
     */
    public function loadFilterGroupReferences()
    {
        /** @var array $swFilterOptions */
        $swFilterOptions = $this->getSwFilterOptions();

        /** @var FilterGroupReference[] $filterGroupReferences */
        $filterGroupReferences = $this->filterGroupReferenceHandler->createFromArray($swFilterOptions);

        // serialize
        $json = $this->filterGroupReferenceSerializer->serializeArray($filterGroupReferences);

        return $json;
    }

    /**
     * @param $id
     * @return string
     */
    public function loadFilterGroup($id)
    {
        /** @var array $swFilterOption */
        $swFilterOption = $this->getSwFilterOption($id);

        /** @var FilterGroup[] $filterGroup */
        $filterGroups = $this->filterGroupHandler->createFromArray($swFilterOption);

        // serialize
        $json = $this->filterGroupSerializer->serialize($filterGroups);

        return $json;
    }

    /**
     * @return array
     */
    private function getSwFilterOptions()
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = Shopware()->Models()->createQueryBuilder();
        $queryBuilder->select(['filterOption.id', 'filterOption.name'])
            ->from(Option::class, 'filterOption');

        return $queryBuilder->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);
    }

    /**
     * @param $id
     * @return array
     */
    private function getSwFilterOption($id)
    {
        $queryBuilder = Shopware()->Models()->createQueryBuilder();
        $queryBuilder->select(['filterOption', 'filterValues'])
            ->from(Option::class, 'filterOption')
            ->leftJoin('filterOption.values', 'filterValues')
            ->where('filterOption.id = :optionId')
            ->setParameter('optionId', $id);

        return $queryBuilder->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
    }
}