<?php

namespace DocumentValidator;

class DocumentValidator
{
    public function isValidIdNumber($docNumber, $type)
    {
        $fixedDocNumber = strtoupper($docNumber);
        $fixedType = strtoupper($type);

        switch ($fixedType) {
            case 'NIF':
                $validator = new NifValidator();
                return $validator->isValid($fixedDocNumber);
            case 'NIE':
                $validator = new NieValidator();
                return $validator->isValid($fixedDocNumber);
            case 'CIF':
                $validator = new CifValidator();
                return $validator->isValid($fixedDocNumber);
            default:
                throw new \Exception('Unsupported Type');
        }
    }
}
