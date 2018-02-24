<?php

namespace DocumentValidator;

class NifValidator extends Validator
{
    const NIF_REGEX = '/^[KLM0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][a-zA-Z0-9]/';

    public function isValid($number)
    {
        $fixedDocNumber = strtoupper(substr("000000000" . $number, -9));
        $writtenDigit = strtoupper(substr($number, -1, 1));

        if ($this->isValidNIFFormat($fixedDocNumber)) {
            $correctDigit = $this->getNIFCheckDigit($fixedDocNumber);
            if ($writtenDigit == $correctDigit) {
                return true;
            }
        }

        return false;
    }

    private function isValidNIFFormat($docNumber)
    {
        return $this->respectsDocPattern($docNumber, self::NIF_REGEX);
    }

    private function getNIFCheckDigit($docNumber)
    {
        $keyString = 'TRWAGMYFPDXBNJZSQVHLCKE';
        $correctLetter = "";

        $fixedDocNumber = strtoupper(substr("000000000" . $docNumber, -9));

        if ($this->isValidNIFFormat($fixedDocNumber)) {
            $fixedDocNumber = str_replace('K', '0', $fixedDocNumber);
            $fixedDocNumber = str_replace('L', '0', $fixedDocNumber);
            $fixedDocNumber = str_replace('M', '0', $fixedDocNumber);
            $position = substr($fixedDocNumber, 0, 8) % 23;
            $correctLetter = substr($keyString, $position, 1);
        }

        return $correctLetter;
    }
}
