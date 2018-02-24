<?php

namespace DocumentValidator;

class NifValidator extends Validator
{
    const NIF_REGEX = '/^[KLM0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][a-zA-Z0-9]/';
    const NIF_LETTERS = 'TRWAGMYFPDXBNJZSQVHLCKE';

    public function isValid($number)
    {
        $number = $this->normalizeDocumentNumber($number);
        $writtenDigit = strtoupper(substr($number, -1, 1));

        if ($this->isValidNIFFormat($number)) {
            $correctDigit = $this->getNIFCheckDigit($number);
            if ($writtenDigit == $correctDigit) {
                return true;
            }
        }

        return false;
    }

    private function isValidNIFFormat($number)
    {
        return $this->respectsDocPattern($number, self::NIF_REGEX);
    }

    private function getNIFCheckDigit($number)
    {
        $correctLetter = "";

        if ($this->isValidNIFFormat($number)) {
            $number = str_replace('K', '0', $number);
            $number = str_replace('L', '0', $number);
            $number = str_replace('M', '0', $number);
            $position = substr($number, 0, 8) % 23;
            $correctLetter = substr(self::NIF_LETTERS, $position, 1);
        }

        return $correctLetter;
    }
}
