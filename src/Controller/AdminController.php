<?php

namespace App\Controller;

use App\Repository\QuestionnaireRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_home", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('admin/home.html.twig', [
            'dateActuelle' => new \DateTime(),
            'questionnaireActif' => false
        ]);
    }

    /**
     * @Route("/recuperation_donnees", name="recuperation_donnees", methods={"GET", "POST"})
     */
    public function recuperation_donnees(QuestionnaireRepository $questionnaireRepository, Request $request): Response
    {
        $filesystem = new Filesystem();
        $document = "documentsBD/bd.csv";
        try {
            if($filesystem->exists($document)){
                $filesystem->remove($document);
            }
            $filesystem->touch($document);
            $contenue = '"date_realisation";"code";"question";"reponse"' . "\n";
            $filesystem->appendToFile($document, $contenue);
            $questionnaires = $questionnaireRepository->findAllOrderByDateAndCode();
            foreach ($questionnaires as $questionnaire){
                $contenue =
                    '"' . $questionnaire->getDateRealisation()->format('d/m/Y') . '";' .
                    '"' . $questionnaire->getCode() . '";' .
                    '"' . $questionnaire->getQuestion() . '";' .
                    '"' . $questionnaire->getReponse() . '"'. "\n";
                $filesystem->appendToFile($document, $contenue);
            }
            return $this->render('admin/recuperationBD.html.twig', [
                'dateActuelle' => new \DateTime(),
                'questionnaireActif' => false
            ]);
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating your directory at ".$exception->getPath();
        }

    }

    /**
     * @Route("/generation_code", name="generation_code", methods={"GET", "POST"})
     */
    public function generation_code(UtilisateurRepository $utilisateurRepository, Request $request): Response
    {
        $reponseFormulaire = false;
        $codes = array();
        $nb_code_genere = $request->request->get('nb_code_generer');
        if(!empty($nb_code_genere) && $nb_code_genere>0){
            $reponseFormulaire = true;
            $codes = $this->genererXCodes($utilisateurRepository, $nb_code_genere);
        }
        return $this->render('admin/generationCode.html.twig', [
            'dateActuelle' => new \DateTime(),
            'reponseFormulaire' => $reponseFormulaire,
            'questionnaireActif' => false,
            'codes' => $codes
        ]);
    }

    public function genererXCodes(UtilisateurRepository $utilisateurRepository, int $nb_code_generer)
    {
        $res = array();
        for($i = 0; $i < $nb_code_generer; $i++){
            $res[$i] = $this->generer1Code($utilisateurRepository);
        }
        return $res;
    }

    public function generer1Code(UtilisateurRepository $utilisateurRepository)
    {
        $res = "";
        $utilisateurs = $utilisateurRepository->findAll();
        $codeInvalide = true;
        while ($codeInvalide){
            $res = $this->generation();
            $cpt = 0;
            foreach ($utilisateurs as $utilisateur){
                if($res == $utilisateur->getCode()){
                    $cpt++;
                }
            }
            if($res == $this->get('session')->get('code')){
                $cpt++;
            }
            if($cpt == 0){
                $codeInvalide = false;
            }
        }
        return $res;
    }

    public function generation() {
        $string = "";
        $chaine = "abcdefghijklmnpqrstuvwxyAZERTYUIOPQSDFGHJKLMWXCVBN1234567890";
        srand((double)microtime()*1000000);
        for($i=0; $i<10; $i++) {
            $string .= $chaine[rand()%strlen($chaine)];
        }
        return $string;
    }
}
