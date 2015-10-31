<?php
class Cminds_Supplierfrontendproductuploader_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction() {
        $this->_title($this->__('Manage Supplier Products'));
        $this->loadLayout();
        $this->_setActiveMenu('catalog/supplierfrontendproductuploader_catalog_product');
        $block = $this->getLayout()->createBlock('supplierfrontendproductuploader/adminhtml_catalog_product_grid');
        $this->_addContent($block);
        $this->renderLayout();
    }

    public function approveAction() {
        $id = $this->_request->getParam('id');
        
        $s = $this->approve($id);
        
        if($s) {
            Mage::getSingleton('core/session')->addSuccess('Product has been approved.');
        }

        Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("*/*/index"));
    }

    public function massApproveAction() {
        $id = $this->_request->getParam('id');
        $approved = 0;
        foreach($this->_request->getParam('product') AS $product_id) {
            $s = $this->approve($product_id);
            if($s) {
                $approved++;
            }
        }

        Mage::getSingleton('core/session')->addSuccess($approved . ' product(s) has been approved.');
        Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("*/*/index"));
    }

    private function approve($id) {
        try {
            $p = Mage::getModel('catalog/product')->load($id);

            $p->setSupplierActivedProduct(1);   
            $p->getResource()->saveAttribute($p, 'supplier_actived_product'); 
            $p->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
            $p->setFrontendproductProductStatus(Cminds_Supplierfrontendproductuploader_Model_Product::STATUS_APPROVED);
            $p->getResource()->saveAttribute($p, 'frontendproduct_product_status');
            $p->setStockData(array( 
                'is_in_stock' => 1
            ));
            $p->save();

            foreach(Mage::app()->getStores() AS $store) {
                if($store->getId()) {
                    $p->setStoreId($store->getId())->load($id)
                        ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                        ->save();
                }
            }

            $customer = Mage::getModel('customer/customer')->load($p->getData('creator_id'));

            Mage::helper('supplierfrontendproductuploader/email')->productApproved($customer, $p);
        } catch(Exception $e) {
            Mage::log($e->getMessage());
            Mage::getSingleton('core/session')->addError('Product '. $p->getName() .' has not been approved.');
        }
    }

    public function disapproveAction() {
        $id = $this->_request->getParam('id');

        $s = $this->disapprove($id);

        if($s) {
            Mage::getSingleton('core/session')->addSuccess('Products has been disapproved');
        }

        Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("*/*/index"));
    }


    public function massDisapproveAction() {
        $disapproved = 0;
        foreach($this->_request->getParam('product') AS $product_id) {
            $s = $this->disapprove($product_id);
            if($s) {
                $disapproved++;
            }
        }

        Mage::getSingleton('core/session')->addSuccess($disapproved . ' product(s) has been disapproved.');
        Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl("*/*/index"));
    }

    private function disapprove($id) {
        try {
            $p = Mage::getModel('catalog/product')->load($id);

            $p->setSupplierActivedProduct(0); 
            $p->getResource()->saveAttribute($p, 'supplier_actived_product'); 
            
            $p->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
            $p->setFrontendproductProductStatus(Cminds_Supplierfrontendproductuploader_Model_Product::STATUS_DISAPPROVED);
            $p->getResource()->saveAttribute($p, 'frontendproduct_product_status');
            $p->setStockData(array( 
                'is_in_stock' => 0
            )); 
            $p->save();

            foreach(Mage::app()->getStores() AS $store) {
                if($store->getId()) {
                    $p->setStoreId($store->getId())->load($id)
                        ->setVisibility(1)
                        ->save();
                }
            }
        } catch(Exception $e) {
            Mage::log($e->getMessage());
            Mage::getSingleton('core/session')->addError('Product '. $p->getName() .' has not been disapproved.');
        }
    }

    public function exportCsvAction()
    {
        $fileName = 'catalog_supplier_products.csv';
        $grid = $this->getLayout()->createBlock('supplierfrontendproductuploader/adminhtml_catalog_product_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }
    public function exportExcelAction()
    {
        $fileName = 'catalog_supplier_products.xml';
        $grid = $this->getLayout()->createBlock('supplierfrontendproductuploader/adminhtml_catalog_product_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}
