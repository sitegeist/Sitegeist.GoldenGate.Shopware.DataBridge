<?php declare(strict_types=1);

/**
 * Class Shopware_Controllers_Api_SitegeistFilterGroup
 */
class Shopware_Controllers_Api_SitegeistFilterGroup extends Shopware_Controllers_Api_Rest
{
    private $filterGroupsResource;

    /**
     * Shopware_Controllers_Api_SitegeistFilterGroup constructor.
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

        $this->filterGroupsResource = Shopware()->Container()->get('sitegeist_golden_gate_shopware_data_bridge.components.api.resource.sitegeist_filter_group');
    }

    public function postDispatch()
    {
        /** @var string $data */
        $data = $this->View()->getAssign('data');

        $this->Response()->setHeader('Content-type', 'application/json', true);
        $this->Response()->setBody($data);
    }

    /**
     * GET Request on /api/SitegeistFilterGroup
     */
    public function indexAction()
    {
        $result = $this->filterGroupsResource->loadFilterGroupReferences();

        $this->View()->assign('data', $result);
    }

    /**
     * Get one ProductGroup
     *
     * GET Request on /api/SitegeistFilterGroup/{id}
     */
    public function getAction()
    {
        $id = $this->Request()->getParam('id');

        $result = $this->filterGroupsResource->loadFilterGroup($id);

        $this->View()->assign('data', $result);
    }
}