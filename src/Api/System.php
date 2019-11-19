<?php
namespace Netzkollektiv\EasyCredit\Api;

class System implements \Netzkollektiv\EasyCreditApi\SystemInterface {

    public function getSystemVendor() {
        return 'Shopware';
    }

    public function getSystemVersion() {
        return '6.0';
    }

    public function getModuleVersion() {
        $json = file_get_contents(dirname(__FILE__).'/../../composer.json');
        $json = json_decode($json);
        if (isset($json->version)) {
            return $json->version;
        }
    }

    public function getIntegration() {
        return 'PAYMENT_PAGE';
    }
}