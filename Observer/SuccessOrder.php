<?php

namespace OpenControl\Integration\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Registry;

class SuccessOrder implements ObserverInterface
{
    protected $logger;
    protected $config;
    protected $invoiceService;
    protected $orderService;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \OpenControl\Integration\Model\OpenControlConfigProvider $config,
        \OpenControl\Integration\Model\Utils\InvoiceService $invoiceService,
        \OpenControl\Integration\Model\Utils\OrderService $orderService
    ) {
        $this->logger = $logger;
        $this->config = $config;
        $this->invoiceService = $invoiceService;
        $this->orderService = $orderService;
    }

    public function execute(EventObserver $observer) {
        $isActiveOpenControl = $this->config->getValue('active');
        if (!$isActiveOpenControl) {
            $this->logger->debug('#SuccessOrderOpenControlNotActive');
            return;
        }
        
        $event = $observer->getEvent();
        $order = $event->getData('order');
        if (null === $order) {
            $order = $event->getData('orders')[0];
        }
        $payment = $order->getPayment();

        $quoteId = $order->getQuoteId();
        $orderExists = $this->orderService->existOrderWithQuoteId($quoteId);
        $ccLast4 = $payment->getCcLast4();    
        
        if ($this->invoiceService->isInvoicePaid($order)) {
            $this->deleteOrderExists($order, $orderExists);
            return;
        }

        //Offline Methods
        if ($ccLast4 == null) {
            $this->deleteOrderExists($order, $orderExists);
            return;
        }    
    }

    private function deleteOrderExists($orderSuccess, $orderExists) { 
        $this->logger->debug('orderIds', ['orderExists'=>$orderExists->getEntityId(), 'orderSucces'=>$orderSuccess->getId()]);
        if ($orderExists && $orderSuccess->getId() != $orderExists->getEntityId()) {
            $objectManager = ObjectManager::getInstance();
            $objectManager->get(Registry::class)->register('isSecureArea', true);//This line is to allow orderExists delete
            $this->orderService->copyCommentsHistory($orderExists, $orderSuccess);
            $orderSuccess->save();
            $orderExists->delete();
        }
    }
}