<?php
class Cminds_Marketplace_Helper_Email extends Mage_Core_Helper_Abstract {
    private function _isConfigEnabled($slug) {
        return $this->getConfig($slug);
    }
    private function getConfig($slug) {
        return Mage::getStoreConfig('marketplace_configuration/' . $slug);
    }
    
    public function notifyAdminOnProfileChange() {
        if(!$this->_isConfigEnabled('general/notify_admin_on_profile_changed')) return false;

        $adminEmail = Mage::getStoreConfig('trans_email/ident_general/email');
        $adminName = Mage::getStoreConfig('trans_email/ident_general/name');

        Mage::helper('supplierfrontendproductuploader/email')->_sendEmail($adminName, $adminEmail, 'New profile for confirmation', 'Your profile has beed saved and moved to admin confirmation');

    }

    public function notifyAdminOnUploadedFiles() {
        if(!$this->_isConfigEnabled('general/notify_admin_on_profile_changed')) return false;

        $adminEmail = Mage::getStoreConfig('trans_email/ident_general/email');
        $adminName = Mage::getStoreConfig('trans_email/ident_general/name');

        Mage::helper('supplierfrontendproductuploader/email')->_sendEmail($adminName, $adminEmail, 'New profile for confirmation', 'Your profile has beed saved and moved to admin confirmation');

    }

    public function notifyAdminOnUploadingProducts($customer, $products_count) {
        if(!$this->_isConfigEnabled('csv_import/notify_admin_on_uploading_products')) return false;

        $adminEmail = Mage::getStoreConfig('trans_email/ident_general/email');
        $adminName = Mage::getStoreConfig('trans_email/ident_general/name');
        $supplierName = $customer->getFirstname() .' '. $customer->getLastname();

        Mage::helper('supplierfrontendproductuploader/email')->_sendEmail($adminName, $adminEmail, 'Supplier uploaded products', 'Supplier '.$supplierName.' uploaded ' . $products_count .' new product(s)');
    }

    public function notifySupplier($customer) {
        if(!$this->_isConfigEnabled('notify_supplier_on_profile_approved')) return false;
        
        Mage::helper('supplierfrontendproductuploader/email')->_sendEmail($customer->getFirstname() .' '. $customer->getLastname(), $customer->getEmail(), 'Your supplier profile has been enabled', 'Your Supplier profile has been enabled !');
    }
}
