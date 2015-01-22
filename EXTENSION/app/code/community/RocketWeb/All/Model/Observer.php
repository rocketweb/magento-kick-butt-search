<?php
class RocketWeb_All_Model_Observer {
	public function updateNotifications($observer) {
		if (Mage::getStoreConfig('rocketweb_all/general/enable_notifications')) {
            try {
                Mage::getModel('rocketweb_all/feed')->checkUpdate();
            } catch (Exception $e) {
                //silently ignore
            }
        }
	}
}
