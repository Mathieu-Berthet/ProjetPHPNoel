<?php 

namespace Mathieu\ProjetPhpNoel\Controller;
use mikehaertl\pdftk\Pdf;


class GeneratorPDF
{
    public static $Cerfa_Entreprise = 'Entreprise';
    public static $Cerfa_Particulier = 'Particulier';


    private static $instancePDF;
    private string $pathPDFFinal;

    private int $numberGeneration = 0;


    //Créer le dossier ou seront stockés les pdfs générés par le web-service
    private function __construct()
    {
        $pathPDFFinal = __DIR__ . "/../..//PDF/PDFGenerate";

        if(!file_exists($pathPDFFinal))
        {
            mkdir($pathPDFFinal);
        }

        $this->pathPDFFinal = $pathPDFFinal;
    }

    //Récupère l'instance, ou la crée si elle n'existe pas, afin qu'elle soit unique. (Pattern Singleton)
    public static function getInstance()
    {
        if(!isset(self::$instancePDF))
        {
            self::$instancePDF = new GeneratorPDF();
        }

        return self::$instancePDF;
    }

    //Remplit le pdf avec les données passées en paramètres
    public function generatePDF($path, $data)
    {
        $pathPDFFinal = $this->pathPDFFinal . '/cerfaNumber' . $this->numberGeneration . ".pdf";
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

        $this->numberGeneration++;

        return $contentBase64;
    }

    //Lance la génération du PDF selon le type donné 
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