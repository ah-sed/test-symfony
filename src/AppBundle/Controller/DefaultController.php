<?php

namespace AppBundle\Controller;


use AppBundle\Service\TraitementFichier;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render(':default:index.html.twig');
    }

    /**
     * @Route("/traitement-fichier", name="traitement_fichier")
     */
    public function traitementFichierAction(Request $request)
    {
        //On utilise finder pour récupèrer les fichiers
        $finder = new Finder();
        $finder->files()->in(__DIR__ . '\..\..\..\web\files');

        //On appel le service permettant le traitement des fichiers et l'enregistrement en bdd
        /* @var $traitementFichier TraitementFichier */
        $traitementFichier = $this->get('app.traitement_fichier');
        $nbEnregistrements = $traitementFichier->enregistrementDonnees($finder);

        return $this->render(':default:index.html.twig', array(
            'nbEnregistrements' => $nbEnregistrements
        ));
    }

    /**
     * @Route("/patients", name="patients")
     */
    public function showPatientsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $patients = $em->getRepository('AppBundle:Patient')->findAll();

        return $this->render(':default:patients.html.twig', array(
            'patients' => $patients,
        ));
    }

    /**
     * @Route("/medecins", name="medecins")
     */
    public function showMedecinsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $medecins = $em->getRepository('AppBundle:Medecin')->findAll();

        return $this->render(':default:medecins.html.twig', array(
            'medecins' => $medecins,
        ));
    }

    //On retourne le nombre total des patients
    public function countPatiensAction(){
        $em = $this->getDoctrine()->getManager();
        $patients = $em->getRepository('AppBundle:Patient')->findAll();

        return new Response(count($patients));
    }

    //On retourne le nombre total des medecins
    public function countMedecinsAction(){
        $em = $this->getDoctrine()->getManager();
        $medecins = $em->getRepository('AppBundle:Medecin')->findAll();

        return new Response(count($medecins));
    }
}
