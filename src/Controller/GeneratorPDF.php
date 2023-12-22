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
                    foreach($rules['field'] as $subfield)
                    {
                        $FILLED[$subfield] = $value;
                    }
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
 
        //Cerfa entreprise
        //2 qui réussisse
        $myClient1 = json_decode('{
            "num_recu": 4,
            "asso_name": "LA SPA",
            "asso_siren": "SPA",
            "asso_street_number": 45,
            "asso_street": "rue des animaux",
            "asso_CP": 75478,
            "asso_city": "Paris",
            "asso_country": "France",
            "object_line_1": "The object of the don",
            "asso_type": "LOI1901",
            "amount_nature": 15,
            "amount_nature_letter": "quinze",
            "description": "For help to develop organisation",
            "amount_versement": 25,
            "amount_versement_letter": "vingt cinq",
            "total_amount": 40,
            "total_amount_letter": "quarante"
        }', true);

        $myClient2 = json_decode('{
            "num_recu": 3,
            "asso_name": "Le Refuge",
            "asso_siren": "Refuge",
            "asso_street_number": 23,
            "asso_street": "rue des amis",
            "asso_CP": 69000,
            "asso_city": "Lyon",
            "asso_country": "France",
            "object_line_1": "Help for young and old people",
            "asso_type": "LOI1901",
            "amount_nature": 40,
            "amount_nature_letter": "quarante",
            "description": "For help young people",
            "amount_versement": 34,
            "amount_versement_letter": "trente quatre",
            "total_amount": 74,
            "total_amount_letter": "soixante quatorze"
        }', true);
        echo json_encode( $this->validate($model, $myClient1) ) ." >> Done". PHP_EOL;
        echo "<br />";
        echo json_encode( $this->validate($model, $myClient2) ) ." >> Done". PHP_EOL;
        echo "<br />";

        //2 qui plante
        $myClient3 = json_decode('{
            "num_recu": 4,
            "asso_name": "LA SPA",
            "asso_siren": "SPA",
            "asso_street_number": 45,
            "asso_street": "rue des animaux",
            "asso_CP": 12478,
            "asso_city": "Paris",
            "asso_country": "France",
            "object_line_1": "The object of the don",
            "asso_type": "LOI1901",
            "date": "2008/05/24",
            "amount_nature": 15,
            "amount_nature_letter": "quinze",
            "description": "For help to develop organisation",
            "amount_versement": 25,
            "amount_versement_letter": "vingt cinq",
            "total_amount": 40,
            "total_amount_letter": "quarante"
        }', true);

        $myClient4 = json_decode('{
            "num_recu": 3,
            "asso_name": "Le Refuge",
            "asso_siren": "Refuge",
            "asso_street_number": 23,
            "asso_street": "rue des amis",
            "asso_CP": 69000,
            "asso_city": "Lyon",
            "asso_country": "France",
            "object_line_1": "Help for young and old people",
            "asso_type": "LOI1901",
            "amount_nature": 40,
            "amount_nature_letter": "quarante",
            "description": "For help young people",
            "amount_versement": 34,
            "amount_versement_letter": "trente quatre",
            "total_amount": 74,
            "total_amount_letter": 74
        }', true);

        echo json_encode( $this->validate($model, $myClient3) ) ." >> Done". PHP_EOL;
        echo "<br />";
        echo json_encode( $this->validate($model, $myClient4) ) ." >> Done". PHP_EOL;
        echo "<br />";

        //Test with validate for cerfa particulier
        $modelIndiv = json_decode(file_get_contents(__DIR__. '/../../validityParticulier.json'), true);

        $myClient5 = json_decode('{
            "num_recu": 5,
            "indiv_name": "LA SPA",
            "indiv_siren": "SPA",
            "indiv_street_number": 45,
            "indiv_street": "rue des animaux",
            "indiv_CP": 75478,
            "indiv_city": "Paris",
            "indiv_country": "France",
            "object_line_1": "The object of the don",
            "object_line_2": "The object of the don 2",
            "indiv_type": "LOI1901",
            "indiv_last_name": "Doe",
            "indiv_first_name": "John",
            "amount_versement": 25,
            "amount_versement_letter": "vingt cinq",
            "NAT_DON_AUTRESS": "Heritage"
        }', true);

        $myClient6 = json_decode('{
            "num_recu": 6,
            "indiv_name": "Le Refuge",
            "indiv_siren": "Refuge",
            "indiv_street_number": 23,
            "indiv_street": "rue des amis",
            "indiv_CP": 69000,
            "indiv_city": "Lyon",
            "indiv_country": "France",
            "object_line_1": "Help for young and old people",
            "object_line_2": "Help for young and old people 2",
            "indiv_type": "ANR",
            "indiv_last_name": "Doe",
            "indiv_first_name": "John",
            "amount_versement": "34",
            "amount_versement_letter": "trente quatre",
            "NAT_DON_AUTRESS": "Heritage"
        }', true);

        echo json_encode( $this->validate($modelIndiv, $myClient5) ) ." >> Done". PHP_EOL;
        echo "<br />";
        echo json_encode( $this->validate($modelIndiv, $myClient6) ) ." >> Done". PHP_EOL;
    }
}




/***

2 json en entrée. Celui envoyé dans la requête, + un avec les règles métiers

Celui des règles métiers comprend ce que le champs doit être, s'il est obligatoire, ses dépendances, et le champ équivalent du pdf

Celui qu'on reçoit contiendra les valeurs qui serviront au remplissage

Selon les règles, on va aller chercher la valeur correspondante pour la remplir


*/