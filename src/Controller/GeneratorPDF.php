<?php 

namespace Mathieu\ProjetPhpNoel\Controller;

use DateTime;
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
        
        $pathPDFFinal = __DIR__ . "/../../PDF/PDFGenerate";


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

    public function isValidDate($date, $format = 'Y-m-d') {
        $dateTime = DateTime::createFromFormat($format, $date);
        return $dateTime && $dateTime->format($format) === $date;
    }

    public function validate($model, $client)
    {
        $FILLED = [];
        foreach($model as $field => $rules)
        {
            $value = @$client[$field];
            //var_dump($value);
            if ($rules['mandatory'] === true && !$value) return "missing field '$field'";
            if (is_array($rules['mandatory'])) {
                foreach ($rules['mandatory'] as $subfield => $subvalues) {
                    foreach ($subvalues as $subvalue) {
                        if (!isset($value) && $subvalue === $client[$subfield]) return "missing field '$field'";
                    }
                }
            }
            if (isset($value)) {
                if ($rules['type'] === 'date' && !$this->isValidDate($value)) {
                    return "incompatible date format for field '$field'";
                }
                if ($rules['type'] !== 'date' && gettype($value) !== $rules['type']) {
                    return "incompatible type for field '$field'";
                }
                if (isset($rules['dependency'])) {
                    $dependency = $rules['dependency']['field'];
                    foreach ($rules['dependency']['values'][$client[$dependency]] as $subfield => $subvalue) {
                        if ($rules['type'] === 'date') {
                            $FILLED[$subfield] = DateTime::createFromFormat('Y-m-d', $value)->format($subvalue);
                        } else {
                            $FILLED[$subfield] = $subvalue;
                        }
                    }
                } else {
                    $FILLED[$rules['field']] = $value;
                }
            }
        }
        return $FILLED;
    }

    //Remplit le pdf avec les données passées en paramètres
    public function generatePDF($path, $data)
    {
        $pathPDFFinal = $this->pathPDFFinal . '/cerfaNumber' . $this->numberGeneration . ".pdf";

        $pdf = new Pdf($path);
        //echo($path);
        //echo($data);
        $result = $pdf->fillForm($data)
        ->needAppearances()
        ->saveAs($pathPDFFinal);

        // Always check for errors
        if ($result === false) {
            $error = $pdf->getError();
            //var_dump($error);
        }

        $contenu = file_get_contents($pathPDFFinal);

        $contentBase64 = base64_encode($contenu);

        $this->numberGeneration++;

        return $contentBase64;
    }

    //Lance la génération du PDF selon le type donné 
    public function finalPDF($type, $json)
    {
        $dataFromJson = json_decode($json, true);
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

    public function testData()
    {
        $model = json_decode(file_get_contents(__DIR__. '/../../validity.json'), true);
        
        //Test donné par Johann
        $client = json_decode('{
            "asso_siren": "SPA",
            "asso_name": "LA SPA",
            "asso_street": "Paris",
            "asso_type": "LOI1901"
        }', true);
        $client2 = json_decode('{
            "asso_siren": "SPA",
            "asso_name": "LA SPA",
            "asso_street": "Paris",
            "asso_type": "FRUP",
            "date": "2023-01-01"
        }', true);
        /*echo json_encode( $this->validate($model, $client) ) ." >> Done". PHP_EOL;
        echo json_encode( $this->validate($model, $client2) ) ." >> Done". PHP_EOL;*/
        $client = json_decode('{
            "asso_siren": "SPA",
            "asso_name": "LA SPA",
            "asso_street": "Paris",
            "asso_type": "FRUP"
        }', true);
        $client2 = json_decode('{
            "asso_siren": "SPA",
            "asso_name": "LA SPA",
            "asso_street": "Paris",
            "asso_type": "FRUP",
            "date": "01/01/2023"
        }', true);

        $client3 = json_decode('{
            "asso_siren": "SPA",
            "asso_name": "LA SPA",
            "asso_street": "Paris",
            "asso_type": ""
        }', true);
       /*echo json_encode( $this->validate($model, $client) ) ." >> Done". PHP_EOL;
        echo json_encode( $this->validate($model, $client2) ) ." >> Done". PHP_EOL;
        echo json_encode( $this->validate($model, $client3) ) ." >> Done". PHP_EOL;*/




        //Mes jeux de test
        //2 qui réussisse
        $myClient1 = json_decode('{
            "num_recu": "4",
            "asso_name": "LA SPA",
            "asso_siren": "SPA",
            "asso_street_number": "45",
            "asso_street": "rue des animaux",
            "asso_CP": "75478",
            "asso_city": "Paris",
            "asso_country": "France",
            "asso_type": "LOI1901"
        }', true);

        $myClient2 = json_decode('{
            "num_recu": "3",
            "asso_name": "Le Refuge",
            "asso_siren": "Refuge",
            "asso_street_number": "23",
            "asso_street": "rue des amis",
            "asso_CP": "69000",
            "asso_city": "Lyon",
            "asso_country": "France",
            "asso_type": "LOI1901"
        }', true);
        echo json_encode( $this->validate($model, $myClient1) ) ." >> Done". PHP_EOL;
        echo json_encode( $this->validate($model, $myClient2) ) ." >> Done". PHP_EOL;

        //2 qui plante
        $myClient3 = json_decode('{
            "num_recu": "4",
            "asso_name": "LA SPA",
            "asso_siren": "SPA",
            "asso_street_number": "45",
            "asso_street": "rue des animaux",
            "asso_CP": "12478",
            "asso_city": "Paris",
            "asso_country": "France",
            "asso_type": "LOI1901"
        }', true);

        $myClient4 = json_decode('{
            "num_recu": "4",
            "asso_name": "LA SPA",
            "asso_siren": "SPA",
            "asso_street_number": "45",
            "asso_street": "rue des animaux",
            "asso_CP": "",
            "asso_city": "Paris",
            "asso_country": "France",
            "asso_type": "LOI1901"
        }', true);

        echo json_encode( $this->validate($model, $myClient3) ) ." >> Done". PHP_EOL;
        echo json_encode( $this->validate($model, $myClient4) ) ." >> Done". PHP_EOL;
    }
}




/***

2 json en entrée. Celui envoyé dans la requête, + un avec les règles métiers

Celui des règles métiers comprend ce que le champs doit être, s'il est obligatoire, ses dépendances, et le champ équivalent du pdf

Celui qu'on reçoit contiendra les valeurs qui serviront au remplissage

Selon les règles, on va aller chercher la valeur correspondante pour la remplir


*/