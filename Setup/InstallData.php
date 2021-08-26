<?php

namespace OpenControl\Integration\Setup;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Sales\Model\Order\Status;
use Psr\Log\LoggerInterface;

class InstallData implements InstallDataInterface
{
    const OPENCONTROL_STATUS = [
        [
            'name'=> 'approved_opencontrol',
            'label'=> 'Aprobado por OpenControl'
            
        ],
        [
            'name'=> 'denied_opencontrol',
            'label'=> 'Denegado por OpenControl'
        ],
    ];
    
    protected $logger;
    
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }
    
    
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) { 
        $this->logger->debug('#InstallData');
        $setup->startSetup();
        try {
            $objectManager = ObjectManager::getInstance();
            foreach (self::OPENCONTROL_STATUS as $status) {
                $newStatus = $objectManager->create(Status::class);
                $newStatus->setStatus($status['name']);
                $newStatus->setLabel($status['label']);
                $newStatus->save();
            }
        } catch(AlreadyExistsException $e) {
            return;
        }
    }
}