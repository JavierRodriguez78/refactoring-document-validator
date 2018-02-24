<?php

namespace DocumentValidator;

class CifValidator extends Validator
{
    const CIF_REGEX = '/^[PQSNWR][0-9][0-9][0-9][0-9][0-9][0-9][0-9][A-Z0-9]|^[ABCDEFGHJUV][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/';

    public function isValid($number)
    {
        $number = $this->normalizeDocumentNumber($number);
        $writtenDigit = substr($number, -1, 1);

        if ($this->isValidCIFFormat($number) == 1) {
            $correctDigit = $this->getCIFCheckDigit($number);

            if ($writtenDigit == $correctDigit) {
                return true;
            }
        }

        return false;
    }

    private function isValidCIFFormat($number)
    {
        return $this->respectsDocPattern($number, self::CIF_REGEX);
    }

    private function getCIFCheckDigit($number)
    {
        $totalSum = $this->getCifDigitsSum($number);
        $lastDigitTotalSum = substr($totalSum, -1);

        if ($lastDigitTotalSum > 0) {
            $correctDigit = 10 - ($lastDigitTotalSum % 10);
        } else {
            $correctDigit = 0;
        }

        /**
         * If CIF number starts with P, Q, S, N, W or R,
         * check digit sould be a letter
         */
        if (preg_match('/^[PQSNWR].*/', $number)) {
            $correctDigit = substr("JABCDEFGHI", $correctDigit, 1);
        }

        return $correctDigit;
    }

    private function getCifDigitsSum($number)
    {
        $digits = substr($number, 1, 7);
        $digitsArray = str_split($digits);

        $oddSum = 0;
        $evenSum = 0;
        for ($i = 0; $i < count($digitsArray); $i++) {
            if ($i % 2 == 0) {
                $oddSum += array_sum(str_split($digitsArray[$i] * 2));
            } else {
                $evenSum += $digitsArray[$i];
            }
        }

        $totalSum = $evenSum + $oddSum;

        return $totalSum;
    }
}
