<?php
/**
 * @author Miranda Johnson
 * @package MirandaJohnson\ProductsInRange
 */
namespace MirandaJohnson\ProductsInRange\Controller\Search;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /** @var PageFactory */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory)
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * "Products in Range" search grid page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
      /** @var \Magento\Framework\View\Result\Page $resultPage */
      $resultPage = $this->resultPageFactory->create();
      $resultPage->getConfig()->getTitle()->set(__('Find Products in Range'));

      $block = $resultPage->getLayout()->getBlock('customer.account.link.back');
      if ($block) {
          $block->setRefererUrl($this->_redirect->getRefererUrl());
      }
      return $resultPage;
    }
}
