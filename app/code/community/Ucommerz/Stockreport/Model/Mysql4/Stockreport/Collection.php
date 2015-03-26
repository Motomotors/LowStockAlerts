<?php

class Ucommerz_Stockreport_Model_Mysql4_Stockreport_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Initialize collection model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ucommerz_stockreport/stockreport');
    }
}
