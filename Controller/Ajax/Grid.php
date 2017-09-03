<?php
/**
 * @author Miranda Johnson
 * @package MirandaJohnson\ProductsInRange
 */
namespace MirandaJohnson\ProductsInRange\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use MirandaJohnson\ProductsInRange\Helper\Data;

class Grid extends Action
{
    /** @var JsonFactory */
    protected $resultJsonFactory;

    /** @var Data */
    protected $helper;

    /** @var float */
    protected $minPrice;

    /** @var float */
    protected $maxPrice;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Data $helper
        )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * "Products in Range" load grid results
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
      $resultJson = $this->resultJsonFactory->create();
      if (!$this->validateFormData()) {
        return $resultJson->setData([
          'error' => 'Server-side validation failed. Please try again later.'
        ]);
      }
      return $resultJson->setData($this->getProductData());
    }

    /**
     * Validate form values
     *
     * @return boolean
     */
    protected function validateFormData()
    {
      if (!$this->getRequest()->getPost('min_price') ||
          !$this->getRequest()->getPost('max_price')) {
        return false;
      }
      $this->minPrice = (float) $this->getRequest()->getPost('min_price');
      $this->maxPrice = (float) $this->getRequest()->getPost('max_price');
      if ($this->maxPrice < $this->minPrice) {
        return false;
      }
      if ($this->maxPrice > ($this->minPrice * 5)) {
        return false;
      }
      return true;
    }

    /**
     * Get product data for grid
     *
     * @return array
     */
    protected function getProductData()
    {
      return $this->helper->setPriceRange([
        'min_price' => $this->minPrice,
        'max_price' => $this->maxPrice
      ])->setSortBy(
        $this->getRequest()->getPost('sort_by')
      )->getProductCollection();
    }
}
