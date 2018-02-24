<?php

namespace DocumentValidator;

use function str_split;

class DocumentValidator
{
    /*
    *   This function validates a Spanish identification number
    *   verifying its check digits.
    *
    *   NIFs and NIEs are personal numbers.
    *   CIFs are corporates.
    *
    *   This function requires:
    *       - isValidCIF and isValidCIFFormat
    *       - isValidNIE and isValidNIEFormat
    *       - isValidNIF and isValidNIFFormat
    *
    *   This function returns:
    *       TRUE: If specified identification number is correct
    *       FALSE: Otherwise
    *
    *   Usage:
    *       echo isValidIdNumber( 'G28667152' );
    *   Returns:
    *       TRUE
    */
    const NIF_REGEX = '/^[KLM0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][a-zA-Z0-9]/';

    const NIE_REGEX = '/^[XYZT][0-9][0-9][0-9][0-9][0-9][0-9][0-9][A-Z0-9]/';

    const CIF_REGEX = '/^[PQSNWR][0-9][0-9][0-9][0-9][0-9][0-9][0-9][A-Z0-9]|^[ABCDEFGHJUV][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/';

    public function isValidIdNumber($docNumber, $type)
    {
        $fixedDocNumber = strtoupper($docNumber);
        $fixedType = strtoupper($type);

        switch ($fixedType) {
            case 'NIF':
                return $this->isValidNIF($fixedDocNumber);
            case 'NIE':
                return $this->isValidNIE($fixedDocNumber);
            case 'CIF':
                return $this->isValidCIF($fixedDocNumber);
            default:
                throw new \Exception('Unsupported Type');
        }
    }

    /*
     *   This function validates a Spanish identification number
     *   verifying its check digits.
     *
     *   This function is intended to work with NIF numbers.
     *
     *   This function is used by:
     *       - isValidIdNumber
     *
     *   This function requires:
     *       - isValidCIFFormat
     *       - getNIFCheckDigit
     *
     *   This function returns:
     *       TRUE: If specified identification number is correct
     *       FALSE: Otherwise
     *
     *   Algorithm works as described in:
     *       http://www.interior.gob.es/dni-8/calculo-del-digito-de-Check-del-nif-nie-2217
     *
     *   Usage:
     *       echo isValidNIF( '33576428Q' );
     *   Returns:
     *       TRUE
     */
    private function isValidNIF($docNumber)
    {
        $fixedDocNumber = strtoupper(substr("000000000" . $docNumber, -9));
        $writtenDigit = strtoupper(substr($docNumber, -1, 1));

        if ($this->isValidNIFFormat($fixedDocNumber)) {
            $correctDigit = $this->getNIFCheckDigit($fixedDocNumber);
            if ($writtenDigit == $correctDigit) {
                return true;
            }
        }

        return false;
    }

    /*
     *   This function validates a Spanish identification number
     *   verifying its check digits.
     *
     *   This function is intended to work with NIE numbers.
     *
     *   This function is used by:
     *       - isValidIdNumber
     *
     *   This function requires:
     *       - isValidNIEFormat
     *       - isValidNIF
     *
     *   This function returns:
     *       TRUE: If specified identification number is correct
     *       FALSE: Otherwise
     *
     *   Algorithm works as described in:
     *       http://www.interior.gob.es/dni-8/calculo-del-digito-de-control-del-nif-nie-2217
     *
     *   Usage:
     *       echo isValidNIE( 'X6089822C' )
     *   Returns:
     *       TRUE
     */
    private function isValidNIE($docNumber)
    {
        $fixedDocNumber = strtoupper(substr("000000000" . $docNumber, -9));

        if ($this->isValidNIEFormat($fixedDocNumber)) {

            if (substr($fixedDocNumber, 1, 1) == "T") {
                return true;
            }
            /* The algorithm for validating the check digits of a NIE number is
                identical to the altorithm for validating NIF numbers. We only have to
                replace Y, X and Z with 1, 0 and 2 respectively; and then, run
                the NIF altorithm */
            $fixedDocNumber = $this->prepareToNifValidation($fixedDocNumber);

            return $this->isValidNIF($fixedDocNumber);
        }

        return false;
    }

    /**
     * @param $fixedDocNumber
     * @return string
     */
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
    
    /*
     *   This function validates a Spanish identification number
     *   verifying its check digits.
     *
     *   This function is intended to work with CIF numbers.
     *
     *   This function is used by:
     *       - isValidDoc
     *
     *   This function requires:
     *       - isValidCIFFormat
     *       - getCIFCheckDigit
     *
     *   This function returns:
     *       TRUE: If specified identification number is correct
     *       FALSE: Otherwise
     *
     * CIF numbers structure is defined at:
     *   BOE number 49. February 26th, 2008 (article 2)
     *
     *   Usage:
     *       echo isValidCIF( 'F43298256' );
     *   Returns:
     *       TRUE
     */

