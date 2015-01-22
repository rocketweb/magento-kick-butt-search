<?php
class RocketWeb_All_Model_Feed extends Mage_AdminNotification_Model_Feed {
	public function getFeedUrl() {
        $url = (Mage::getStoreConfigFlag(self::XML_USE_HTTPS_PATH) ? 'https://':'http://').'www.rocketweb.com/media/rss-notification.xml';
		return $url;
    }
	
	
	public function getLastUpdate()
    {
        return Mage::app()->loadCache('rocketweb_notifications_lastcheck');
    }

    public function setLastUpdate()
    {
        Mage::app()->saveCache(time(), 'rocketweb_notifications_lastcheck');
        return $this;
    }
}
