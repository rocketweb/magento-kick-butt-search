<?php
class RocketWeb_All_Helper_Data extends Mage_Core_Helper_Data {

    /**
     * Filter rocketweb extensions as array
     *
     * @return array
     */
    public function getExtensions() {

        $modules = Mage::app()->getConfig()->getNode('modules')->asArray();

        foreach ($modules as $name => $props) {
            if (strpos($name, 'RocketWeb_') !== 0) {
                unset($modules[$name]);
            } elseif (!array_key_exists('extension_version', $props)) {
                $modules[$name]['extension_version'] = 'unknown';
            }
        }
        return $modules;
    }
}
