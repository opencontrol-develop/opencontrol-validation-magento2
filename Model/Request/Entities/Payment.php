<?php

namespace OpenControl\Integration\Model\Request\Entities;

use OpenControl\Integration\Model\Request\ModelObject;

class Payment extends ModelObject
{
    const REQUIRED_FIELDS = [
        'card'
    ];
    
    public $channel = '9'; //Comercio eléctronico

    //TODO Check if can get the promotion

    /**
     * @var \OpenControl\Integration\Model\Request\Entities\Card $card
     */
    public $card;

    /**
     * @var \OpenControl\Integration\Model\Request\Entities\Address $address
     */
    public $address;

    public function __construct() {
        $this->card = new Card();
        $this->address = new Address();
    }
}