<?php

namespace DocumentValidator\Tests;

use DocumentValidator\DocumentValidator;
use PHPUnit\Framework\TestCase;

class DocumentValidatorTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldValidateAValidNif()
    {
        $validator = new DocumentValidator();

        $result = $validator->isValidIdNumber('33576428Q', 'nif');

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function itShouldNotValidateAnInvalidNif()
    {
        $validator = new DocumentValidator();

        $result = $validator->isValidIdNumber('1234.4324!', 'nif');

        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function itShouldValidateAValidNie()
    {
        $validator = new DocumentValidator();

        $result = $validator->isValidIdNumber('X6089822C', 'nie');

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function itShouldNotValidateAnInvalidNie()
    {
        $validator = new DocumentValidator();

        $result = $validator->isValidIdNumber('1234.4324!', 'nie');

        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function itShouldValidateAValidCif()
    {
        $validator = new DocumentValidator();

        $result = $validator->isValidIdNumber('F43298256', 'cif');

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function itShouldNotValidateAnInvalidCif()
    {
        $validator = new DocumentValidator();

        $result = $validator->isValidIdNumber('1234.4324!', 'cif');

        $this->assertFalse($result);
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionMessage  Unsupported Type
     */
    public function itShouldThrownAnExceptionWhenTheDocumentTypeIsInvalid()
    {
        $validator = new DocumentValidator();

        $validator->isValidIdNumber('F43298256', 'pluf');
    }

}