    private function isValidCIF($docNumber)
    {
        $fixedDocNumber = strtoupper($docNumber);
        $writtenDigit = substr($fixedDocNumber, -1, 1);

        if ($this->isValidCIFFormat($fixedDocNumber) == 1) {
            $correctDigit = $this->getCIFCheckDigit($fixedDocNumber);

            if ($writtenDigit == $correctDigit) {
                return true;
            }
        }

        return false;
    }

    /*
     *   This function validates the format of a given string in order to
     *   see if it fits with NIF format. Practically, it performs a validation
     *   over a NIF, except this function does not check the check digit.
     *
     *   This function is intended to work with NIF numbers.
     *
     *   This function is used by:
     *       - isValidIdNumber
     *       - isValidNIF
     *
     *   This function returns:
     *       TRUE: If specified string respects NIF format
     *       FALSE: Otherwise
     *
     *   Usage:
     *       echo isValidNIFFormat( '33576428Q' )
     *   Returns:
     *       TRUE
     */

    private function isValidNIFFormat($docNumber)
    {
        return $this->respectsDocPattern($docNumber, self::NIF_REGEX);
    }
    /*
     *   This function validates the format of a given string in order to
     *   see if it fits with NIE format. Practically, it performs a validation
     *   over a NIE, except this function does not check the check digit.
     *
     *   This function is intended to work with NIE numbers.
     *
     *   This function is used by:
     *       - isValidIdNumber
     *       - isValidNIE
     *
     *   This function requires:
     *       - respectsDocPattern
     *
     *   This function returns:
     *       TRUE: If specified string respects NIE format
     *       FALSE: Otherwise
     *
     *   Usage:
     *       echo isValidNIEFormat( 'X6089822C' )
     *   Returns:
     *       TRUE
     */

    private function isValidNIEFormat($docNumber)
    {
        return $this->respectsDocPattern($docNumber, self::NIE_REGEX);
    }
    /*
     *   This function validates the format of a given string in order to
     *   see if it fits with CIF format. Practically, it performs a validation
     *   over a CIF, but this function does not check the check digit.
     *
     *   This function is intended to work with CIF numbers.
     *
     *   This function is used by:
     *       - isValidIdNumber
     *       - isValidCIF
     *
     *   This function requires:
     *       - respectsDocPattern
     *
     *   This function returns:
     *       TRUE: If specified string respects CIF format
     *       FALSE: Otherwise
     *
     *   Usage:
     *       echo isValidCIFFormat( 'H24930836' )
     *   Returns:
     *       TRUE
     */

    private function isValidCIFFormat($docNumber)
    {
        return $this->respectsDocPattern($docNumber, self::CIF_REGEX);
    }
    /*
     *   This function calculates the check digit for an individual Spanish
     *   identification number (NIF).
     *
     *   You can replace check digit with a zero when calling the function.
     *
     *   This function is used by:
     *       - isValidNIF
     *
     *   This function requires:
     *       - isValidNIFFormat
     *
     *   This function returns:
     *       - Returns check digit if provided string had a correct NIF structure
     *       - An empty string otherwise
     *
     *   Usage:
     *       echo getNIFCheckDigit( '335764280' )
     *   Returns:
     *       Q
     */

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
    /*
     *   This function calculates the check digit for a corporate Spanish
     *   identification number (CIF).
     *
     *   You can replace check digit with a zero when calling the function.
     *
     *   This function is used by:
     *       - isValidCIF
     *
     *   This function requires:
     *     - isValidCIFFormat
     *
     *   This function returns:
     *       - The correct check digit if provided string had a
     *         correct CIF structure
     *       - An empty string otherwise
     *
     *   Usage:
     *       echo getCIFCheckDigit( 'H24930830' );
     *   Prints:
     *       6
     */

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

    /*
     *   This function validates the format of a given string in order to
     *   see if it fits a regexp pattern.
     *
     *   This function is intended to work with Spanish identification
     *   numbers, so it always checks string length (should be 9) and
     *   accepts the absence of leading zeros.
     *
     *   This function is used by:
     *       - isValidNIFFormat
     *       - isValidNIEFormat
     *       - isValidCIFFormat
     *
     *   This function returns:
     *       TRUE: If specified string respects the pattern
     *       FALSE: Otherwise
     *
     *   Usage:
     *       echo respectsDocPattern(
     *           '33576428Q',
     *           '/^[KLM0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][A-Z]/' );
     *   Returns:
     *       TRUE
     */
    private function respectsDocPattern($givenString, $pattern)
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
