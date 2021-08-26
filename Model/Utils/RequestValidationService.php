<?php

namespace OpenControl\Integration\Model\Utils;

use OpenControl\Integration\Model\Request\Entities\Product;
use OpenControl\Integration\Model\Request\RequestValidation;
use \OpenControl\Integration\Model\Request\Entities\Address;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;

class RequestValidationService
{
    protected $generator;
    protected $config;
    protected $checkoutSession;

    public function __construct(
        \OpenControl\Integration\Model\Request\DeviceSessionIdGenerator $generator,
        \OpenControl\Integration\Model\OpenControlConfigProvider $config,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->generator = $generator;
        $this->config = $config;
        $this->checkoutSession = $checkoutSession;
    }
    
    public function create($order) {
        $request = new RequestValidation();
        $this->setGeneralInformation($order, $request);
        $this->setProductsInformation($order, $request);
        $this->setCustomerInformation($order, $request);
        $this->setPaymentInformation($order, $request);
        $this->setShippingInformation($order, $request);
        $this->setBillingInformation($order, $request);
        $this->setMerchantInformation($order, $request);   
        return $request;
    }

    private function setGeneralInformation($order, RequestValidation $request) {
        $request->id = $order->getIncrementId();
        $request->session_id = $this->generator->generateDeviceSessionId();
        $request->amount = $order->getGrandTotal();
        //TODO OrderId
    }

    private function setProductsInformation($order, RequestValidation $request) {
        $productsOrder = $order->getAllItems();
        foreach ($productsOrder as $productOrder) {
            if(!$productOrder->getParentItem()) { //Avoid duplicate in request
                $product = new Product();
                $product->id = $productOrder->getProductId();
                $product->name = $productOrder->getName();
                $product->quantity = $productOrder->getQtyOrdered();
                $product->total_amount = $product->quantity * $productOrder->getPrice();
                $product->type = ($productOrder->getIsVirtual()) ? Product::TYPE_OF_PRODUCTS["DIGITAL"] : Product::TYPE_OF_PRODUCTS["PHYSICAL"];
                $request->products[] = $product;
            }
        }
    }

    private function setCustomerInformation($order, RequestValidation $request) {
        $request->customer->id = ($order->getCustomerIsGuest()) ? 'GUEST' : $order->getCustomerId();
        $request->customer->name = $order->getCustomerFirstname();
        $request->customer->last_name = $order->getCustomerLastname();
        $request->customer->phone = $order->getShippingAddress()->getTelephone();
        $request->customer->emaill = $order->getCustomerEmail();
    }

    private function setPaymentInformation($order, RequestValidation $request){
        $payment = $order->getPayment();
        $request->payment->address = (!$order->getBillingAddress()) ? $this->processAddress($order->getShippingAddress()) : $this->processAddress($order->getBillingAddress()); 
        $request->payment->card->number = $payment->getData('cc_number');
        $request->payment->card->holder_name = $order->getCustomerFirstname() ." ".$order->getCustomerLastname();
    }

    private function setShippingInformation($order, RequestValidation $request) {
        $request->shipping = $this->processAddress($order->getShippingAddress());
    }

    private function setBillingInformation($order,RequestValidation $request) {
        $request->billing = (!$order->getBillingAddress()) ? $this->processAddress($order->getShippingAddress()) : $this->processAddress($order->getBillingAddress()); 
    }

    private function setMerchantInformation($order, RequestValidation $request){
        $request->merchant->id = $this->config->getValue('sandbox_merchant_id');
    }

    private function processAddress($addressToProcess) {
        $address = new Address();
        
        $address->line1 = $addressToProcess->getStreetLine(1);
        $address->line2 = $addressToProcess->getStreetLine(2);
        $address->line3 = $addressToProcess->getStreetLine(3);
        $address->city = $addressToProcess->getCity();
        $address->state = $addressToProcess->getRegion();
        $address->postal_code = $addressToProcess->getPostcode();
        
        return $address;
    }
} 


