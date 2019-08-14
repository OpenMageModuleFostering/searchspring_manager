<?php



$this->startSetup();

$apiHost = Mage::getConfig()->getNode('global/searchspring/api_host');

$config = new Mage_Core_Model_Config();
$uuid = getUUIDv4();
$config->saveConfig('ssmanager/ssmanager_track/uuid', $uuid);

$client = new Zend_Http_Client();
$client->setUri($apiHost . "/api/track/ga-track.json");
$client->setConfig(array('maxredirects'=>0, 'timeout'=>30));
$client->setParameterGet(array(
    'ec' => 'Magento',
    'ea' => 'Install',
    'el' => $_SERVER['HTTP_HOST'],
    'cid' => $uuid
));
$response = $client->request();

$this->endSetup();

function getUUIDv4()
{
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}


