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

    /**
     * Generate with: http://ensaimeitor.apsl.net/fiscal/10/
     * @return array
     */
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
     * @dataProvider getValidNie
     */
    public function itShouldValidateAValidNie($nie)
    {
        $validator = new DocumentValidator();

        $result = $validator->isValidIdNumber($nie, 'nie');

        $this->assertTrue($result);
    }

    public function getValidNie()
    {
        return [
            ['Z2757153X'],
            ['Y4146427R'],
            ['Z6998997X'],
            ['Y2866432W'],
            ['X2536926A'],
            ['X2536926A'],
            ['X8440061G'],
            ['Y4985449Y'],
            ['X1402535H'],
            ['X0673874C'],
        ];
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

    /**
     * Generated with http://ensaimeitor.apsl.net/cif/10/
     * @return array
     */
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
            ['P8343433B'],
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
