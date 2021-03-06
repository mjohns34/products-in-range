<?php
/**
 * @author Miranda Johnson
 * @package MirandaJohnson\ProductsInRange
 */
namespace MirandaJohnson\ProductsInRange\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Helper\Product;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\DB\Select;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\UrlInterface;

class Data extends AbstractHelper
{
    /** @var CollectionFactory */
    protected $productCollectionFactory;

    /** @var Magento\Catalog\Model\ResourceModel\Product\Collection */
    protected $productCollection;

    /** @var Product */
    protected $productHelper;

    /** @var StoreManagerInterface */
    protected $storeManager;

    /** @var Visibility */
    protected $productVisibility;

    /** @var PricingHelper */
    protected $pricingHelper;

    /** @var float */
    protected $minPrice;

    /** @var float */
    protected $maxPrice;

    /** @var string */
    protected $sortBy;

    /**
     * @param Context $context
     * @param CollectionFactory $productCollectionFactory
     * @param Product $productHelper
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        Product $productHelper,
        StoreManagerInterface $storeManager,
        Visibility $productVisibility,
        PricingHelper $pricingHelper
        )
    {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productHelper = $productHelper;
        $this->storeManager = $storeManager;
        $this->productVisibility = $productVisibility;
        $this->pricingHelper = $pricingHelper;
        $this->sortBy = Select::SQL_ASC;
        parent::__construct($context);
    }

    /**
     * Set minimum & maximum price values for collection filter
     *
     * @param array $values
     * @return $this
     */
    public function setPriceRange(array $values)
    {
        if (isset($values['min_price'])) {
          $this->minPrice = (float) $values['min_price'];
        }
        if (isset($values['max_price'])) {
          $this->maxPrice = (float) $values['max_price'];
        }
        $this->productCollection = null;
        return $this;
    }

    /**
     * Set "sort by" value for product collection
     *
     * @param string $value
     * @return $this
     */
    public function setSortBy(string $value)
    {
      switch ($value) {
        case 'asc':
          $this->sortBy = Select::SQL_ASC;
          break;
        case 'desc':
          $this->sortBy = Select::SQL_DESC;
          break;
      }
      $this->productCollection = null;
      return $this;
    }

    /**
     * Get product collection filtered by price
     *
     * @param boolean $toArray
     * @return Magento\Catalog\Model\ResourceModel\Product\Collection|array
     */
    public function getProductCollection($toArray = true)
    {
      if (is_null($this->productCollection)) {
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addFieldToFilter('price', [
          'from' => $this->minPrice,
          'to' => $this->maxPrice
        ])
        ->addAttributeToSelect('name')
        ->addAttributeToSelect('thumbnail')
        ->setOrder('price', $this->sortBy)
        ->addAttributeToFilter('status', Status::STATUS_ENABLED)
        ->setVisibility($this->productVisibility->getVisibleInSiteIds())
        ->joinField(
            'qty',
            'cataloginventory_stock_item',
            'qty',
            'product_id=entity_id'
        )
        ->setPageSize(10)
        ->setCurPage(1);
        $this->productCollection = $productCollection;
      }
      if ($toArray) {
        return $this->productCollectionToArray();
      } else {
        return $this->productCollection;
      }
    }

    /**
     * Extract data from product collection to array
     *
     * @return array
     */
    protected function productCollectionToArray()
    {
      $result = [];
      foreach ($this->productCollection as $product) {
          $productData = [
              'thumbnail' => $this->storeManager->getStore()
                  ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                  . 'catalog/product' . $product->getThumbnail(),
              'sku' => $product->getSku(),
              'name' => $product->getName(),
              'qty' => intval(($product->getQty()) ? $product->getQty() : 0),
              'price' => $this->pricingHelper->currency(
                $product->getFinalPrice(), true, false
                ),
              'url' => $this->productHelper->getProductUrl($product->getId())
          ];
          $result[] = $productData;
      }
      return $result;
    }
}
