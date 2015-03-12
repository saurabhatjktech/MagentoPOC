<?php 
class Mage_CatalogRule_Block_Promotion_Promotions extends Mage_CatalogRule_Model_Resource_Rule{
    public function __construct(){
        parent::__construct();
        $storeId = Mage::app()->getStore()->getId();
        $promotions = Mage::getResourceModel('catalogrule/promotion/promotions_collection')
            ->addOrderedQty()
            ->addAttributeToSelect('*')
            ->addAttributeToSelect(array('name'))
            ->setStoreId($storeId)
            ->addStoreFilter($storeId);
        echo "<pre>"; print_r($promotions); echo "</pre>"; die("HEREEEE");

            
        Mage::getSingleton('catalogrule/promotion_status')->addVisibleFilterToCollection($promotions);
        Mage::getSingleton('catalogrule/promotion_visibility')->addVisibleInCatalogFilterToCollection($promotions);
 
        $promotions->setPageSize(3)->setCurPage(1);
        $this->setPromotionCollection($promotions);
    }
}