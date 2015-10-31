<?php

class Cminds_Marketplace_Block_Adminhtml_Billing_List_Grid_Renderer_Owning
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $orderId = $row->getData('order_id');
        $supplierId = $row->getData('supplier_id');
        $toPaid = $row->getData('vendor_amount');

        $col = Mage::getModel('marketplace/payments')->getCollection()->addFilter('order_id', $orderId)->addFilter('supplier_id', $supplierId)->getFirstItem();

        return Mage::helper('core')->currency($toPaid - $col->getAmount(), true, false);
    }
}

?>