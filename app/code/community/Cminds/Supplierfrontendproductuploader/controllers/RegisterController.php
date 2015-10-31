<?php

class Cminds_Supplierfrontendproductuploader_RegisterController extends Cminds_Supplierfrontendproductuploader_Controller_Action {
    public function preDispatch() {
        parent::preDispatch();

        $this->_getHelper()->validateModule();
        
        if($this->_getHelper()->hasAccess()) {
            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::helper('customer')->getLoginUrl());
        }
    }

    public function indexAction() {
       // echo "rest";exit;
        $this->_renderBlocks();
    }
    
}
