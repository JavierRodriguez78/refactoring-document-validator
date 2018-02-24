<?php

namespace DocumentValidator\Tests;

use DocumentValidator\DocumentValidator;
use PHPUnit\Framework\TestCase;

class DocumentValidatorTest extends TestCase
{
    /**
     * @test
     * @dataProvider  getValidNif
     */
    public function itShouldValidateAValidNif($nif)
    {
        $validator = new DocumentValidator();

        $result = $validator->isValidIdNumber($nif, 'nif');

        $this->assertTrue($result);
    }

    public function getValidNif()
    {
        return [
            ['52129471V'],
            ['56103805C'],
            ['53834223D'],
            ['62219093E'],
            ['13927496F'],
            ['47131798Z'],
            ['85424889Z'],
            ['61356650X'],
            ['53753476S'],
            ['28772951C'],
        ];
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
     * @dataProvider getValidCif
     */
    public function itShouldValidateAValidCif($cif)
    {
        $validator = new DocumentValidator();

        $result = $validator->isValidIdNumber($cif, 'cif');

        $this->assertTrue($result);
    }

    public function getValidCif()
    {
        return [
            ['B66044967'],
            ['B23901762'],
            ['Q0107868B'],
            ['A29814019'],
            ['A04828547'],
            ['Q3456795H'],
            ['B68466820'],
            ['A58313859'],
            ['S5750409D'],
        ];
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
