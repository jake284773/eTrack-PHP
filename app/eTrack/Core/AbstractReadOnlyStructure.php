<?php namespace eTrack\Core;

abstract class AbstractReadOnlyStructure {

    public function __get($property)
    {
        $this->throwUnlessPropertyExists($property);

        return $this->{$property};
    }

    public function __isset($property)
    {
        if (property_exists($this, $property)) {
            return true;
        }

        return false;
    }

    public function __set($property, $value)
    {
        throw new \RuntimeException("Property is read-only.");
    }

    private function throwUnlessPropertyExists($property)
    {
        if (property_exists($this, $property) !== true) {
            throw new \RuntimeException();
        }
    }

} 