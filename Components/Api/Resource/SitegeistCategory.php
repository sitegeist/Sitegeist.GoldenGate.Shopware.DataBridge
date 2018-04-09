<?php declare(strict_types=1);

namespace SitegeistGoldenGateShopwareDataBridge\Components\Api\Resource;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Shopware\Components\Api\Resource\Resource;
use Shopware\Models\Category\Category;
use Sitegeist\Goldengate\Dto\Serializer\CategoryReferenceSerializer;
use SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\Category\CategoryReferenceHandler;

/**
 * Class SitegeistCategory
 * @package SitegeistGoldenGateShopwareDataBridge\Components\Api\Resource
 */
class SitegeistCategory extends Resource
{
    /** @var CategoryReferenceHandler $categoryReferenceHandler */
    private $categoryReferenceHandler;

    /** @var CategoryReferenceSerializer $categoryReferenceSerializer */
    private $categoryReferenceSerializer;

    /**
     * SitegeistCategory constructor.
     * @param CategoryReferenceHandler $categoryReferenceHandler
     */
    public function __construct(CategoryReferenceHandler $categoryReferenceHandler)
    {
        $this->categoryReferenceHandler = $categoryReferenceHandler;
        $this->categoryReferenceSerializer = new CategoryReferenceSerializer();
    }

    /**
     * @return string
     */
    public function loadCategoryReferences(): string
    {
        /** @var array $swCategories */
        $swCategories = $this->getSwCategories();

        $categoryReferences = $this->categoryReferenceHandler->createFromArray($swCategories);

        // serialize
        $json = $this->categoryReferenceSerializer->serializeArray($categoryReferences);

        return $json;
    }

    /**
     * @return array
     */
    private function getSwCategories(): array
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = Shopware()->Models()->createQueryBuilder();

        $queryBuilder->select('category')
            ->from(Category::class, 'category')
            //->leftJoin(Category::class, 'categoryParent')

            ->where('category.path IS NOT NULL');

        $result = $queryBuilder->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);

        return $result;
    }
}