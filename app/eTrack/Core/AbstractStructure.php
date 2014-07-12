<?php namespace eTrack\Core;

use RuntimeException;

abstract class AbstractStructure {

    public function __set($property, $value)
    {
        $this->throwUnlessPropertyExists($property);
        $this->throwUnlessCorrectType($property, $value);
        $this->throwUnlessCorrectSubclass($property, $value);

        $this->{$property} = $value;
    }

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

    private function throwUnlessPropertyExists($property)
    {
        if (property_exists($this, $property) !== true) {
            throw new RuntimeException();
        }
    }

    private function throwUnlessCorrectType($property, $value)
    {
        $typeUsedToBe = $this->getTypeUsedToBe($property);

        if ($typeUsedToBe !== "null") {
            $typeWantsToBe = $this->getTypeWantsToBe($value);

            if ($typeUsedToBe !== $typeWantsToBe) {
                throw new RuntimeException();
            }
        }
    }

    private function getTypeUsedToBe($property)
    {
        return $this->getTypeOf($this->{$property});
    }

    private function getTypeWantsToBe($value)
    {
        return $this->getTypeOf($value);
    }

    private function getTypeOf($value)
    {
        return strtolower(gettype($value));
    }

    private function getClassUsedToBe($property)
    {
        return get_class($this->{$property});
    }

    private function throwUnlessCorrectSubclass($property, $value)
    {
        $typeUsedToBe = $this->getTypeUsedToBe($property);

        if ($typeUsedToBe === "object") {
            $classUsedToBe = $this->getClassUsedToBe($property);

            if (! is_subclass_of($value, $classUsedToBe)) {
                throw new RuntimeException();
            }
        }
    }

} 