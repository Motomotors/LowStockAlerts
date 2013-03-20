<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Ucommerz_Stockreport_Adminhtml_DebugController extends Mage_Adminhtml_Controller_Action
{

    public function sendreportAction()
    {
       
        $result=Mage::getModel('ucommerz_stockreport/report')->sendReport();

        if ($result) {
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__("Stock Notification email sent"));
        }
        else{
            Mage::getSingleton('adminhtml/session')->addError($this->__("Error Sending Stock Notification - please check your settings"));
        }
        $this->_redirectReferer();

    }
   
    
}
