<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use mikehaertl\pdftk\Pdf;

final class indexTest extends TestCase
{
    //Test si la string en base64 sortie est identique en ayant le même nom de fichier pour chaque pdf
    public function testStringSamePdfBase64() : void
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
        ->saveAs(__DIR__ . '/PDF/filled.pdf');
    
        $nameBase64_2 = base64_encode(__DIR__ . '/PDF/filled.pdf');
        
        
        $this->assertEquals($nameBase64, $nameBase64_2);
    }


    //Test si la string en base64 sortie est pas identique en ayant pas le même nom de fichier pour chaque pdf
    public function testStringDifferentBase64() : void
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
    }
}