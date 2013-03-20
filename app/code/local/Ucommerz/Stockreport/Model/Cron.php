<?php
class Ucommerz_Stockreport_Model_Cron
{
    public function emailreport()
    {
        Mage::getModel('ucommerz_stockreport/report')->sendReport();
    }
}