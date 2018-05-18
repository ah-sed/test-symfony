<?php
/**
 * Created by PhpStorm.
 * User: aseddik-adc
 * Date: 18/05/2018
 * Time: 09:44
 */

namespace AppBundle\Service;


use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Adresse;
use AppBundle\Entity\Medecin;
use AppBundle\Entity\Patient;


class TraitementFichier
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function enregistrementDonnees($finder)
    {
        //Puisque le flush se fait à la fin du traitement,
        // on va ajouter chaque rpps dans un tableau pour vérifier les doublons
        $rppsListe = array();

        //On crée un tableau pour stocker le nombre d'ajout patients et medecins;
        $nbEnregitrement = array(
            'patients' => array(),
            'medecins' => array()
        );

        //Traitement de chaque fichier
        foreach ($finder as $file) {
            //On ouvre chaque fichier en mode lecture
            $file = fopen($file->getRealPath(), 'r');

            //On controle la fin du fichier
            while (!feof($file)) {
                //On lit ligne par ligne le fichier
                $ligne = fgets($file);

                //On va récupérer seulement les lignes commençant par 'PID' et 'ROL'
                $identifiant = substr($ligne, 0, 3);

                //On va couper le segment selon le délimiteur '|'
                $infos = explode('|', $ligne);

                //Patient
                if ($identifiant == 'PID') {
                    //PID-5
                    $nomPrenom = explode('^', $infos[5]);
                    $nom = $nomPrenom[0];
                    $prenom = $nomPrenom[1];

                    //PID-7
                    //On transforme la chaine de caractère en datetime
                    $dateNaissance = new \DateTime(date('d-m-Y H:i:s', strtotime($infos[7])));

                    //PID-8
                    $civilite = strtoupper($infos[8]);

                    //PID-11
                    $adresse = explode('^', $infos[11]);
                    $rue = $adresse[0];
                    $codePostal = $adresse[4];
                    $ville = $adresse[2];

                    //On instancie une adresse
                    $adressePatient = new Adresse($rue, $codePostal, $ville);

                    //On instancie un patient
                    $patient = new Patient($nom, $prenom, $dateNaissance, $civilite, $adressePatient);

                    //On persit le patient
                    $this->em->persist($patient);

                    $nbEnregitrement['patients'][] = $patient;
                }

                //Médecin
                if ($identifiant == 'ROL') {
                    //ROL-4
                    $infosMedecin = explode('^', $infos[4]);
                    $nom = $infosMedecin[1];
                    $prenom = $infosMedecin[2];

                    if ($infosMedecin[12] == 'RPPS') {
                        $rpps = $infosMedecin[0];

                        //On vérifier en BDD si le medecin existe
                        $medecinBdd = $this->em->getRepository('AppBundle:Medecin')->findOneBy(array('rpps' => $rpps));

                        if (!in_array($rpps, $rppsListe) && !$medecinBdd) {
                            //On instancie un medecin
                            $medecin = new Medecin($nom, $prenom, $rpps);

                            //On stock le rpps dans le tableau
                            $rppsListe[$rpps] = $rpps;

                            $this->em->persist($medecin);

                            $nbEnregitrement['medecins'][] = $medecin;
                        }
                    }
                }
            }

            fclose($file);
        }

        //On flush
        $this->em->flush();

        //On retourn le nombre d'enregistrement
        return $nbEnregitrement;
    }


}