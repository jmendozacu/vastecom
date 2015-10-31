<?php
class EM_Galaelectronuessettings_Adminhtml_Galaelectronuessettings_LinkController extends Mage_Adminhtml_Controller_Action
{

	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	
	public function userguideAction() {
		$this->getResponse()->setRedirect('http://www.galathemes.com/documentation/galaelectronues-userguide/');
	}
	
	public function gotoforumAction() {
		$this->getResponse()->setRedirect('http://forum.galathemes.com/');
	}
	
	public function submitticketAction() {
		$this->getResponse()->setRedirect('http://www.galathemes.com/contacts');
	}
	
	public function service_customizationAction() {
		$this->getResponse()->setRedirect('http://www.galathemes.com/magento-custom-development');
	}

	public function service_designAction() {
		$this->getResponse()->setRedirect('http://www.galathemes.com/magento-theme-design');
	}
	
	public function service_extdevAction() {
		$this->getResponse()->setRedirect('http://www.galathemes.com/magento-custom-development');
	}
	
	public function service_boostAction() {
		$this->getResponse()->setRedirect('http://www.galathemes.com/magento-speed-optimization-services');
	}
	
	public function service_moreAction() {
		$this->getResponse()->setRedirect('http://www.galathemes.com/');
	}
}