<?php
class Ucommerz_Stockreport_Model_Cron
{
    public function emailreport()
    {
        $reporter = Mage::getModel('ucommerz_stockreport/report');

        // If disabled don't send the email
        if ($reporter->isEnabled()) Mage::log($reporter->sendReport());;

    }
}