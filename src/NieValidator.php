<?php

namespace DocumentValidator;

class NieValidator extends Validator
{
    const NIE_REGEX = '/^[XYZT][0-9][0-9][0-9][0-9][0-9][0-9][0-9][A-Z0-9]/';

    public function isValid($number)
    {
        $number = $this->normalizeDocumentNumber($number);

        if ($this->isValidNIEFormat($number)) {

            if (substr($number, 1, 1) == "T") {
                return true;
            }

            $number = $this->prepareToNifValidation($number);
            $nifValidator = new NifValidator();

            return $nifValidator->isValid($number);
        }

        return false;
    }

    private function isValidNIEFormat($docNumber)
    {
        return $this->respectsDocPattern($docNumber, self::NIE_REGEX);
    }

    /**
     * The algorithm for validating the check digits of a NIE number is
     * identical to the algorithm for validating NIF numbers. We only have to
     * replace Y, X and Z with 1, 0 and 2 respectively; and then, run
     * the NIF algorithm
     */
    private function prepareToNifValidation($number)
    {
        $numberWithoutLast = substr($number, 0, strlen($number) - 1);
        $lastDigit = substr($number, strlen($number) - 1, strlen($number));
        $numberWithoutLast = str_replace('Y', '1', $numberWithoutLast);
        $numberWithoutLast = str_replace('X', '0', $numberWithoutLast);
        $numberWithoutLast = str_replace('Z', '2', $numberWithoutLast);
        $number = $numberWithoutLast . $lastDigit;

        return $number;
    }

}
