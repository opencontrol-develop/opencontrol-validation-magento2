<?php

namespace OpenControl\Integration\Model\Request;

use OpenControl\Integration\Model\Request\Entities\Address;
use OpenControl\Integration\Model\Request\Entities\Customer;
use OpenControl\Integration\Model\Request\Entities\Merchant;
use OpenControl\Integration\Model\Request\Entities\Payment;

class RequestValidation extends ModelObject
{
    const SECONDS_TO_MILISECONDS = 1000;
    const ENDPOINT = '/v1/validation';   
    const REQUIRED_FIELDS = [
        'id',
        'session_id',
        'amount',
        'transaction_date',
        'payment',
        'merchant',
    ];
    
    public $id;
    public $session_id;
    public $amount;
    public $currency = "MXN";
    public $transaction_date;
    public $order_id;
    public $source = "API";
    public $reason = "00"; //Validación
    public $validation_type = "COMPLETE";

    /**
     * @var \OpenControl\Integration\Model\Request\Entities\Customer $customer
     */
    public $customer;
    
    /**
     * @var \OpenControl\Integration\Model\Request\Entities\Product[] $products
     */
    public $products;
    
    /**
     * @var \OpenControl\Integration\Model\Request\Entities\Payment $payment
     */
    public $payment;

    /**
     * @var \OpenControl\Integration\Model\Request\Entities\Address $shipping
     */
    public $shipping;

    /**
     * @var \OpenControl\Integration\Model\Request\Entities\Address $billing
     */
    public $billing;
    
    /**
     * @var \OpenControl\Integration\Model\Request\Entities\Merchant $merchant
     */
    public $merchant;

    public function __construct() {
        $this->transaction_date = round(microtime(true) * self::SECONDS_TO_MILISECONDS);
        $this->products = [];
        $this->customer = new Customer();
        $this->payment = new Payment();
        $this->shipping = new Address();
        $this->billing = new Address();
        $this->merchant = new Merchant();
    }

}