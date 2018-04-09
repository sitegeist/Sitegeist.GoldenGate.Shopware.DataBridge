<?php declare(strict_types=1);

use SitegeistGoldenGateShopwareDataBridge\Components\Api\Resource\SitegeistCategory;

/**
 * Class Shopware_Controllers_Api_SitegeistCategory
 */
class Shopware_Controllers_Api_SitegeistCategory extends Shopware_Controllers_Api_Rest
{
    /** @var SitegeistCategory $categoryResource */
    private $categoryResource;

    /**
     * Shopware_Controllers_Api_SitegeistCategory constructor.
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

        $this->categoryResource = Shopware()->Container()->get('sitegeist_golden_gate_shopware_data_bridge.components.api.resource.sitegeist_category');
    }

    public function postDispatch()
    {
        /** @var string $data */
        $data = $this->View()->getAssign('data');

        $this->Response()->setHeader('Content-type', 'application/json', true);
        $this->Response()->setBody($data);
    }

    /**
     * GET Request on /api/SitegeistCategory
     */
    public function indexAction()
    {
        $result = $this->categoryResource->loadCategoryReferences();

        $this->View()->assign('data', $result);
    }

    /**
     * GET Request on /api/SitegeistCategory/{id}
     */
    public function getAction()
    {
        $id = $this->Request()->getParam('id');

        $result = $this->categoryResource->loadCategory($id);

        $this->View()->assign('data', $result);
    }
}