<?php 

namespace Mathieu\ProjetPhpNoel\Controller;
use mikehaertl\pdftk\Pdf;


class GeneratorPDF
{
    private $Cerfa_Entreprise = 'Entreprise';
    private $Cerfa_Particulier = 'Particulier';


    private static $instancePDF;
    private string $pathPDF;

    private int $numberGeneration = 0;


    private function __construct()
    {
        $myPath = __DIR__ . "/../../PDFGenerate";
        if(!file_exists($myPath))
        {
            mkdir($myPath);
        }

        $this->pathPDF = $myPath;
    }

    public function getInstance()
    {
        if(!isset(self::$instancePDF))
        {
            self::$instancePDF = new generatePDF();
        }

        return self::$instancePDF;
    }

    public static function generatePDF($path, $data)
    {
        $pathPDFFinal = $this->pathPDF . '/cerfaNumber' . $numberGeneration . ".pdf";
        $pdf = new Pdf($path);

        $result = $pdf->fillForm($data)
        ->needAppearances()
        ->saveAs($pathPDFFinal);

        // Always check for errors
        if ($result === false) {
            $error = $pdf->getError();
            var_dum($error);
        }

        $contenu = file_get_contents($pathPDFFinal);

        //$nameBase64 = base64_encode(__DIR__ . '/PDF/filled.pdf');

        $contentBase64 = base64_encode($contenu);

        $numberGeneration++;

        return $contentBase64;
    }

    public function finalPDF($type, $json)
    {
        $dataFromJson = json_decode($json);
        if($type === self::$Cerfa_Entreprise)
        {
            $pdfGenerate = $this->generatePDF(App_config::get('CERFA_ENTREPRISE_PATH'), $dataFromJson);
        }
        else if($type === self::$Cerfa_Particulier)
        {
            $pdfGenerate = $this->generatePDF(App_config::get('CERFA_PARTICULIER_PATH'), $dataFromJson);
        }
        else
        {
            echo("Ce type de certificat n'existe pas");
        }


        return $pdfGenerate;
    }

}