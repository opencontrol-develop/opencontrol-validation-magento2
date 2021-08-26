<?php

namespace OpenControl\Integration\Model\Request;

use OpenControl\Integration\Model\Request\Errors\IsRequiredField;
use ReflectionClass;

abstract class ModelObject 
{
    const REQUIRED_FIELDS = [];

    public function validateObject(){
        $reflect = new ReflectionClass($this);
        $props = $reflect->getProperties();
       
        foreach ($props as $prop) {
            $propertyName = $prop->getName();
            $propertyValue = $prop->getValue($this);
            if(is_object($propertyValue) && $propertyValue instanceof ModelObject){
                $propertyValue->validateObject();
            }
            if (in_array($propertyName, $this::REQUIRED_FIELDS) && $propertyValue === null) {
                throw new IsRequiredField($propertyName, get_class($this));
            }
        }
    }
}