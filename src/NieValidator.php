<?php

namespace DocumentValidator;

class NieValidator extends Validator
{
    const NIE_REGEX = '/^[XYZT][0-9][0-9][0-9][0-9][0-9][0-9][0-9][A-Z0-9]/';

    public function isValid($number)
    {
        $fixedDocNumber = strtoupper(substr("000000000" . $number, -9));

        if ($this->isValidNIEFormat($fixedDocNumber)) {

            if (substr($fixedDocNumber, 1, 1) == "T") {
                return true;
            }
            /* The algorithm for validating the check digits of a NIE number is
                identical to the altorithm for validating NIF numbers. We only have to
                replace Y, X and Z with 1, 0 and 2 respectively; and then, run
                the NIF altorithm */
            $fixedDocNumber = $this->prepareToNifValidation($fixedDocNumber);

            $nifValidator = new NifValidator();

            return $nifValidator->isValid($fixedDocNumber);
        }

        return false;
    }

    private function isValidNIEFormat($docNumber)
    {
        return $this->respectsDocPattern($docNumber, self::NIE_REGEX);
    }

    private function prepareToNifValidation($fixedDocNumber)
    {
        $numberWithoutLast = substr($fixedDocNumber, 0, strlen($fixedDocNumber) - 1);
        $lastDigit = substr($fixedDocNumber, strlen($fixedDocNumber) - 1, strlen($fixedDocNumber));
        $numberWithoutLast = str_replace('Y', '1', $numberWithoutLast);
        $numberWithoutLast = str_replace('X', '0', $numberWithoutLast);
        $numberWithoutLast = str_replace('Z', '2', $numberWithoutLast);
        $fixedDocNumber = $numberWithoutLast . $lastDigit;

        return $fixedDocNumber;
    }

}
