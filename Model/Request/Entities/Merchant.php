<?php

namespace OpenControl\Integration\Model\Request\Entities;

use OpenControl\Integration\Model\Request\ModelObject;

class Merchant extends ModelObject
{
    const REQUIRED_FIELDS = [
        'id'
    ];
    
    public $id;
    //TODO check if this information the commerce can get;
}