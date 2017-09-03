<?php
/**
 * @author Miranda Johnson
 * @package MirandaJohnson\ProductsInRange
 */
namespace MirandaJohnson\ProductsInRange\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Grid extends Action
{
    /** @var JsonFactory */
    protected $resultJsonFactory;

    /** @var float */
    protected $minPrice;

    /** @var float */
    protected $maxPrice;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory
        )
    {
        $this->resultJsonFactory = $resultJsonFactory;
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

      return $resultJson->setData($this->getRequest()->getPost());
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
}
