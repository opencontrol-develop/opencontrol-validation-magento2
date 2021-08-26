<?php

namespace OpenControl\Integration\Model\Request\Entities;

use OpenControl\Integration\Model\Request\ModelObject;

class Card extends ModelObject
{
    const REQUIRED_FIELDS = [
        'number'
    ];
    
    public $number;
    public $holder_name;
}