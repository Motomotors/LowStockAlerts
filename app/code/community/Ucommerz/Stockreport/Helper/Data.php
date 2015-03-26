<?php
/*
 empty helper to keep admin from breaking
*/
class Ucommerz_Stockreport_Helper_Data extends Mage_Core_Helper_Abstract
{
	 public function getSkuList()
    {
    	$arr = array();
    	$collection = Mage::getModel('ucommerz_stockreport/stockreport')->getCollection();
    	
    	foreach($collection as $cur_rec)
    	{
    		$arr[] = $cur_rec->getSku();
    	}
    	
    	return $arr;
    }
    
    public function removeSkusWithUpdatedInventory($threshold)
    {
    	$collection = Mage::getModel('ucommerz_stockreport/stockreport')->getCollection();
    	foreach($collection as $cur_rec)
    	{
    		$_product = Mage::getModel('catalog/product')->loadByAttribute('sku',$cur_rec->getSku());
    		$current_stock = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($_product)->getQty();
    		
    		if($current_stock > $threshold)
    			$cur_rec->delete();
    	}
    }
    
    public function writeSkuList($arr)
    {
    	$timestamp = Mage::getModel('core/date')->timestamp(time());
    	foreach($arr as $sku)
		{
			$newRec = Mage::getModel('ucommerz_stockreport/stockreport');
			$newRec->setSku($sku);
			$newRec->setTimestamp(Mage::getModel('core/date')->timestamp(time()));
			$newRec->save();
		}
    }

}
