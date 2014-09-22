<?php

class Ucommerz_Stockreport_Model_Adminhtml_System_Config_Backend_Lsenmodel_Cron extends Mage_Core_Model_Config_Data
{
    const CRON_STRING_PATH = 'crontab/jobs/ucommerz_stockreport_email/schedule/cron_expr';
 
    protected function _afterSave()
    {
        $cronExprString = $this->getData('groups/ucommerz_stockreport_settings/fields/report_schedule/value');
 
        try {
            Mage::getModel('core/config_data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
        }
        catch (Exception $e) {
            throw new Exception(Mage::helper('cron')->__('Unable to save the cron expression.'));
 
        }
    }
}
