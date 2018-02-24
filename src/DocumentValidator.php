<?php

namespace DocumentValidator;

class DocumentValidator
{
    private $validators;

    public function __construct()
    {
        $this->validators = [
            'NIF' => new NifValidator(),
            'NIE' => new NieValidator(),
            'CIF' => new CifValidator(),
        ];
    }

    public function isValidIdNumber($docNumber, $type)
    {
        $normalizedType = strtoupper($type);

        $validator = $this->getValidator($normalizedType);

        return $validator->isValid($docNumber);
    }

    private function getValidator($type)
    {
        if (!array_key_exists($type, $this->validators)) {
            throw new \Exception('Unsupported Type');
        }

        return $this->validators[$type];
    }
}
