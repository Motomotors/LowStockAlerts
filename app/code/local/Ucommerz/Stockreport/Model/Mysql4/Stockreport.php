<?php
class Ucommerz_Stockreport_Model_Mysql4_Stockreport extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('ucommerz_stockreport/stockreport', 'record_id');
    }
}
