<?php
class Cminds_Supplierfrontendproductuploader_Block_Adminhtml_Catalog_Product_Grid_Renderer_Approve extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return $this->_getValue($row);
    }
    protected function _getValue(Varien_Object $row)
    {
        $str = '<a href="' .  $this->getUrl('*/*/approve', array('id' => $row->getEntityId())) . '">Approve</a>';
        $p = Mage::getModel('catalog/product')->load($row->getEntityId());
        
        if($p->getData('frontendproduct_product_status') == 1) {
            $str = '<a href="' .  $this->getUrl('*/*/disapprove', array('id' => $row->getEntityId())) . '">Disapprove</a>';
        }

        return $str;
    }
}
