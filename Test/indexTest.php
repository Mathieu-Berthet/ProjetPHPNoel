<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Mathieu\ProjetPhpNoel\Controller\App_config;
use Mathieu\ProjetPhpNoel\Controller\GeneratorPDF;


use mikehaertl\pdftk\Pdf;

final class indexTest extends TestCase
{
    private Pdf $pathPDFEntreprise;
    private Pdf $pathPDFParticulier;

    private static string $pathPDF;
    private static array $dataFromJson;

    public static function setUpBeforeClass() : void
    {
        //Set up json File
  
        $myContent = file_get_contents(__DIR__ . '/../db.json');

        self::$dataFromJson = json_decode($myContent, true);

        //echo($dataFromJson);

        //Set up path from pdf

        $pathForNewPDF = __DIR__ . "/../PDF/PDFTest";
        if(!file_exists($pathForNewPDF))
        {
            mkdir($pathForNewPDF);
        }
        self::$pathPDF = $pathForNewPDF;
    }

    protected function setUp() : void
    {
        $this->pathPDFEntreprise = new Pdf(App_config::get('CERFA_ENTREPRISE_PATH'));
        $this->pathPDFParticulier = new Pdf(App_config::get('CERFA_PARTICULIER_PATH'));
    }

    //Test si la string en base64 sortie est la même si on rempli 2 pdf avec les mêmes données dedans.
    public function testNumOrdreCerfa() : void
    {
        $dataForTest = json_decode('{
            "a1": 12
        }', true);

        $pathExpected = self::$pathPDF . '/test.pdf';
        $this->pathPDFEntreprise->fillForm($dataForTest)->needAppearances()->saveAs($pathExpected);
        $contentExepected  = file_get_contents($pathExpected);

        $expectedContentBase64 = base64_encode($contentExepected);

        $dataForTestWithGenerator = json_decode('{
            "type_cerfa": "Entreprise",
            "num_recu": "12"
        }', true);

        $actualContent = GeneratorPDF::getInstance()->finalPDF(GeneratorPDF::$Cerfa_Entreprise, json_encode($dataForTestWithGenerator));

        $this->assertEquals($expectedContentBase64, $actualContent);
    }
}