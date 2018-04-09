<?php declare(strict_types=1);

namespace SitegeistGoldenGateShopwareDataBridge\Components\Api\Resource;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Shopware\Components\Api\Exception\NotFoundException;
use Shopware\Components\Api\Resource\Resource;
use Shopware\Models\Article\Article;
use Sitegeist\Goldengate\Dto\Serializer\FilterSerializer;
use Sitegeist\Goldengate\Dto\Serializer\ProductSerializer;
use Sitegeist\Goldengate\Dto\Serializer\ProductReferenceSerializer;
use Sitegeist\Goldengate\Dto\Structure\CategoryReference;
use Sitegeist\Goldengate\Dto\Structure\Filter;
use Sitegeist\Goldengate\Dto\Structure\FilterGroupOptionReference;
use SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\Product\ProductHandler;
use SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\Product\ProductReferenceHandler;

/**
 * Class SitegeistProduct
 * @package SitegeistGoldenGateShopwareDataBridge\Components\Api\Resource
 */
class  SitegeistProduct extends Resource
{
    /** @var ProductSerializer $productSerializer */
    private $productSerializer;

    /** @var ProductReferenceSerializer $productReferenceSerializer */
    private $productReferenceSerializer;

    /** @var FilterSerializer $filterSerializer */
    private $filterSerializer;

    /** @var ProductReferenceHandler $productReferenceHandler */
    private $productReferenceHandler;

    /** @var ProductHandler $productHandler */
    private $productHandler;

    /**
     * SitegeistProduct constructor.
     * @param ProductReferenceHandler $productReferenceHandler
     * @param ProductHandler $productHandler
     */
    public function __construct(
        ProductReferenceHandler $productReferenceHandler,
        ProductHandler $productHandler)
    {
        $this->productReferenceHandler = $productReferenceHandler;
        $this->productHandler = $productHandler;

        $this->productSerializer = new ProductSerializer();
        $this->productReferenceSerializer = new ProductReferenceSerializer();
        $this->filterSerializer = new FilterSerializer();
    }

    /**
     * @param string $filter
     * @return string
     */
    public function loadProductReferences($filter): string
    {
        $productFilter = ($filter) ? $this->filterSerializer->deserialize($filter) : null;

        /** @var array $swProducts */
        $swProducts = $this->getSwProducts($productFilter);
        $productReferences = $this->productReferenceHandler->createFromArray($swProducts);

        // serialize
        $json = $this->productReferenceSerializer->serializeArray($productReferences);

        return $json;
    }

    /**
     * @param $number
     * @return string
     * @throws NotFoundException
     */
    public function loadProduct($number): string
    {
        /** @var array $swProduct */
        $swProduct = $this->getSwProduct($number);

        if (!$swProduct) {
            throw new NotFoundException(sprintf('Product with id %s not found', $number));
        }
        $product = $this->productHandler->createFromArray($swProduct);

        // serialize
        $json = $this->productSerializer->serialize($product);

        return $json;
    }

    /**
     * @param Filter $productFilter
     * @return array
     */
    private function getSwProducts($productFilter)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = Shopware()->Models()->createQueryBuilder();

        $queryBuilder->select(['product', 'mainDetail', 'propertyValues', 'categories', 'prices'])
            ->from(Article::class, 'product')
            ->leftJoin('product.mainDetail', 'mainDetail')
            ->leftJoin('product.propertyValues', 'propertyValues')
            ->leftJoin('product.categories', 'categories')
            ->leftJoin('mainDetail.prices', 'prices');

        if ($productFilter) {
            $minPrice = $productFilter->getMinPrice();
            $maxPrice = $productFilter->getMaxPrice();
            $categories = $productFilter->getCategoryReferences();
            $optionIds = [];
            $categoryIds = [];

            /** @var FilterGroupOptionReference $optionReference */
            foreach ($productFilter->getFilterGroupOptionReferences() as $optionReference) {
                $optionIds[] = $optionReference->getId();
            }

            if (count($optionIds) > 0) {
                $queryBuilder->andWhere('propertyValues.id IN (:optionIds)')
                    ->setParameter('optionIds', implode(',', $optionIds));
            }

            /** @var CategoryReference $category */
            foreach ($categories as $category) {
                $categoryIds[] = $category->getId();
            }

            if (count($categoryIds) > 0) {
                $queryBuilder->andWhere('categories.id IN (:categoryIds)')
                    ->setParameter('categoryIds', implode(',', $categoryIds));
            }

            if (isset($minPrice) && isset($maxPrice)) {
                $queryBuilder->andWhere('prices.price >= :priceMin')
                    ->andWhere('prices.price <= :priceMax')
                    ->setParameters(['priceMin' => $minPrice/1.19, 'priceMax' => $maxPrice/1.19]);
            }
        }

        $result = $queryBuilder->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY);

        return $result;
    }

    /**
     * @param $number
     * @return mixed
     */
    private function getSwProduct($number)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = Shopware()->Models()->createQueryBuilder();

        $queryBuilder->select([
            'product',
            'mainDetail',
            'details',
            'unit',
            'prices',
            'tax',
            'productImages',
            'detailsImages',
            'detailsImagesParent'
        ])->from(Article::class, 'product')
            ->leftJoin('product.mainDetail', 'mainDetail')
            ->leftJoin('product.details', 'details')
            ->leftJoin('product.tax', 'tax')
            ->leftJoin('details.unit', 'unit')
            ->leftJoin('details.prices', 'prices')
            ->leftJoin('product.images', 'productImages')
            ->leftJoin('productImages.media', 'productImagesMedia')
            ->leftJoin('details.images', 'detailsImages')
            ->leftJoin('detailsImages.parent', 'detailsImagesParent')
            ->leftJoin('detailsImagesParent.media', 'detailsImagesParentMedia')

            ->where('mainDetail.number = :mainNumber')
            ->setParameter('mainNumber', $number);

        return $queryBuilder->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
    }
}