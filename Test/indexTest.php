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

        $pathForNewPDF = __DIR__ . "/../PDF/PDFGenerate";
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
    public function testStringSamePdfBase64() : void
    {
        $dataForTest = self::$dataFromJson['entreprise'];

        $pathExpected = self::$pathPDF . '/test.expected.pdf';
        $this->pathPDFEntreprise->fillForm($dataForTest)->needAppearances()->saveAs($pathExpected);
        $contentExepected  = file_get_contents($pathExpected);

        $expectedContentBase64 = base64_encode($contentExepected);
        $actualContent = GeneratorPDF::getInstance()->finalPDF(GeneratorPDF::$Cerfa_Entreprise, json_encode($dataForTest));
        
        
        $this->assertEquals($contentExepected, $actualContent);
    }


    //Test si la string en base64 sortie est pas identique en ayant pas le même nom de fichier pour chaque pdf
    /*public function testStringDifferentBase64() : void
    {
        $pdf = new Pdf(__DIR__ . '/PDF/cerfaParticulier.pdf');

        $result = $pdf->fillForm([
            'a2' => "SPA",
        ])
        ->needAppearances()
        ->saveAs(__DIR__ . '/PDF/filled.pdf');
    
        $nameBase64 = base64_encode(__DIR__ . '/PDF/filled.pdf');


        $pdf2 = new Pdf(__DIR__ . '/PDF/cerfaParticulier.pdf');
        
        $result2 = $pdf2->fillForm([
            'a2' => "SPA",
        ])
        ->needAppearances()
        ->saveAs(__DIR__ . '/PDF/filled2.pdf');
    
        $nameBase64_2 = base64_encode(__DIR__ . '/PDF/filled2.pdf');
        
        
        $this->assertNotEquals($nameBase64, $nameBase64_2);
    }*/
}



/* Pour les signatures

     * Create double page signature

    private function createSignature(string $id, string $base64Signature)
    {
        $path = self::$PATH ."/signature_{$id}";
        $png = $path .'.png';
        $pdf = $path .'.pdf';
        file_put_contents($png, $base64Signature);
        $tcpdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $tcpdf->AddPage(); // page blank
        $tcpdf->AddPage();
        $tcpdf->Image($png,134,216,50,20,'PNG'); // @TODO How to keep ratio ?
        $tcpdf->Output('/home/runcloud/webapps/cerfa-generator/'. $pdf, 'F');
        return $pdf;
    }
    public function generatePDF()
    {
        $id = uniqid();
        $signature = $this->createSignature($id, base64_decode($this->values()['signature']));
        $filename = "CerfaReceipt{$id}.pdf";
        $template = new Pdf(self::$PATH .'/cerfa_16216_01.pdf');
        $result = $template->fillForm($this->values());
        $template = new Pdf($template);
        $result = $template->flatten() // to compress
                           ->multistamp($signature) // to add signature
                           ->saveAs(self::$PATH . $filename);
        if ($result === false) {
            throw new \Exception($template->getError());
        }
        $this->file = $filename;
    }

*/