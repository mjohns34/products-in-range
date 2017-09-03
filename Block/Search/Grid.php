<?php
/**
 * @author Miranda Johnson
 * @package MirandaJohnson\ProductsInRange
 */
namespace MirandaJohnson\ProductsInRange\Block\Search;

use Magento\Framework\View\Element\Template;

class Grid extends Template
{
  /**
   * @return $this
   */
  protected function _prepareLayout()
  {
      $this->pageConfig->getTitle()->set(__('Find Products in Range'));
      return parent::_prepareLayout();
  }

  /**
   * Get form submission URL
   *
   * @return string
   */
  public function getAjaxGridUrl()
  {
    return $this->getUrl('productsinrange/ajax/grid');
  }
}
