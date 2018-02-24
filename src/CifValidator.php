<?php

namespace DocumentValidator;

class CifValidator extends Validator
{
    const CIF_REGEX = '/^[PQSNWR][0-9][0-9][0-9][0-9][0-9][0-9][0-9][A-Z0-9]|^[ABCDEFGHJUV][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/';

    public function isValid($number)
    {
        $fixedDocNumber = strtoupper($number);
        $writtenDigit = substr($fixedDocNumber, -1, 1);

        if ($this->isValidCIFFormat($fixedDocNumber) == 1) {
            $correctDigit = $this->getCIFCheckDigit($fixedDocNumber);

            if ($writtenDigit == $correctDigit) {
                return true;
            }
        }

        return false;
    }

    private function isValidCIFFormat($docNumber)
    {
        return $this->respectsDocPattern($docNumber, self::CIF_REGEX);
    }

    private function getCIFCheckDigit($docNumber)
    {
        $fixedDocNumber = strtoupper($docNumber);
        $totalSum = $this->getCifDigitsSum($fixedDocNumber);
        $lastDigitTotalSum = substr($totalSum, -1);

        if ($lastDigitTotalSum > 0) {
            $correctDigit = 10 - ($lastDigitTotalSum % 10);
        } else {
            $correctDigit = 0;
        }

        /* If CIF number starts with P, Q, S, N, W or R,
            check digit sould be a letter */
        if (preg_match('/^[PQSNWR].*/', $fixedDocNumber)) {
            $correctDigit = substr("JABCDEFGHI", $correctDigit, 1);
        }

        return $correctDigit;
    }

    private function getCifDigitsSum($fixedDocNumber)
    {
        $digits = substr($fixedDocNumber, 1, 7);
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
