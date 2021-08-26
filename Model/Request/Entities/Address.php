<?php

namespace OpenControl\Integration\Model\Request\Entities;

use OpenControl\Integration\Model\Request\ModelObject;

class Address extends ModelObject
{
    /**
     * @var $line1 Calle
     */
    public $line1;

    /**
     * @var $line2 Colonia
     */
    public $line2;
    
    /**
     * @var $line3 Delegacion/Municipio
     */
    public $line3;
    public $city;
    public $state;
    public $postal_code;
    public $country = 'MX';
}