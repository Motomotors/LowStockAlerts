<?php
/**
 * Ucommerz Low Stock Report
 *
 * @category    Ucommerz
 * @package     Ucommerz_Stockreport
 * @author      Chris Rose <chris@ucommerz.com>
 */
class Ucommerz_Stockreport_Model_Report extends Mage_Core_Model_Abstract
{

    private $_enabled = null;
    private $_from_email = null;
    private $_to_email = null;
    private $_template = null;
    private $_threshold = null;
    private $_exclude_disabled = null;
    private $_exclude_parent = null;
    private $_exclude_last_sent = null;
    private $_existing_skus = null;
    private $_included_skus = null;

	/**
	 * (non-PHPdoc)
	 * @see Mage_Shell_Abstract::_construct()
	 */
	public function _construct() {

        $this->_enabled = Mage::getStoreConfig('ucommerz_stockreport/ucommerz_stockreport_settings/report_enabled');
        $this->_from_email = Mage::getStoreConfig('ucommerz_stockreport/ucommerz_stockreport_settings/report_from_email');
        $this->_to_email = Mage::getStoreConfig('ucommerz_stockreport/ucommerz_stockreport_settings/report_to_email');
        $this->_template = Mage::getStoreConfig('ucommerz_stockreport/ucommerz_stockreport_settings/report_template');
        $this->_threshold = Mage::getStoreConfig('ucommerz_stockreport/ucommerz_stockreport_settings/report_threshold');
        $this->_exclude_disabled = Mage::getStoreConfig('ucommerz_stockreport/ucommerz_stockreport_settings/report_exclude_disabled');
        $this->_exclude_parent = Mage::getStoreConfig('ucommerz_stockreport/ucommerz_stockreport_settings/report_exclude_parent');
        $this->_exclude_last_sent = Mage::getStoreConfig('ucommerz_stockreport/ucommerz_stockreport_settings/report_exclude_last_sent');

	} // end

    public function isEnabled() {
        return $this->_enabled;
    }

    public function sendReport() {

        $html = "";

        try {

            // Get the stock items for everything under the stock threshold
            $items = Mage::getModel('cataloginventory/stock_item')
                ->getCollection();

            // This is not a correct check
            if (count($items) == 0) return "NOTICE|You currently have no Low Stock Items";

            $product=null;
            $countIncludedProducts=0;

            //remove skus that with updated inventory and above the threshold
			Mage::helper('ucommerz_stockreport')->removeSkusWithUpdatedInventory($this->_threshold);

			//get the skus that are already sent to email
			$this->_existing_skus = Mage::helper('ucommerz_stockreport')->getSkuList();
			$this->_included_skus = array();
            foreach ($items as $item) {
                //Check that "Inventory" has "Manage Stock" set to "Yes" and
                //has less or equal stock than the "Notify for Quantity Below"
                //amount.
                if($item->getManageStock()==1 && $item->getStockQty() <= $item->getNotifyStockQty()){
                    // Get the product
                    $product = Mage::getModel('catalog/product')->load($item->getProductId());

                    if ($this->belongsToReport($product)) {
    					$this->_included_skus[] = $product->getSku();
                        $countIncludedProducts++;
                        $html .= "<tr>";
                        $html .= "<td>".$product->getName()."</td>";
                        $html .= "<td>".$product->getSku()."</td>";
                        $html .= "<td>".round($item->getQty())."</td>";
                        $html .= "<td>".($product->getIsInStock()?"Yes":"No")."</td>";
                        $html .= "<td>".($product->getStatus()==1 ? "Enabled":"Disabled")."</td>";
                        $html .= "</tr>\n";
                    }
                }
            }

            // If we don't have any products to send, then don't send the email
            if ($countIncludedProducts == 0) return "NOTICE|You currently have no Low Stock Items";

        }

        catch (Exception $e)
        {
            return "ERROR|There was a problem creating the report".$e->getMessage();
        }

		try
		{
		    if($this->sendEmail($html))
		    {
		    	Mage::helper('ucommerz_stockreport')->writeSkuList($this->_included_skus);
		    	return "NOTICE|Low Stock Report Sent Successfully";
		    }
			else
			{
				return "ERROR|Low Stock Report could not be delivered to all recipients";
			}
		}
		catch (Exception $e)
        {
            return "ERROR|There was a problem sending the report - ".$e->getMessage();
        }

    } // end


    // Use this method to validate items for inclusion in the report
    private function belongsToReport($product) {

    	//exclude if it has been sent already
    	if($this->_exclude_last_sent && in_array($product->getSku(), $this->_existing_skus))
    		return false;

        // if we're excluding the disabled products AND the product is disabled, then disclude the product
        if ($this->_exclude_disabled && ($product->getStatus() == 2)) return false;

        // if this product is a configurable/grouped/bundled product, then disclude this product
        switch ($product->getTypeId()) {
            case 'configurable':
            case 'grouped':
            case 'bundle':
                return $this->_exclude_parent ? false : true;  // Only return false if we've set "exlude parent products" in our settings
            default:
                break;
        }

        // otherwise, include the product
        return true;

    }


    private function sendEmail($html) {

        $result = true;

        try {

            // Get list of recipients
            $recipients = explode(";", $this->_to_email);

            foreach ($recipients as $recipient) {

                // send mail to each recipient
                $mail = Mage::getModel('core/email_template');
                $mail->setDesignConfig(array('area' => 'frontend', 'store' => Mage::app()->getStore()->getId()))
                    ->sendTransactional(
                        $this->_template,
                        $this->_from_email,
                        trim($recipient),
                        null,
                        array('items'=>$html));

                $result = $mail->getSentSuccess() ? $result : false;
            }

        }
        catch (Exception $e)
        {
            // log($e->getMessage());
            throw new Exception("ERROR|There was a problem sending the report - ".$e->getMessage());
        }
        return $result;
    }

} // end class
