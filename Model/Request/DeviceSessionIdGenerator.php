<?php

namespace OpenControl\Integration\Model\Request;

class DeviceSessionIdGenerator {

    /**
     * Must complete 32 characters with the prefix
     */
    const PREFIX = "OCTRL-";

    protected $sessionManager;

    public function __construct(
        \Magento\Framework\Session\SessionManager $sessionManager
    )
    {
        $this->sessionManager = $sessionManager;
    }

    public function generateDeviceSessionId(){
        return self::PREFIX.$this->sessionManager->getSessionId();
    }
}