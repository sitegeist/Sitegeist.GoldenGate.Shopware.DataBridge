<?php declare(strict_types=1);

namespace SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\Product;

use \Doctrine\DBAL\Query\QueryBuilder;
use Shopware\Bundle\StoreFrontBundle\Struct\Tax;
use Sitegeist\Goldengate\Dto\Structure\ProductDetail;
use Sitegeist\Goldengate\Dto\Structure\ProductDetailImage;
use Sitegeist\Goldengate\Dto\Structure\ProductDetailPrice;
use SitegeistGoldenGateShopwareDataBridge\Components\ShopHandler;

/**
 * Class ProductDetailHandler
 * @package SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\Product
 */
class ProductDetailHandler
{
    /** @var ShopHandler $shopHandler */
    private $shopHandler;

    /** @var ProductDetailPriceHandler $productDetailPriceHandler */
    private $productDetailPriceHandler;

    /** @var ProductDetailImageHandler $productDetailImageHandler */
    private $productDetailImageHandler;

    /**
     * ProductDetailHandler constructor.
     * @param ShopHandler $shopHandler
     * @param ProductDetailPriceHandler $productDetailPriceHandler
     * @param ProductDetailImageHandler $productDetailImageHandler
     */
    public function __construct(
        ShopHandler $shopHandler,
        ProductDetailPriceHandler $productDetailPriceHandler,
        ProductDetailImageHandler $productDetailImageHandler)
    {
        $this->shopHandler = $shopHandler;
        $this->productDetailPriceHandler = $productDetailPriceHandler;
        $this->productDetailImageHandler = $productDetailImageHandler;
    }

    /**
     * @param ProductDetail[] $details
     * @param int $mainDetailId
     * @param array $images
     * @param array $tax
     * @return array
     * @throws \Exception
     */
    public function createFromArray(array $details, int $mainDetailId, array $images, array $tax): array
    {
        /** @var ProductDetail[] $productDetails */
        $productDetails = [];

        foreach ($details as $detail) {

            /** @var boolean $isMain */
            $isMain = $detail['id'] === $mainDetailId ? true : false;

            /** @var string $unit */
            $unit = $detail['referenceUnit'] && $detail['unit']['name']
                ? $detail['referenceUnit'] . ' ' . $detail['unit']['name']
                : '';

            /** @var array $imageArray */
            $imageArray = count($detail['images']) > 0 ? $detail['images'] : $images;

            /** @var ProductDetailImage[] $images */
            $productImages = $this->productDetailImageHandler->createFromArray($imageArray);

            /** @var ProductDetailPrice[] $prices */
            $prices = $this->productDetailPriceHandler->createFromArray(
                $detail['prices'],
                $this->createTaxFromArray($tax)
            );

            /** @var ProductDetail $productDetail */
            $productDetail = new ProductDetail();
            $productDetail->setId((string)$detail['id']);
            $productDetail->setLabel($detail['additionalText']);
            $productDetail->setNumber($detail['number']);
            $productDetail->setMain($isMain);
            $productDetail->setUnit($unit);
            $productDetail->setImages($productImages);
            $productDetail->setPrices($prices);
            $productDetail->setUri($this->getDetailLink($detail['articleId']));
            $productDetails[] = $productDetail;
        }

        return $productDetails;
    }

    /**
     * @param $array
     * @return Tax
     */
    private function createTaxFromArray($array)
    {
        $tax = new Tax();
        $tax->setId($array['id']);
        $tax->setName($array['name']);
        $tax->setTax($array['tax']);

        return $tax;
    }

    /**
     * @param $articleId
     * @return string
     * @throws \Exception
     */
    private function getDetailLink($articleId)
    {
        $articlePath = 'sViewport=detail&sArticle=' . $articleId;

        if ($seoPath = $this->existsSeoUrl($articlePath)) {
            return $this->shopHandler->getBaseUrl() . '/' . $seoPath;
        } else {
            return $this->shopHandler->getBaseUrl() . '/shopware.php?' . $articlePath;
        }
    }

    /**
     * @param $orgPath
     * @return false|string
     * @throws \Exception
     */
    private function existsSeoUrl($orgPath)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = Shopware()->Container()->get('dbal_connection')->createQueryBuilder();
        $queryBuilder->select('seoUrl.path')
            ->from('s_core_rewrite_urls', 'seoUrl')
            ->where('seoUrl.org_path = :orgPath')
            ->setParameter('orgPath', $orgPath)
            ->andWhere('seoUrl.subshopID = :subShopId')
            ->setParameter('subShopId', $this->shopHandler->getShop()->getId())
            ->andWhere('seoUrl.main = :isMain')
            ->setParameter('isMain', 1);

        $result =  $queryBuilder->execute()->fetchColumn(0);

        return $result;
    }
}