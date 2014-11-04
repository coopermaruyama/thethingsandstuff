<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product list
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_List extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';

    /**
     * Product Collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_productCollection;

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $layer = $this->getLayer();
            /* @var $layer Mage_Catalog_Model_Layer */
            if ($this->getShowRootCategory()) {
                $this->setCategoryId(Mage::app()->getStore()->getRootCategoryId());
            }

            // if this is a product view page
            if (Mage::registry('product')) {
                // get collection of categories this product is associated with
                $categories = Mage::registry('product')->getCategoryCollection()
                    ->setPage(1, 1)
                    ->load();
                // if the product is associated with any category
                if ($categories->count()) {
                    // show products from this category
                    $this->setCategoryId(current($categories->getIterator()));
                }
            }

            $origCategory = null;
            if ($this->getCategoryId()) {
                $category = Mage::getModel('catalog/category')->load($this->getCategoryId());
                if ($category->getId()) {
                    $origCategory = $layer->getCurrentCategory();
                    $layer->setCurrentCategory($category);
                }
            }
            $this->_productCollection = $layer->getProductCollection();

            $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

            if ($origCategory) {
                $layer->setCurrentCategory($origCategory);
            }
        }

        return $this->_productCollection;
    }

    /**
     * Get catalog layer model
     *
     * @return Mage_Catalog_Model_Layer
     */
    public function getLayer()
    {
        $layer = Mage::registry('current_layer');
        if ($layer) {
            return $layer;
        }
        return Mage::getSingleton('catalog/layer');
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedProductCollection()
    {
        //return $this->_getProductCollection();
		if ((isset($_GET['pt']) && $_GET['pt'] != '' && !isset($_GET['condition'])) || (isset($_GET['pt']) && $_GET['pt'] != '' && isset($_GET['condition']) && $_GET['condition'] == '')) //only price tier is set
		{
			$priceTierArray = preg_split("/,/", urldecode($_GET['pt']) );
			
			$cat_id = Mage::getModel('catalog/layer')->getCurrentCategory()->getId();
			$category = Mage::getModel('catalog/category')->load($cat_id);
			$_productCollection = Mage::getResourceModel('catalog/product_collection')->addCategoryFilter($category);
			$_productCollection->addAttributeToSelect('*')
				->addAttributeToFilter('price_tier', array('in' => $priceTierArray))
				->setOrder('price', 'DESC');
            error_log(var_dump($_productCollection));
		}
		else if ((isset($_GET['condition']) && $_GET['condition'] != '' && !isset($_GET['pt']))  || (isset($_GET['condition']) && $_GET['condition'] != '' && isset($_GET['pt']) && $_GET['pt'] == '')) //only condition is set
		{
			$conditionArray = preg_split( "/,/", urldecode($_GET['condition']) );
			
			$cat_id = Mage::getModel('catalog/layer')->getCurrentCategory()->getId();
			$category = Mage::getModel('catalog/category')->load($cat_id);
			$_productCollection = Mage::getResourceModel('catalog/product_collection')->addCategoryFilter($category);
			$_productCollection->addAttributeToSelect('*')
				->addAttributeToFilter('condition', array('in' => $conditionArray))
				->setOrder('price', 'DESC');
		}
		else if (isset($_GET['pt']) && $_GET['pt'] != '' && isset($_GET['condition']) && $_GET['condition'] != '') //both are set
		{
			$priceTierArray = preg_split("/,/", urldecode($_GET['pt']) );
			$conditionArray = preg_split( "/,/", urldecode($_GET['condition']) );
					
			$cat_id = Mage::getModel('catalog/layer')->getCurrentCategory()->getId();
			$category = Mage::getModel('catalog/category')->load($cat_id);
			$_productCollection = Mage::getResourceModel('catalog/product_collection')->addCategoryFilter($category);
			$_productCollection->addAttributeToSelect('*')
				->addAttributeToFilter('condition', array('in' => $conditionArray))
				->addAttributeToFilter('price_tier', array('in' => $priceTierArray))
				->setOrder('price', 'DESC');
		}
        else if (!isset($_GET['order']) && isset($_GET['newest'])) {
            $cat_id = Mage::getModel('catalog/layer')->getCurrentCategory()->getId();
            $category = Mage::getModel('catalog/category')->load($cat_id);
            $_productCollection = Mage::getResourceModel('catalog/product_collection')->addCategoryFilter($category);
            $_productCollection->addAttributeToSelect('*')
                ->setOrder('created_at', 'DESC');
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($_productCollection); //filter out of stock items
            $pageSize = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 12;
            if (isset($_GET['p'])) {
                $page = intVal($_GET['p']);

                $_productCollection->setPage($page,$pageSize);
            } else {
                $_productCollection->setPage(1,$pageSize);
            }
        }
		else
		{
			$_productCollection=$this->_getProductCollection();
		}
        // if(!isset($_GET['order']) && isset($_GET['newest'])) {
        //     $this->_productCollection->getSelect()->reset( Zend_Db_Select::ORDER );
        //     $this->_productCollection->getSelect()->order('e.entity_id desc');
        // }
        
		return $_productCollection;
		//echo $_productCollection->getSelect();
		 

    }

    /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getChild('toolbar')->getCurrentMode();
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->_getProductCollection();

        // use sortable parameters
        if ($orders = $this->getAvailableOrders()) {
            $toolbar->setAvailableOrders($orders);
        }
        if ($sort = $this->getSortBy()) {
            $toolbar->setDefaultOrder($sort);
        }
        if ($dir = $this->getDefaultDirection()) {
            $toolbar->setDefaultDirection($dir);
        }
        if ($modes = $this->getModes()) {
            $toolbar->setModes($modes);
        }

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
        Mage::dispatchEvent('catalog_block_product_list_collection', array(
            'collection' => $this->_getProductCollection()
        ));

        $this->_getProductCollection()->load();

        return parent::_beforeToHtml();
    }

    /**
     * Retrieve Toolbar block
     *
     * @return Mage_Catalog_Block_Product_List_Toolbar
     */
    public function getToolbarBlock()
    {
        if ($blockName = $this->getToolbarBlockName()) {
            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
        return $block;
    }

    /**
     * Retrieve additional blocks html
     *
     * @return string
     */
    public function getAdditionalHtml()
    {
        return $this->getChildHtml('additional');
    }

    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    public function setCollection($collection)
    {
        $this->_productCollection = $collection;
        return $this;
    }

    public function addAttribute($code)
    {
        $this->_getProductCollection()->addAttributeToSelect($code);
        return $this;
    }

    public function getPriceBlockTemplate()
    {
        return $this->_getData('price_block_template');
    }

    /**
     * Retrieve Catalog Config object
     *
     * @return Mage_Catalog_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('catalog/config');
    }

    /**
     * Prepare Sort By fields from Category Data
     *
     * @param Mage_Catalog_Model_Category $category
     * @return Mage_Catalog_Block_Product_List
     */
    public function prepareSortableFieldsByCategory($category) {
        if (!$this->getAvailableOrders()) {
            $this->setAvailableOrders($category->getAvailableSortByOptions());
        }
        $availableOrders = $this->getAvailableOrders();
        if (!$this->getSortBy()) {
            if ($categorySortBy = $category->getDefaultSortBy()) {
                if (!$availableOrders) {
                    $availableOrders = $this->_getConfig()->getAttributeUsedForSortByArray();
                }
                if (isset($availableOrders[$categorySortBy])) {
                    $this->setSortBy($categorySortBy);
                }
            }
        }

        return $this;
    }
}
