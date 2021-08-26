<?php


namespace OpenControl\Integration\Model\Request\Errors;

use DomainException;
use Error;

class IsRequiredField extends DomainException
{
    public function __construct($fieldName, $className) {
        parent::__construct(sprintf('The %s field of %s is required', $fieldName, $className));
    }
}