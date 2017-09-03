<?php
/**
 * @author Miranda Johnson
 * @package MirandaJohnson\ProductsInRange
 */
namespace MirandaJohnson\ProductsInRange\Block\Search;

use Magento\Framework\View\Element\Template;

class Grid extends Template
{
  public function getAjaxGridUrl()
  {
    return $this->getUrl('productsinrange/ajax/grid');
  }
}
