<?php

namespace DocumentValidator;

abstract class Validator
{
    abstract public function isValid($number);

    protected function respectsDocPattern($givenString, $pattern)
    {
        $isValid = false;
        $fixedString = strtoupper($givenString);

        if (is_int(substr($fixedString, 0, 1))) {
            $fixedString = substr("000000000" . $givenString, -9);
        }

        if (preg_match($pattern, $fixedString)) {
            $isValid = true;
        }

        return $isValid;
    }
}
