<?php
namespace Perbility\Console\Configuration;

/**
 * @author Marc Wörlein <marc.woerlein@perbility.de>
 * @package Perbility\Console\Configuration
 */
abstract class PropertyContainer
{
    /** @var mixed[] */
    protected $properties;
    
    /**
     * @param mixed[] $properties
     */
    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }
    
    /**
     * @param string $propertyName
     * @return bool
     */
    protected function has($propertyName)
    {
        return isset($this->properties[$propertyName]);
    }
    
    /**
     * @param string $propertyName
     * @return mixed
     */
    protected function extract($propertyName)
    {
        if (!isset($this->properties[$propertyName])) {
            return null;
        }
        $value = $this->properties[$propertyName];
        unset($this->properties[$propertyName]);
        return $value;
    }
    
    /**
     * @param $propertyName
     * @throws PropertyException
     */
    protected function assertPropertyIsString($propertyName)
    {
        if (!isset($this->properties[$propertyName]) || !is_string($this->properties[$propertyName])) {
            throw new PropertyException("Missing property '$propertyName'");
        }
    }
}
