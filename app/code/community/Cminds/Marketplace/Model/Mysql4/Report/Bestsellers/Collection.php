<?php
class Cminds_Marketplace_Model_Mysql4_Report_Bestsellers_Collection extends Mage_Sales_Model_Resource_Report_Bestsellers_Collection {
    protected function _applyStoresFilterToSelect(Zend_Db_Select $select)
    {
        $nullCheck = false;
        $storeIds  = $this->_storesIds;

        if (!is_array($storeIds)) {
            $storeIds = array($storeIds);
        }

        $storeIds = array_unique($storeIds);

        if ($index = array_search(null, $storeIds)) {
            unset($storeIds[$index]);
            $nullCheck = true;
        }

        $storeIds[0] = ($storeIds[0] == '') ? 0 : $storeIds[0];

        if ($nullCheck) {
            $select->where('e.store_id IN(?) OR e.store_id IS NULL', $storeIds);
        } else {
            $select->where('e.store_id IN(?)', $storeIds);
        }

        return $this;
    }
}
