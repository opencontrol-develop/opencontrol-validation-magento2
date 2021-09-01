<?php

namespace OpenControl\Integration\Model;

use Magento\Checkout\Model\ConfigProviderInterface;


class OpenControlConfigProvider implements ConfigProviderInterface
{

    const CONFIGURATION_PATH = 'opencontrol_configuration/opencontrol_parameters/';
    const SANDBOX_URL = 'https://sandbox-api.opencontrol.mx';
    const LIVE_URL = 'https://api.opencontrol.mx';
    const DEVICE_SESSION_URL = '/v1/logo.htm?m=%s&s=%s&u=%s&k=%s';

    protected $url;
    protected $scopeConfig;
    protected $logger;
    protected $generator;

    public function __construct(
        \OpenControl\Integration\Model\Request\DeviceSessionIdGenerator $generator,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $logger
        
    ) {
        $this->generator = $generator;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->url = ((bool) $this->getValue('is_sandbox') === true ) ? self::SANDBOX_URL : self::LIVE_URL;
    }
    
    public function getConfig() {
        $sessionId = $this->generator->generateDeviceSessionId();
        $merchantId = $this->getValue('sandbox_merchant_id');
        $licence = $this->getValue('sandbox_license');
        $publicKey = $this->getValue('sandbox_pk');
        
        $formatString = sprintf(self::DEVICE_SESSION_URL, $merchantId, $sessionId, $licence, $publicKey);
        $urlDevice = $this->getURL().$formatString;

        $this->logger->debug('#UrlDeviceSessionId', ['url'=>$urlDevice]);
        
        $isActive = $this->getValue('active');

        $config = [
            'url_device' => $urlDevice,
            'is_active' => $isActive
        ];
        return $config;
    }

    public function getURL(){
        return $this->url;
    }

    public function getValue($value){
        return $this->scopeConfig->getValue(self::CONFIGURATION_PATH.$value);
    }

    public function isMethodIncludedInConfiguration($method) {
        $paymentMethods = explode(',', $this->getValue('payment_methods'));
        $this->logger->debug('#paymentMethodUser', ['method'=>$method]);
        if (!in_array($method, $paymentMethods)) {
            $this->logger->debug('MethodNotIncludedInConfiguration');
            return false;
        }
        return true;
    }
}