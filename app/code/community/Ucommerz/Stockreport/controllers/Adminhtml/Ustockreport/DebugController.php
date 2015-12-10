<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Ucommerz_Stockreport_Adminhtml_Ustockreport_DebugController extends Mage_Adminhtml_Controller_Action
{

    public function sendreportAction()
    {

        $returnVal = Mage::getModel('ucommerz_stockreport/report')->sendReport();
        $result = explode('|',$returnVal);
        $success = ($result[0] == "NOTICE") ? true : false;
        $msg = $result[1];

        if ($success) {
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__($msg));
        }
        else{
            Mage::getSingleton('adminhtml/session')->addError($this->__($msg));
        }
        $this->_redirectReferer();

    }
   
    
}
