<?php
/**
 * Ucommerz Export Sales to Capacity
 *
 * @category    Ucommerz
 * @package     Ucommerz_Capacity
 * @author      Chris Rose <chris@ucommerz.com>
 */
class Ucommerz_Stockreport_Model_Report extends Mage_Core_Model_Abstract
{

    private $_enabled = null;
    private $_from_email = null;
    private $_to_email = null;
    private $_template = null;
    private $_threshold = null;

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
		
	} // end

    public function sendReport() {

        $html = "";

        // If disabled don't send the email
        if (!$this->_enabled) return false;

        try {

            $items = Mage::getModel('cataloginventory/stock_item')
                ->getCollection()
                ->addQtyFilter('<=', $this->_threshold);

            if (count($items) == 0) return false;

            $product=null;
            foreach ($items as $item) {

                $product = Mage::getModel('catalog/product')->load($item->getProductId());

                $html .= "<tr><td>".$product->getName()."</td><td>".$product->getSku()."</td><td>".round($item->getQty())."</td></tr>\n";
            }

        }

        catch (Exception $e)
        {
            log($e->getMessage());
            return false;
        }

        return $this->sendEmail($html);

    } // end

    private function sendEmail($html) {

        $result = false;

        try {

            $mail = Mage::getModel('core/email_template');
            $mail->setDesignConfig(array('area' => 'frontend', 'store' => Mage::app()->getStore()->getId()))
                ->sendTransactional(
                    $this->_template,
                    $this->_from_email,
                    $this->_to_email,
                    null,
                    array('items'=>$html));

            $result = $mail->getSentSuccess();

        }
        catch (Exception $e)
        {
            log($e->getMessage());
            return $result;
        }

        return $result;

    }
	
} // end class
