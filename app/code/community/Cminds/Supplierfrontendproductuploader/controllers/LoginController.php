<?php

class Cminds_Supplierfrontendproductuploader_LoginController extends Cminds_Supplierfrontendproductuploader_Controller_Action {
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
 
    public function loginAction() {
        
        if(!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $session = Mage::getSingleton('customer/session');

            if ($this->getRequest()->isPost()) {
                $login = $this->getRequest()->getPost();
                if (!empty($login['email']) && !empty($login['password'])) {
                    try {
                        $session->login($login['email'], $login['password']);
                        if ($session->getCustomer()->getIsJustConfirmed()) {
                            $this->_redirect(Mage::getBaseUrl() . 'supplierfrontendproductuploader/');
                        }
                        $this->_redirect('*');
                    } catch (Mage_Core_Exception $e) {
                        $session->addError($e->getMessage());
                        $session->setUsername($login['email']);
                        $this->_redirect('*');
                    } catch (Exception $e) {
                        $this->_redirect('*');
                    }
                } else {
                    $session->addError($this->__('Login and password are required.'));
                    $this->_redirect('*');
                }
            }
        } else {
            $this->_redirect(Mage::getBaseUrl() . 'supplierfrontendproductuploader/');
        }
    }    
}
