<?php declare(strict_types=1);

namespace SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\Product;

use Shopware\Components\Model\ModelManager;
use Shopware\Models\Media\Media;
use Sitegeist\Goldengate\Dto\Structure\ProductDetailImage;
use SitegeistGoldenGateShopwareDataBridge\Components\ShopHandler;

/**
 * Class ProductDetailImageHandler
 * @package SitegeistGoldenGateShopwareDataBridge\Components\Dto\Handler\Product
 */
class ProductDetailImageHandler
{
    /** @var ModelManager $modelManager */
    private $modelManager;

    /** @var  ShopHandler $shopHandler */
    private $shopHandler;

    /** @var ProductDetailImageSize $productDetailImageSizeHandler */
    private $productDetailImageSizeHandler;

    /**
     * ProductDetailImageHandler constructor.
     * @param ModelManager $modelManager
     * @param ShopHandler $shopHandler
     * @param ProductDetailImageSizeHandler $productDetailImageSizeHandler
     */
    public function __construct(
        ModelManager $modelManager,
        ShopHandler $shopHandler,
        ProductDetailImageSizeHandler $productDetailImageSizeHandler)
    {
        $this->modelManager = $modelManager;
        $this->shopHandler = $shopHandler;
        $this->productDetailImageSizeHandler = $productDetailImageSizeHandler;
    }

    /**
     * @param array $images
     * @return ProductDetailImage[]
     */
    public function createFromArray(array $images): array
    {
        /** @var ProductDetailImage[] $productDetailImages */
        $productDetailImages = [];

        foreach ($images as $image) {

            /** @var int $mediaId */
            $mediaId = isset($image['mediaId']) ? $image['mediaId'] : $image['parent']['mediaId'];

            if (is_null($mediaId)) {
                continue;
            }

            /** @var Media $media */
            $media = $this->modelManager->getRepository(Media::class)->find($mediaId);

            $isMain = $image['main'] === 1 ? true : false;

            /** @var ProductDetailImage $productDetailImage */
            $productDetailImage = new ProductDetailImage();
            $productDetailImage->setId((string)$media->getId());
            $productDetailImage->setMain($isMain);
            $productDetailImage->setTitle($media->getName());
            $productDetailImage->setImageSizes(
                $this->productDetailImageSizeHandler->createFromArray($media->getThumbnails())
            );
            $productDetailImages[] = $productDetailImage;
        }

        return $productDetailImages;
    }
}