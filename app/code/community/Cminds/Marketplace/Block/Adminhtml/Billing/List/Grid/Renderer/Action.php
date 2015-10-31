<?php

class Cminds_Marketplace_Block_Adminhtml_Billing_List_Grid_Renderer_Action
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $orderId = $row->getData('order_id');
        $supplierId = $row->getData('supplier_id');
        $url = $this->getUrl('*/*/edit', array('order_id' => $orderId, 'supplier_id' => $supplierId));
        return sprintf("<a href='%s'>%s</a>", $url, Mage::helper('marketplace')->__('Edit'));
    }
}

?>