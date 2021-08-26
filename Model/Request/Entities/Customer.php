<?php

namespace OpenControl\Integration\Model\Request\Entities;

use OpenControl\Integration\Model\Request\ModelObject;

class Customer extends ModelObject
{
    const REQUIRED_FIELDS = [
        'id',
        'name',
        'last_name',
        'phone',
        'email',
    ];

    public $id;
    public $name;
    public $last_name;
    public $phone;
    public $emaill;
}