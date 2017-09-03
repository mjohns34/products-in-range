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

    /** @var float */
    protected $minPrice;

    /** @var float */
    protected $maxPrice;

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
        StoreManagerInterface $storeManager
        )
    {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productHelper = $productHelper;
        $this->storeManager = $storeManager;
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
        ->setOrder('price', Select::SQL_ASC)
        ->addAttributeToFilter('status', Status::STATUS_ENABLED)
        ->joinField(
            'qty',
            'cataloginventory_stock_item',
            'qty',
            'product_id=entity_id'
        )
        ->setPageSize(10)
        ->setCurPage(1);
      }
      $this->productCollection = $productCollection;

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
              'qty' => $product->getQty(),
              'price' => $product->getPrice(),
              'url' => $this->productHelper->getProductUrl($product->getId())
          ];
          $result[] = $productData;
      }
      return $result;
    }
}
