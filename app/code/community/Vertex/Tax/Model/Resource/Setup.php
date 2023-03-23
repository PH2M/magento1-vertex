<?php

/**
 * @copyright   Vertex. All rights reserved.    https://www.vertexinc.com/
 * @author      Mediotype                       https://www.mediotype.com/
 */

class Vertex_Tax_Model_Resource_Setup extends Mage_Eav_Model_Entity_Setup
{

    /**
     * @return $this|Mage_Core_Model_Resource_Setup
     */
    public function applyUpdates()
    {
        if (!Mage::isInstalled() && method_exists(Mage::getConfig(), "addAllowedModules")) {
            $modules = Mage::getConfig()->getNode('modules')->children();
            $myModule = substr(__CLASS__, 0, strpos(__CLASS__, '_Model'));
            foreach ($modules as $moduleName => $moduleNode) {
                if ($moduleName != $myModule) {
                    Mage::getConfig()->addAllowedModules($moduleName);
                }
            }

            Mage::getConfig()->reinit();

            return $this;
        }

        return parent::applyUpdates();
    }

    /**
     *
     * @param $entityTypeId
     * @param $attributeId
     *
     * @return boolean
     */
    public function attributeExists($entityTypeId, $attributeId)
    {
        try {
            $entityTypeId = $this->getEntityTypeId($entityTypeId);
            $attributeId = $this->getAttributeId($entityTypeId, $attributeId);
            return !empty($attributeId);
        } catch (Exception $e) {
            return false;
        }
    }
}
