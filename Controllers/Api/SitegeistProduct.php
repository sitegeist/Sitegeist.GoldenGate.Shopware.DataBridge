<?php declare(strict_types=1);

use SitegeistGoldenGateShopwareDataBridge\Components\Api\Resource\SitegeistProduct;

/**
 * Class Shopware_Controllers_Api_SitegeistProduct
 */
class Shopware_Controllers_Api_SitegeistProduct extends Shopware_Controllers_Api_Rest
{
    /** @var SitegeistProduct $productResource */
    private $productResource;

    /**
     * Shopware_Controllers_Api_SitegeistProduct constructor.
     * @param Enlight_Controller_Request_Request $request
     * @param Enlight_Controller_Response_Response $response
     * @throws Exception
     */
    public function __construct(
        Enlight_Controller_Request_Request $request,
        Enlight_Controller_Response_Response $response
    ) {
        parent::__construct($request, $response);

        /** @var int $shopId */
        $shopId = $this->Request()->getParam('shopId') ? $this->Request()->getParam('shopId') : 1;
        Shopware()->Container()->get('sitegeist_golden_gate_shopware_data_bridge.components.shop_handler')->createShopContext($shopId);

        $this->productResource = Shopware()->Container()->get('sitegeist_golden_gate_shopware_data_bridge.components.api.resource.sitegeist_product');
    }

    public function postDispatch()
    {
        /** @var string $data */
        $data = $this->View()->getAssign('data');

        $this->Response()->setHeader('Content-type', 'application/json', true);
        $this->Response()->setBody($data);
    }

    /**
     * GET Request on /api/SitegeistProduct
     */
    public function indexAction()
    {
        $filter = $this->request->getParam('filter');

        $result = $this->productResource->loadProductReferences($filter);

        $this->View()->assign('data', $result);
    }

    /**
     * GET Request on /api/SitegeistProduct/{id}
     */
    public function getAction()
    {
        $number = $this->Request()->getParam('id');

        $result = $this->productResource->loadProduct($number);

        $this->View()->assign('data', $result);
    }
}