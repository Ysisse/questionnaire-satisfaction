<?php


namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Questionnaire;
use App\Entity\Utilisateur;
use App\Form\ConnexionType;
use App\Repository\AdminRepository;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="home");
     */
    public function home(AdminRepository $adminRepository, UtilisateurRepository $utilisateurRepository, Request $request) : Response
    {
        $this->get('session')->set('code', null);
        $this->get('session')->set('repondreQuestionnaire', false);
        $admin = new Admin();
        $form = $this->createForm(ConnexionType::class, $admin);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $code = $form->get('code')->getData();
            if(strlen($code) == 10){
                $admin = $adminRepository->findOneByCode($code);
                if($admin != null and $admin->getCode()==$code){
                    if($admin->getCode() == "0123456789"){
                        $this->get('session')->set('code', $code);
                        $this->get('session')->set('repondreQuestionnaire', true);
                        $this->get('session')->set('codeTest', true);
                        return $this->redirectToRoute('questionnaire_satisfaction');
                    } else {
                        $this->get('session')->set('code', $code);
                        return $this->redirectToRoute('admin_home');
                    }
                } else {
                    $utilisateur = $utilisateurRepository->findOneByCode($code);
                    if($utilisateur != null and $utilisateur->getCode()==$code && !$utilisateur->getUtilise()){
                        $this->get('session')->set('code', $code);
                        $this->get('session')->set('repondreQuestionnaire', true);
                        $this->get('session')->set('codeTest', false);
                        return $this->redirectToRoute('questionnaire_satisfaction');
                    }
                }
            }
        }
        $admin = new Admin();
        return $this->render('home.html.twig', [
            'admin' => $admin,
            'form' => $form->createView(),
            'dateActuelle' => new \DateTime(),
            'questionnaireActif' => false
        ]);
    }

    /**
     * @Route("/questionnaire_satisfaction", name="questionnaire_satisfaction");
     */
    public function questionnaire_satisfaction(UtilisateurRepository $utilisateurRepository, Request $request) : Response
    {
        if( !$this->get('session')->get('repondreQuestionnaire') && $request->isMethod('GET')){
            return $this->redirectToRoute('home');
        }
        $this->get('session')->set('repondreQuestionnaire', false);
        $formulaireValider = false;

        if($this->verifFormulaire($request)) {
            $formulaireValider = true;
            if ($this->get('session')->get('codeTest')) {

            } else {
                $code = $this->get('session')->get('code');

                $utilisateur = $utilisateurRepository->findOneByCode($code);
                $utilisateur->setUtilise(true);
                $this->getDoctrine()->getManager()->flush();

                /*$utilisation = new Utilisateur();
                $utilisation->setCode($code);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($utilisation);*/

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("1 - Age")
                    ->setReponse($request->request->get('age'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("2 - Formations / Dispositifs")
                    ->setReponse($request->request->get('formations_dispositifs'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("3 - Département d’origine")
                    ->setReponse($request->request->get('departement_origine'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);
                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("3 - Numéro du département")
                    ->setReponse($request->request->get('num_departement'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("4 - Régime")
                    ->setReponse($request->request->get('regime'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);
                $reponseDecouverteEtablissement = "";
                if (!empty($request->request->get('MDPH_decouvertEtablissement'))) {
                    $reponseDecouverteEtablissement .= $request->request->get('MDPH_decouvertEtablissement') . " ";
                }
                if (!empty($request->request->get('internet_decouvertEtablissement'))) {
                    $reponseDecouverteEtablissement .= $request->request->get('internet_decouvertEtablissement') . " ";
                }
                if (!empty($request->request->get('reseauPersonnel_decouvertEtablissement'))) {
                    $reponseDecouverteEtablissement .= $request->request->get('reseauPersonnel_decouvertEtablissement') . " ";
                }
                if (!empty($request->request->get('conseillerCapEmploi_decouvertEtablissement'))) {
                    $reponseDecouverteEtablissement .= $request->request->get('conseillerCapEmploi_decouvertEtablissement') . " ";
                }
                if (!empty($request->request->get('conseillerPoleEmploi_decouvertEtablissement'))) {
                    $reponseDecouverteEtablissement .= $request->request->get('conseillerPoleEmploi_decouvertEtablissement') . " ";
                }
                if (!empty($request->request->get('autre_decouvertEtablissement'))) {
                    $reponseDecouverteEtablissement .= $request->request->get('autre_decouvertEtablissement') . " ";
                    $reponseDecouverteEtablissement .= "(" . $request->request->get('decouvertEtablissement_autre') . ")";
                }
                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("5 - Vous avez découvert l'établissement par")
                    ->setReponse($reponseDecouverteEtablissement);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("6 - Pré-accueil : J'ai bénéficié d'un pré-accueil ?")
                    ->setReponse($request->request->get('beneficie_preAccueil'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                if ($request->request->get('beneficie_preAccueil') == "Oui") {
                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("6 - Pré-accueil : Je suis satisfait(e) des informations reçues lors du pré-accueil")
                        ->setReponse($request->request->get('information_preAccueil'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("6 - Pré-accueil : Je suis satisfait(e) des échanges avec les professionnels lors du pré-accueil")
                        ->setReponse($request->request->get('echange_preAccueil'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("6 - Pré-accueil : Je suis satisfait(e) de la visite de l’établissement lors du pré accueil")
                        ->setReponse($request->request->get('visite_preAccueil'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("6 - Pré-accueil : Je souhaite apporter les améliorations suivantes")
                        ->setReponse($request->request->get('amelioration_preAccueil'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);
                }

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("7 - Accueil : La communication des documents administratifs réglementaires : J’ai signé un contrat de séjour ?")
                    ->setReponse($request->request->get('contratSejour_accueil'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("7 - Accueil : La communication des documents administratifs réglementaires : J’ai reçu un livret d’accueil de l’établissement ?")
                    ->setReponse($request->request->get('livretAccueil_accueil'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("7 - Accueil : La communication des documents administratifs réglementaires : J’ai reçu un livret de fonctionnement ?")
                    ->setReponse($request->request->get('livretFonctionnement_accueil'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("7 - Accueil : Votre accueil : Je suis satisfait(e) de l’accueil physique reçu de la part des secrétaires")
                    ->setReponse($request->request->get('accueilPhysique_accueil'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("7 - Accueil : Votre accueil : Je suis satisfait(e) de l’accueil téléphonique")
                    ->setReponse($request->request->get('accueilTelephonique_accueil'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("7 - Accueil : Votre accueil : Je suis satisfait(e) des horaires d’ouverture de l’accueil")
                    ->setReponse($request->request->get('horaireOuverture_accueil'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("7 - Accueil : Votre accueil : Je suis satisfait(e) de l’accueil des professionnels")
                    ->setReponse($request->request->get('accueilProfessionnel_accueil'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("7 - Accueil : Votre accueil : Je suis satisfait(e) de l’information reçue sur les règles de vie dans l’établissement")
                    ->setReponse($request->request->get('informationRegleVieEtablissement_accueil'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("7 - Accueil : Votre accueil : Je suis satisfait(e) des informations reçues sur les activités proposées")
                    ->setReponse($request->request->get('informationRegleActivitesProposees_accueil'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("7 - Accueil : Votre accueil : Je souhaite apporter les améliorations suivantes")
                    ->setReponse($request->request->get('amelioration_accueil'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("8 - Le dispositif suivi / La formation suivie : Je suis satisfait(e) de la qualité de la formation/dispositif et de son contenu")
                    ->setReponse($request->request->get('qualiteFormation_dispositifSuivi_formationSuivie'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("8 - Le dispositif suivi / La formation suivie : Je suis satisfait(e) des supports de formation")
                    ->setReponse($request->request->get('supportFormation_dispositifSuivi_formationSuivie'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("8 - Le dispositif suivi / La formation suivie : Je suis satisfait(e) de la maitrise du sujet par le formateur")
                    ->setReponse($request->request->get('maitriseSujetFormateur_dispositifSuivi_formationSuivie'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("8 - Le dispositif suivi / La formation suivie : Je suis satisfait(e) de la clarté des explications du formateur")
                    ->setReponse($request->request->get('clarteExplicationFormateur_dispositifSuivi_formationSuivie'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("8 - Le dispositif suivi / La formation suivie : Je suis satisfait(e) des interventions collectives du formateur")
                    ->setReponse($request->request->get('interventionCollectiveFormateur_dispositifSuivi_formationSuivie'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("8 - Le dispositif suivi / La formation suivie : Je suis satisfait(e) des interventions individuelles du formateur")
                    ->setReponse($request->request->get('interventionIndividuelleFormateur_dispositifSuivi_formationSuivie'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("8 - Le dispositif suivi / La formation suivie : Je suis satisfait(e) du climat favorable de travail et d’échange créé par le formateur")
                    ->setReponse($request->request->get('climatTravail_echangeFormateur_dispositifSuivi_formationSuivie'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("8 - Le dispositif suivi / La formation suivie : Je suis satisfait(e) de la durée de la formation/du dispositif")
                    ->setReponse($request->request->get('dureFormationDispositif_dispositifSuivi_formationSuivie'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("8 - Le dispositif suivi / La formation suivie : Je suis satisfait(e) des conditions matérielles en formation")
                    ->setReponse($request->request->get('conditionMaterielle_dispositifSuivi_formationSuivie'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("8 - Le dispositif suivi / La formation suivie : Je suis satisfait(e) des échanges entre stagiaires")
                    ->setReponse($request->request->get('echangeStagiaire_dispositifSuivi_formationSuivie'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("8 - Le dispositif suivi / La formation suivie : Je suis satisfait(e) de ce choix de formation/de dispositif")
                    ->setReponse($request->request->get('choicFormationDispositif_dispositifSuivi_formationSuivie'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("8 - Le dispositif suivi / La formation suivie : Je suis satisfait(e) de l’accessibilité générale des locaux de formation")
                    ->setReponse($request->request->get('accessibiliteGeneralLocauxFormation_dispositifSuivi_formationSuivie'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("8 - Le dispositif suivi / La formation suivie : Je souhaite apporter les améliorations suivantes")
                    ->setReponse($request->request->get('amelioration_preAccueil'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("9 - Le projet personnalisé : J’ai participé à l’élaboration et à la rédaction du projet personnalisé ?")
                    ->setReponse($request->request->get('elaborationRedaction_projetPersonnalise'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("9 - Le projet personnalisé : J’ai signé un projet personnalisé à chaque actualisation ?")
                    ->setReponse($request->request->get('signeProjetActualisation_projetPersonnalise'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("9 - Le projet personnalisé : Mon projet personnalisé est actualisé régulièrement ?")
                    ->setReponse($request->request->get('projetActualiseRegulierement_projetPersonnalise'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("9 - Le projet personnalisé : Je suis satisfait(e) du suivi de mon projet personnalisé")
                    ->setReponse($request->request->get('suiviProjet_projetPersonnalise'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("9 - Le projet personnalisé : Je suis satisfait(e) de l’accompagnement du référent de parcours dans le suivi de mon projet personnalisé")
                    ->setReponse($request->request->get('accompagnementReferent_projetPersonnalise'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("10 - L’accompagnement par l’assistante sociale : J’ai connaissance de la présence de l’assistante sociale dans l’établissement dès mon arrivée ?")
                    ->setReponse($request->request->get('connaissancePresence_accompagnement_assistanceSociale'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("10 - L’accompagnement par l’assistante sociale : J’ai bénéficié d’un accompagnement de l’assistante sociale ?")
                    ->setReponse($request->request->get('beneficieAccompagnement_accompagnement_assistanceSociale'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                if ($request->request->get('beneficieAccompagnement_accompagnement_assistanceSociale') == "Oui") {
                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("10 - L’accompagnement par l’assistante sociale : Je suis satisfait(e) de l’accompagnement réalisé par l’assistante sociale")
                        ->setReponse($request->request->get('accompagnementRealise_accompagnement_assistanceSociale'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("10 - L’accompagnement par l’assistante sociale : Je suis satisfait(e) de l’écoute de l’assistante sociale")
                        ->setReponse($request->request->get('ecouteAssistanceSociale_accompagnement_assistanceSociale'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("10 - L’accompagnement par l’assistante sociale : Je suis satisfait(e) de la clarté des explications de l’assistante sociale")
                        ->setReponse($request->request->get('clarteExplications_accompagnement_assistanceSociale'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("10 - L’accompagnement par l’assistante sociale : Je suis satisfait(e) des délais pour les rendez-vous auprès de l’assistante sociale")
                        ->setReponse($request->request->get('delaisRDV_accompagnement_assistanceSociale'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("10 - L’accompagnement par l’assistante sociale : Je souhaite apporter les améliorations suivantes")
                        ->setReponse($request->request->get('amelioration_accompagnement_assistanceSociale'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);
                }

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("11 - L’accompagnement par la psychologue clinicienne : J’ai connaissance de la présence de la psychologue dans l’établissement dès mon arrivée ?")
                    ->setReponse($request->request->get('connaissancePresence_accompagnement_psychologueClinicienne'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("11 - L’accompagnement par la psychologue clinicienne : J’ai bénéficié d’un accompagnement de la psychologue clinicienne ?")
                    ->setReponse($request->request->get('beneficieAccompagnement_accompagnement_psychologueClinicienne'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                if ($request->request->get('beneficieAccompagnement_accompagnement_psychologueClinicienne') == "Oui") {
                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("11 - L’accompagnement par la psychologue clinicienne : Je suis satisfait(e) de l’accompagnement réalisé par la psychologue clinicienne")
                        ->setReponse($request->request->get('accompagnementRealise_accompagnement_psychologueClinicienne'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("11 - L’accompagnement par la psychologue clinicienne : Je suis satisfait(e) de l’écoute de la psychologue clinicienne")
                        ->setReponse($request->request->get('ecoutePsychologueClinicienne_accompagnement_psychologueClinicienne'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("11 - L’accompagnement par la psychologue clinicienne : Je suis satisfait(e) de la clarté des explications de la psychologue clinicienne")
                        ->setReponse($request->request->get('clarteExplications_accompagnement_psychologueClinicienne'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("11 - L’accompagnement par la psychologue clinicienne : Je suis satisfait(e) des délais pour les rendez-vous auprès de la psychologue clinicienne")
                        ->setReponse($request->request->get('delaisRDV_accompagnement_psychologueClinicienne'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("11 - L’accompagnement par la psychologue clinicienne : Je souhaite apporter les améliorations suivantes")
                        ->setReponse($request->request->get('amelioration_accompagnement_psychologueClinicienne'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);
                }

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("12 - L’accompagnement par la psychologue du travail : J’ai connaissance de la présence de la psychologue du travail dans l’établissement dès mon arrivée ?")
                    ->setReponse($request->request->get('connaissancePresence_accompagnement_psychologueTravail'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("12 - L’accompagnement par la psychologue du travail : J’ai bénéficié d’un accompagnement de la psychologue du travail ?")
                    ->setReponse($request->request->get('beneficieAccompagnement_accompagnement_psychologueTravail'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                if ($request->request->get('beneficieAccompagnement_accompagnement_psychologueTravail') == "Oui") {
                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("12 - L’accompagnement par la psychologue du travail : Je suis satisfait(e) du suivi réalisé par la psychologue du travail")
                        ->setReponse($request->request->get('accompagnementRealise_accompagnement_psychologueTravail'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("12 - L’accompagnement par la psychologue du travail : Je suis satisfait(e) de l’écoute de la psychologue du travail")
                        ->setReponse($request->request->get('ecoutePsychologueTravail_accompagnement_psychologueTravail'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("12 - L’accompagnement par la psychologue du travail : Je suis satisfait(e) de la clarté des explications de la psychologue du travail")
                        ->setReponse($request->request->get('clarteExplications_accompagnement_psychologueTravail'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("12 - L’accompagnement par la psychologue du travail : Je suis satisfait(e) des délais pour les rendez-vous auprès de la psychologue du travail")
                        ->setReponse($request->request->get('delaisRDV_accompagnement_psychologueTravail'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("12 - L’accompagnement par la psychologue du travail : Je souhaite apporter les améliorations suivantes")
                        ->setReponse($request->request->get('amelioration_accompagnement_psychologueTravail'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);
                }

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("13 - L’accompagnement par le conseiller en insertion professionnel : J’ai connaissance de la présence du conseiller en insertion professionnel dans l’établissement dès mon arrivée ?")
                    ->setReponse($request->request->get('connaissancePresence_accompagnement_psychologueProfessionnel'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("13 - L’accompagnement par le conseiller en insertion professionnel : J’ai bénéficié d’un accompagnement du conseiller en insertion professionnel ?")
                    ->setReponse($request->request->get('beneficieAccompagnement_accompagnement_psychologueProfessionnel'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                if ($request->request->get('beneficieAccompagnement_accompagnement_psychologueProfessionnel') == "Oui") {
                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("13 - L’accompagnement par le conseiller en insertion professionnel : Je suis satisfait(e) de l’accompagnement réalisé par le conseiller en insertion professionnel")
                        ->setReponse($request->request->get('accompagnementRealise_accompagnement_psychologueProfessionnel'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("13 - L’accompagnement par le conseiller en insertion professionnel : Je suis satisfait(e) de l’écoute du conseiller en insertion professionnel")
                        ->setReponse($request->request->get('ecoutePsychologueProfessionnel_accompagnement_psychologueProfessionnel'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("13 - L’accompagnement par le conseiller en insertion professionnel : Je suis satisfait(e) de la clarté des explications du conseiller en insertion professionnel")
                        ->setReponse($request->request->get('clarteExplications_accompagnement_psychologueProfessionnel'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("13 - L’accompagnement par le conseiller en insertion professionnel : Je suis satisfait(e) des délais pour les rendez-vous auprès du conseiller en insertion professionnel")
                        ->setReponse($request->request->get('delaisRDV_accompagnement_psychologueProfessionnel'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("13 - L’accompagnement par le conseiller en insertion professionnel : Je suis satisfait(e) des informations délivrées par le conseiller en insertion professionnel pour la recherche de stage et / ou recherche d’emploi")
                        ->setReponse($request->request->get('informationDelivree_accompagnement_psychologueProfessionnel'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("13 - L’accompagnement par le conseiller en insertion professionnel : Je suis satisfait(e) des interventions collectives délivrées par le conseiller en insertion professionnel")
                        ->setReponse($request->request->get('interventionCollective_accompagnement_psychologueProfessionnel'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("13 - L’accompagnement par le conseiller en insertion professionnel : Je souhaite apporter les améliorations suivantes")
                        ->setReponse($request->request->get('amelioration_accompagnement_psychologueProfessionnel'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);
                }

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("14 - L’accompagnement par le référent de parcours : J’ai connaissance de la présence du référent de parcours dans l’établissement dès mon arrivée ?")
                    ->setReponse($request->request->get('connaissancePresence_accompagnement_referentParcours'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("14 - L’accompagnement par le référent de parcours : J’ai bénéficié d’un accompagnement par le référent de parcours ?")
                    ->setReponse($request->request->get('beneficieAccompagnement_accompagnement_referentParcours'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                if ($request->request->get('beneficieAccompagnement_accompagnement_referentParcours') == "Oui") {
                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("14 - L’accompagnement par le référent de parcours : Je suis satisfait(e) du suivi du projet personnalisé réalisé par le référent de parcours")
                        ->setReponse($request->request->get('accompagnementRealise_accompagnement_referentParcours'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("14 - L’accompagnement par le référent de parcours : Je suis satisfait(e) de l’écoute du référent de parcours")
                        ->setReponse($request->request->get('ecouteReferentParcours_accompagnement_referentParcours'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("14 - L’accompagnement par le référent de parcours : Je suis satisfait(e) de la clarté des explications du référent de parcours")
                        ->setReponse($request->request->get('clarteExplications_accompagnement_referentParcours'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("14 - L’accompagnement par le référent de parcours : Je suis satisfait(e) des délais pour les rendez-vous auprès du référent de parcours")
                        ->setReponse($request->request->get('delaisRDV_accompagnement_referentParcours'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("14 - L’accompagnement par le référent de parcours : Je souhaite apporter les améliorations suivantes")
                        ->setReponse($request->request->get('amelioration_accompagnement_referentParcours'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);
                }

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("15 - L’accompagnement par l’infirmière : J’ai connaissance de la présence de l’infirmière dans l’établissement dès mon arrivée ?")
                    ->setReponse($request->request->get('connaissancePresence_accompagnement_infirmiere'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("15 - L’accompagnement par l’infirmière : J’ai bénéficié d’un accompagnement par l’infirmière ?")
                    ->setReponse($request->request->get('beneficieAccompagnement_accompagnement_infirmiere'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                if ($request->request->get('beneficieAccompagnement_accompagnement_infirmiere') == "Oui") {
                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("15 - L’accompagnement par l’infirmière : Je suis satisfait(e) de l’accompagnement réalisé par l’infirmière")
                        ->setReponse($request->request->get('accompagnementRealise_accompagnement_infirmiere'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("15 - L’accompagnement par l’infirmière : Je suis satisfait(e) de l’écoute de l’infirmière")
                        ->setReponse($request->request->get('ecouteInfirmiere_accompagnement_infirmiere'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("15 - L’accompagnement par l’infirmière : Je suis satisfait(e) de la clarté des explications de l’infirmière")
                        ->setReponse($request->request->get('clarteExplications_accompagnement_infirmiere'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("15 - L’accompagnement par l’infirmière : Je suis satisfait(e) des délais pour les rendez-vous auprès de l’infirmière")
                        ->setReponse($request->request->get('delaisRDV_accompagnement_infirmiere'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("15 - L’accompagnement par l’infirmière : Je souhaite apporter les améliorations suivantes")
                        ->setReponse($request->request->get('amelioration_accompagnement_infirmiere'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);
                }

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("16 - L’accompagnement par le médecin : J’ai bénéficié d’un accompagnement du médecin dans le cadre d’une évaluation des aptitudes médicales pour mon projet professionnel ?")
                    ->setReponse($request->request->get('beneficieAccompagnement_accompagnement_medecin'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                if ($request->request->get('beneficieAccompagnement_accompagnement_medecin') == "Oui") {
                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("16 - L’accompagnement par le médecin : Je suis satisfait(e) de l’écoute du médecin lors du rendez-vous")
                        ->setReponse($request->request->get('ecouteRDV_accompagnement_medecin'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("16 - L’accompagnement par le médecin : Je suis satisfait(e) de la clarté des explications du médecin")
                        ->setReponse($request->request->get('clarteExplication_accompagnement_medecin'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("16 - L’accompagnement par le médecin : Je souhaite apporter les améliorations suivantes")
                        ->setReponse($request->request->get('amelioration_accompagnement_medecin'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);
                }

                if ($request->request->get('regime') == "Interne") {
                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("17 - L’Hébergement : Je suis satisfait(e) de ma chambre")
                        ->setReponse($request->request->get('chambre_hebergement'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("17 - L’Hébergement : Je suis satisfait(e) de la prestation ménage dans ma chambre")
                        ->setReponse($request->request->get('prestationMenageChambre_hebergement'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("17 - L’Hébergement : Je suis satisfait(e) de la prestation ménage à l’hébergement")
                        ->setReponse($request->request->get('prestationMenageHebergement_hebergement'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("17 - L’Hébergement : Je suis satisfait(e) des interventions demandées et réalisées par les agents d’entretien")
                        ->setReponse($request->request->get('interventionDemandeeRealisee_hebergement'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("17 - L’Hébergement : Je suis satisfait(e) de la salle informatique de l’hébergement")
                        ->setReponse($request->request->get('salleInformatique_hebergement'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("17 - L’Hébergement : Je suis satisfait(e) des espaces collectifs de l’hébergement")
                        ->setReponse($request->request->get('espaceCollectif_hebergement'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("17 - L’Hébergement : Je suis satisfait(e) du wifi à l’hébergement")
                        ->setReponse($request->request->get('wifi_hebergement'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("17 - L’Hébergement : Je suis satisfait(e) des échanges avec les surveillants en semaine")
                        ->setReponse($request->request->get('echangesSurveillantsSemaine_hebergement'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("17 - L’Hébergement : Je suis satisfait(e) des échanges avec les surveillants le week-end")
                        ->setReponse($request->request->get('echangeSurveillantsWeekEnd_hebergement'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("17 - L’Hébergement : Je souhaite apporter les améliorations suivantes")
                        ->setReponse($request->request->get('amelioration_hebergement'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);
                }

                if ($request->request->get('regime') == "Interne" || $request->request->get('regime') == "Demi-pensionnaire") {
                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("18 - La restauration : Je suis satisfait(e) de la salle de restauration")
                        ->setReponse($request->request->get('salle_restauration'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("18 - La restauration : Je suis satisfait(e) de la salle de restauration du weekend")
                        ->setReponse($request->request->get('salleWeekEnd_restauration'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("18 - La restauration : Je suis satisfait(e) du suivi réalisé par les cuisines de mon régime alimentaire")
                        ->setReponse($request->request->get('suiviRealiseRegimeAlimentaire_restauration'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("17 - L’Hébergement : Je suis satisfait(e) de la qualité de la restauration")
                        ->setReponse($request->request->get('qualite_restauration'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("18 - La restauration : Je suis satisfait(e) des quantités servies")
                        ->setReponse($request->request->get('quantiteServie_restauration'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("18 - La restauration : Je suis satisfait(e) de l’horaire des repas")
                        ->setReponse($request->request->get('horaireRepas_restauration'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("18 - La restauration : Je suis satisfait(e) de la variété des menus")
                        ->setReponse($request->request->get('varieteMenu_restauration'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("18 - La restauration : Je souhaite apporter les améliorations suivantes")
                        ->setReponse($request->request->get('amelioration_restauration'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);
                }

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("19 - Le transport : Je suis satisfait(e) de la prestation transport que j’ai utilisée")
                    ->setReponse($request->request->get('prestation_transport'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("19 - Le transport : Je souhaite apporter les améliorations suivantes")
                    ->setReponse($request->request->get('amelioration_transport'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("20 - La sécurité : J’ai bénéficié d’une formation incendie ?")
                    ->setReponse($request->request->get('prestation_transport'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                if ($request->request->get('prestation_transport') == "Oui") {
                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("20 - La sécurité : Je suis satisfait(e) de la formation incendie dispensée par le référent qualité / sécurité")
                        ->setReponse($request->request->get('amelioration_transport'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);

                    $question = new Questionnaire();
                    $question->setCode($code)
                        ->setDateRealisation(new \DateTime())
                        ->setQuestion("20 - La sécurité : Je souhaite apporter les améliorations suivantes")
                        ->setReponse($request->request->get('amelioration_transport'));
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($question);
                }

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("21 - Le Conseil de la Vie Sociale : J’ai identifié les membres du CVS ?")
                    ->setReponse($request->request->get('identifieMembre_conseilVieSociale'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("21 - Le Conseil de la Vie Sociale : Les représentants du Conseil de la Vie Sociale me consultent avant les réunions.")
                    ->setReponse($request->request->get('representantConsultation_conseilVieSociale'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("21 - Le Conseil de la Vie Sociale : Les représentants du Conseil de la Vie Sociale me rapportent ce qui a été dit en réunion.")
                    ->setReponse($request->request->get('representantRapporter_conseilVieSociale'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("21 - Le Conseil de la Vie Sociale : Je souhaite apporter les améliorations suivantes")
                    ->setReponse($request->request->get('amelioration_conseilVieSociale'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("22 - Préparation de la sortie du centre : Je suis satisfait(e) des informations reçues pour me préparer à la sortie du centre")
                    ->setReponse($request->request->get('amelioration_conseilVieSociale'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("22 - Préparation de la sortie du centre : Je souhaite apporter les améliorations suivantes")
                    ->setReponse($request->request->get('amelioration_preparationSortieCentre'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("23 - SactifactionGenerale : Je suis satisfait(e) d’une manière générale du centre Les Rhuets")
                    ->setReponse($request->request->get('maniereGeneral_satisfactionGenerale'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $question = new Questionnaire();
                $question->setCode($code)
                    ->setDateRealisation(new \DateTime())
                    ->setQuestion("23 - SactifactionGenerale : Je recommanderai l’établissement ?")
                    ->setReponse($request->request->get('recommenderai_satisfactionGenerale'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($question);

                $entityManager->flush();
            }
        }
        if($formulaireValider){
            $this->get('session')->set('finalisation', true);
            return $this->redirectToRoute('finalisation');
        }
        return $this->render('question/home.html.twig',[
            'dateActuelle' => new \DateTime(),
            'questionnaireActif' => true
        ]);
    }

    /**
     * @Route("/finalisation", name="finalisation");
     */
    public function finalisation(Request $request) : Response
    {
        if( !$this->get('session')->get('finalisation') && $request->isMethod('GET')){
            return $this->redirectToRoute('home');
        }
        $this->get('session')->set('finalisation', false);
        return $this->render('question/finalisation.html.twig',[
            'dateActuelle' => new \DateTime(),
            'questionnaireActif' => false
        ]);
    }

    public function verifFormulaire(Request $request)
    {
        $age = $request->request->get('age');
        if(!empty($age)){
            $formationDispositifs = $request->request->get('formations_dispositifs');
            if(!empty($formationDispositifs)){
                $departementOrigine = $request->request->get('departement_origine');
                $numDepartement = $request->request->get('num_departement');
                if(!empty($departementOrigine) && !empty($numDepartement)){
                    $regime = $request->request->get('regime');
                    if(!empty($regime)){
                        $MDPH_decouvertEtablissement = $request->request->get('MDPH_decouvertEtablissement');
                        $internet_decouvertEtablissement = $request->request->get('internet_decouvertEtablissement');
                        $reseauPersonnel_decouvertEtablissement = $request->request->get('reseauPersonnel_decouvertEtablissement');
                        $conseillerCapEmploi_decouvertEtablissement = $request->request->get('conseillerCapEmploi_decouvertEtablissement');
                        $conseillerPoleEmploi_decouvertEtablissement = $request->request->get('conseillerPoleEmploi_decouvertEtablissement');
                        $autre_decouvertEtablissement = $request->request->get('autre_decouvertEtablissement');
                        $decouvertEtablissement_autre_input = $request->request->get('decouvertEtablissement_autre_input');
                        if(
                            !empty($MDPH_decouvertEtablissement) ||
                            !empty($internet_decouvertEtablissement) ||
                            !empty($reseauPersonnel_decouvertEtablissement) ||
                            !empty($conseillerCapEmploi_decouvertEtablissement) ||
                            !empty($conseillerPoleEmploi_decouvertEtablissement) ||
                            (
                                !empty($autre_decouvertEtablissement) &&
                                !empty($decouvertEtablissement_autre_input)
                            )
                        ){
                            $beneficie_preAccueil = $request->request->get('beneficie_preAccueil');
                            if(!empty($beneficie_preAccueil)){
                                $continue = true;
                                if($beneficie_preAccueil == "Oui"){
                                    $continue = false;
                                    $information_preAccueil = $request->request->get('information_preAccueil');
                                    $echange_preAccueil = $request->request->get('echange_preAccueil');
                                    $visite_preAccueil = $request->request->get('visite_preAccueil');
                                    if(
                                        !empty($information_preAccueil) &&
                                        !empty($echange_preAccueil) &&
                                        !empty($visite_preAccueil)
                                    ){
                                        $continue = true;
                                    }
                                }
                                if($continue){
                                    $contratSejour_accueil = $request->request->get('contratSejour_accueil');
                                    $livretAccueil_accueil = $request->request->get('livretAccueil_accueil');
                                    $livretFonctionnement_accueil = $request->request->get('livretFonctionnement_accueil');
                                    $accueilPhysique_accueil = $request->request->get('accueilPhysique_accueil');
                                    $accueilTelephonique_accueil = $request->request->get('accueilTelephonique_accueil');
                                    $horaireOuverture_accueil = $request->request->get('horaireOuverture_accueil');
                                    $accueilProfessionnel_accueil = $request->request->get('accueilProfessionnel_accueil');
                                    $informationRegleVieEtablissement_accueil = $request->request->get('informationRegleVieEtablissement_accueil');
                                    $informationRegleActivitesProposees_accueil = $request->request->get('informationRegleActivitesProposees_accueil');
                                    if(
                                        !empty($contratSejour_accueil) &&
                                        !empty($livretAccueil_accueil) &&
                                        !empty($livretFonctionnement_accueil) &&
                                        !empty($accueilPhysique_accueil) &&
                                        !empty($accueilTelephonique_accueil) &&
                                        !empty($horaireOuverture_accueil) &&
                                        !empty($accueilProfessionnel_accueil) &&
                                        !empty($informationRegleVieEtablissement_accueil) &&
                                        !empty($informationRegleActivitesProposees_accueil)

                                    ){
                                        $qualiteFormation_dispositifSuivi_formationSuivie = $request->request->get('qualiteFormation_dispositifSuivi_formationSuivie');
                                        $supportFormation_dispositifSuivi_formationSuivie = $request->request->get('supportFormation_dispositifSuivi_formationSuivie');
                                        $maitriseSujetFormateur_dispositifSuivi_formationSuivie = $request->request->get('maitriseSujetFormateur_dispositifSuivi_formationSuivie');
                                        $clarteExplicationFormateur_dispositifSuivi_formationSuivie = $request->request->get('clarteExplicationFormateur_dispositifSuivi_formationSuivie');
                                        $interventionCollectiveFormateur_dispositifSuivi_formationSuivie = $request->request->get('interventionCollectiveFormateur_dispositifSuivi_formationSuivie');
                                        $interventionIndividuelleFormateur_dispositifSuivi_formationSuivie = $request->request->get('interventionIndividuelleFormateur_dispositifSuivi_formationSuivie');
                                        $climatTravail_echangeFormateur_dispositifSuivi_formationSuivie = $request->request->get('climatTravail_echangeFormateur_dispositifSuivi_formationSuivie');
                                        $dureFormationDispositif_dispositifSuivi_formationSuivie = $request->request->get('dureFormationDispositif_dispositifSuivi_formationSuivie');
                                        $conditionMaterielle_dispositifSuivi_formationSuivie = $request->request->get('conditionMaterielle_dispositifSuivi_formationSuivie');
                                        $echangeStagiaire_dispositifSuivi_formationSuivie = $request->request->get('echangeStagiaire_dispositifSuivi_formationSuivie');
                                        $choicFormationDispositif_dispositifSuivi_formationSuivie = $request->request->get('choicFormationDispositif_dispositifSuivi_formationSuivie');
                                        $accessibiliteGeneralLocauxFormation_dispositifSuivi_formationSuivie = $request->request->get('accessibiliteGeneralLocauxFormation_dispositifSuivi_formationSuivie');
                                        if(
                                            !empty($qualiteFormation_dispositifSuivi_formationSuivie) &&
                                            !empty($supportFormation_dispositifSuivi_formationSuivie) &&
                                            !empty($maitriseSujetFormateur_dispositifSuivi_formationSuivie) &&
                                            !empty($clarteExplicationFormateur_dispositifSuivi_formationSuivie) &&
                                            !empty($interventionCollectiveFormateur_dispositifSuivi_formationSuivie) &&
                                            !empty($interventionIndividuelleFormateur_dispositifSuivi_formationSuivie) &&
                                            !empty($climatTravail_echangeFormateur_dispositifSuivi_formationSuivie) &&
                                            !empty($dureFormationDispositif_dispositifSuivi_formationSuivie) &&
                                            !empty($conditionMaterielle_dispositifSuivi_formationSuivie) &&
                                            !empty($echangeStagiaire_dispositifSuivi_formationSuivie) &&
                                            !empty($choicFormationDispositif_dispositifSuivi_formationSuivie) &&
                                            !empty($accessibiliteGeneralLocauxFormation_dispositifSuivi_formationSuivie)
                                        ){
                                            $elaborationRedaction_projetPersonnalise = $request->request->get('elaborationRedaction_projetPersonnalise');
                                            $signeProjetActualisation_projetPersonnalise = $request->request->get('signeProjetActualisation_projetPersonnalise');
                                            $projetActualiseRegulierement_projetPersonnalise = $request->request->get('projetActualiseRegulierement_projetPersonnalise');
                                            $suiviProjet_projetPersonnalise = $request->request->get('suiviProjet_projetPersonnalise');
                                            $accompagnementReferent_projetPersonnalise = $request->request->get('accompagnementReferent_projetPersonnalise');
                                            if(
                                                !empty($elaborationRedaction_projetPersonnalise) &&
                                                !empty($signeProjetActualisation_projetPersonnalise) &&
                                                !empty($projetActualiseRegulierement_projetPersonnalise) &&
                                                !empty($suiviProjet_projetPersonnalise) &&
                                                !empty($accompagnementReferent_projetPersonnalise)
                                            ){
                                                $connaissancePresence_accompagnement_assistanceSociale = $request->request->get('connaissancePresence_accompagnement_assistanceSociale');
                                                $beneficieAccompagnement_accompagnement_assistanceSociale = $request->request->get('beneficieAccompagnement_accompagnement_assistanceSociale');
                                                if(
                                                    !empty($connaissancePresence_accompagnement_assistanceSociale) &&
                                                    !empty($beneficieAccompagnement_accompagnement_assistanceSociale)
                                                ){
                                                    $continue = true;
                                                    if($beneficieAccompagnement_accompagnement_assistanceSociale == "Oui"){
                                                        $continue = false;
                                                        $accompagnementRealise_accompagnement_assistanceSociale = $request->request->get('accompagnementRealise_accompagnement_assistanceSociale');
                                                        $ecouteAssistanceSociale_accompagnement_assistanceSociale = $request->request->get('ecouteAssistanceSociale_accompagnement_assistanceSociale');
                                                        $clarteExplications_accompagnement_assistanceSociale = $request->request->get('clarteExplications_accompagnement_assistanceSociale');
                                                        $delaisRDV_accompagnement_assistanceSociale = $request->request->get('delaisRDV_accompagnement_assistanceSociale');
                                                        if(
                                                            !empty($accompagnementRealise_accompagnement_assistanceSociale) &&
                                                            !empty($ecouteAssistanceSociale_accompagnement_assistanceSociale) &&
                                                            !empty($clarteExplications_accompagnement_assistanceSociale) &&
                                                            !empty($delaisRDV_accompagnement_assistanceSociale)
                                                        ){
                                                            $continue = true;
                                                        }
                                                    }
                                                    if($continue){
                                                        $connaissancePresence_accompagnement_psychologueClinicienne = $request->request->get('connaissancePresence_accompagnement_psychologueClinicienne');
                                                        $beneficieAccompagnement_accompagnement_psychologueClinicienne = $request->request->get('beneficieAccompagnement_accompagnement_psychologueClinicienne');
                                                        if(
                                                            !empty($connaissancePresence_accompagnement_psychologueClinicienne) &&
                                                            !empty($beneficieAccompagnement_accompagnement_psychologueClinicienne)
                                                        ) {
                                                            $continue = true;
                                                            if ($beneficieAccompagnement_accompagnement_psychologueClinicienne == "Oui") {
                                                                $continue = false;
                                                                $accompagnementRealise_accompagnement_psychologueClinicienne = $request->request->get('accompagnementRealise_accompagnement_psychologueClinicienne');
                                                                $ecoutePsychologueClinicienne_accompagnement_psychologueClinicienne = $request->request->get('ecoutePsychologueClinicienne_accompagnement_psychologueClinicienne');
                                                                $clarteExplications_accompagnement_psychologueClinicienne = $request->request->get('clarteExplications_accompagnement_psychologueClinicienne');
                                                                $delaisRDV_accompagnement_psychologueClinicienne = $request->request->get('delaisRDV_accompagnement_assistanceSociale');
                                                                if (
                                                                    !empty($accompagnementRealise_accompagnement_psychologueClinicienne) &&
                                                                    !empty($ecoutePsychologueClinicienne_accompagnement_psychologueClinicienne) &&
                                                                    !empty($clarteExplications_accompagnement_psychologueClinicienne) &&
                                                                    !empty($delaisRDV_accompagnement_psychologueClinicienne)
                                                                ) {
                                                                    $continue = true;
                                                                }
                                                            }
                                                            if ($continue) {
                                                                $connaissancePresence_accompagnement_psychologueTravail = $request->request->get('connaissancePresence_accompagnement_psychologueTravail');
                                                                $beneficieAccompagnement_accompagnement_psychologueTravail = $request->request->get('beneficieAccompagnement_accompagnement_psychologueTravail');
                                                                if(
                                                                    !empty($connaissancePresence_accompagnement_psychologueTravail) &&
                                                                    !empty($beneficieAccompagnement_accompagnement_psychologueTravail)
                                                                ) {
                                                                    $continue = true;
                                                                    if ($beneficieAccompagnement_accompagnement_psychologueTravail == "Oui") {
                                                                        $continue = false;
                                                                        $accompagnementRealise_accompagnement_psychologueTravail = $request->request->get('accompagnementRealise_accompagnement_psychologueTravail');
                                                                        $ecoutePsychologueTravail_accompagnement_psychologueTravail = $request->request->get('ecoutePsychologueTravail_accompagnement_psychologueTravail');
                                                                        $clarteExplications_accompagnement_psychologueTravail = $request->request->get('clarteExplications_accompagnement_psychologueTravail');
                                                                        $delaisRDV_accompagnement_psychologueTravail = $request->request->get('delaisRDV_accompagnement_assistanceSociale');
                                                                        if (
                                                                            !empty($accompagnementRealise_accompagnement_psychologueTravail) &&
                                                                            !empty($ecoutePsychologueTravail_accompagnement_psychologueTravail) &&
                                                                            !empty($clarteExplications_accompagnement_psychologueTravail) &&
                                                                            !empty($delaisRDV_accompagnement_psychologueTravail)
                                                                        ) {
                                                                            $continue = true;
                                                                        }
                                                                    }
                                                                    if ($continue) {
                                                                        $connaissancePresence_accompagnement_psychologueProfessionnel = $request->request->get('connaissancePresence_accompagnement_psychologueProfessionnel');
                                                                        $beneficieAccompagnement_accompagnement_psychologueProfessionnel = $request->request->get('beneficieAccompagnement_accompagnement_psychologueProfessionnel');
                                                                        if(
                                                                            !empty($connaissancePresence_accompagnement_psychologueProfessionnel) &&
                                                                            !empty($beneficieAccompagnement_accompagnement_psychologueProfessionnel)
                                                                        ) {
                                                                            $continue = true;
                                                                            if ($beneficieAccompagnement_accompagnement_psychologueProfessionnel == "Oui") {
                                                                                $continue = false;
                                                                                $accompagnementRealise_accompagnement_psychologueProfessionnel = $request->request->get('accompagnementRealise_accompagnement_psychologueProfessionnel');
                                                                                $ecoutePsychologueProfessionnel_accompagnement_psychologueProfessionnel = $request->request->get('ecoutePsychologueProfessionnel_accompagnement_psychologueProfessionnel');
                                                                                $clarteExplications_accompagnement_psychologueProfessionnel = $request->request->get('clarteExplications_accompagnement_psychologueProfessionnel');
                                                                                $delaisRDV_accompagnement_psychologueProfessionnel = $request->request->get('delaisRDV_accompagnement_psychologueProfessionnel');
                                                                                $informationDelivree_accompagnement_psychologueProfessionnel = $request->request->get('informationDelivree_accompagnement_psychologueProfessionnel');
                                                                                $interventionCollective_accompagnement_psychologueProfessionnel = $request->request->get('interventionCollective_accompagnement_psychologueProfessionnel');
                                                                                if (
                                                                                    !empty($accompagnementRealise_accompagnement_psychologueProfessionnel) &&
                                                                                    !empty($ecoutePsychologueProfessionnel_accompagnement_psychologueProfessionnel) &&
                                                                                    !empty($clarteExplications_accompagnement_psychologueProfessionnel) &&
                                                                                    !empty($delaisRDV_accompagnement_psychologueProfessionnel) &&
                                                                                    !empty($informationDelivree_accompagnement_psychologueProfessionnel) &&
                                                                                    !empty($interventionCollective_accompagnement_psychologueProfessionnel)
                                                                                ) {
                                                                                    $continue = true;
                                                                                }
                                                                            }
                                                                            if ($continue) {
                                                                                $connaissancePresence_accompagnement_referentParcours = $request->request->get('connaissancePresence_accompagnement_referentParcours');
                                                                                $beneficieAccompagnement_accompagnement_referentParcours = $request->request->get('beneficieAccompagnement_accompagnement_referentParcours');
                                                                                if(
                                                                                    !empty($connaissancePresence_accompagnement_referentParcours) &&
                                                                                    !empty($beneficieAccompagnement_accompagnement_referentParcours)
                                                                                ) {
                                                                                    $continue = true;
                                                                                    if ($beneficieAccompagnement_accompagnement_referentParcours == "Oui") {
                                                                                        $continue = false;
                                                                                        $accompagnementRealise_accompagnement_referentParcours = $request->request->get('accompagnementRealise_accompagnement_referentParcours');
                                                                                        $ecouteReferentParcours_accompagnement_referentParcours = $request->request->get('ecouteReferentParcours_accompagnement_referentParcours');
                                                                                        $clarteExplications_accompagnement_referentParcours = $request->request->get('clarteExplications_accompagnement_referentParcours');
                                                                                        $delaisRDV_accompagnement_referentParcours = $request->request->get('delaisRDV_accompagnement_referentParcours');
                                                                                        if (
                                                                                            !empty($accompagnementRealise_accompagnement_referentParcours) &&
                                                                                            !empty($ecouteReferentParcours_accompagnement_referentParcours) &&
                                                                                            !empty($clarteExplications_accompagnement_referentParcours) &&
                                                                                            !empty($delaisRDV_accompagnement_referentParcours)
                                                                                        ) {
                                                                                            $continue = true;
                                                                                        }
                                                                                    }
                                                                                    if ($continue) {
                                                                                        $connaissancePresence_accompagnement_infirmiere = $request->request->get('connaissancePresence_accompagnement_infirmiere');
                                                                                        $beneficieAccompagnement_accompagnement_infirmiere = $request->request->get('beneficieAccompagnement_accompagnement_infirmiere');
                                                                                        if(
                                                                                            !empty($connaissancePresence_accompagnement_infirmiere) &&
                                                                                            !empty($beneficieAccompagnement_accompagnement_infirmiere)
                                                                                        ) {
                                                                                            $continue = true;
                                                                                            if ($beneficieAccompagnement_accompagnement_infirmiere == "Oui") {
                                                                                                $continue = false;
                                                                                                $accompagnementRealise_accompagnement_infirmiere = $request->request->get('accompagnementRealise_accompagnement_infirmiere');
                                                                                                $ecouteInfirmiere_accompagnement_infirmiere = $request->request->get('ecouteInfirmiere_accompagnement_infirmiere');
                                                                                                $clarteExplications_accompagnement_infirmiere = $request->request->get('clarteExplications_accompagnement_infirmiere');
                                                                                                $delaisRDV_accompagnement_infirmiere = $request->request->get('delaisRDV_accompagnement_infirmiere');
                                                                                                if (
                                                                                                    !empty($accompagnementRealise_accompagnement_infirmiere) &&
                                                                                                    !empty($ecouteInfirmiere_accompagnement_infirmiere) &&
                                                                                                    !empty($clarteExplications_accompagnement_infirmiere) &&
                                                                                                    !empty($delaisRDV_accompagnement_infirmiere)
                                                                                                ) {
                                                                                                    $continue = true;
                                                                                                }
                                                                                            }
                                                                                            if ($continue) {
                                                                                                $connaissancePresence_accompagnement_infirmiere = $request->request->get('connaissancePresence_accompagnement_infirmiere');
                                                                                                $beneficieAccompagnement_accompagnement_infirmiere = $request->request->get('beneficieAccompagnement_accompagnement_infirmiere');
                                                                                                if(
                                                                                                    !empty($connaissancePresence_accompagnement_infirmiere) &&
                                                                                                    !empty($beneficieAccompagnement_accompagnement_infirmiere)
                                                                                                ) {
                                                                                                    $continue = true;
                                                                                                    if ($beneficieAccompagnement_accompagnement_infirmiere == "Oui") {
                                                                                                        $continue = false;
                                                                                                        $accompagnementRealise_accompagnement_infirmiere = $request->request->get('accompagnementRealise_accompagnement_infirmiere');
                                                                                                        $ecouteInfirmiere_accompagnement_infirmiere = $request->request->get('ecouteInfirmiere_accompagnement_infirmiere');
                                                                                                        $clarteExplications_accompagnement_infirmiere = $request->request->get('clarteExplications_accompagnement_infirmiere');
                                                                                                        $delaisRDV_accompagnement_infirmiere = $request->request->get('delaisRDV_accompagnement_infirmiere');
                                                                                                        if (
                                                                                                            !empty($accompagnementRealise_accompagnement_infirmiere) &&
                                                                                                            !empty($ecouteInfirmiere_accompagnement_infirmiere) &&
                                                                                                            !empty($clarteExplications_accompagnement_infirmiere) &&
                                                                                                            !empty($delaisRDV_accompagnement_infirmiere)
                                                                                                        ) {
                                                                                                            $continue = true;
                                                                                                        }
                                                                                                    }
                                                                                                    if ($continue) {
                                                                                                        $beneficieAccompagnement_accompagnement_medecin = $request->request->get('beneficieAccompagnement_accompagnement_medecin');
                                                                                                        if(
                                                                                                            !empty($beneficieAccompagnement_accompagnement_medecin)
                                                                                                        ) {
                                                                                                            $continue = true;
                                                                                                            if ($beneficieAccompagnement_accompagnement_medecin == "Oui") {
                                                                                                                $continue = false;
                                                                                                                $ecouteRDV_accompagnement_medecin = $request->request->get('ecouteRDV_accompagnement_medecin');
                                                                                                                $clarteExplication_accompagnement_medecin = $request->request->get('clarteExplication_accompagnement_medecin');
                                                                                                                if (
                                                                                                                    !empty($ecouteRDV_accompagnement_medecin) &&
                                                                                                                    !empty($clarteExplication_accompagnement_medecin)
                                                                                                                ) {
                                                                                                                    $continue = true;
                                                                                                                }
                                                                                                            }
                                                                                                            if ($continue) {
                                                                                                                $continue = true;
                                                                                                                if($regime == "Interne"){
                                                                                                                    $continue = false;
                                                                                                                    $chambre_hebergement = $request->request->get('chambre_hebergement');
                                                                                                                    $prestationMenageChambre_hebergement = $request->request->get('prestationMenageChambre_hebergement');
                                                                                                                    $prestationMenageHebergement_hebergement = $request->request->get('prestationMenageHebergement_hebergement');
                                                                                                                    $interventionDemandeeRealisee_hebergement = $request->request->get('interventionDemandeeRealisee_hebergement');
                                                                                                                    $salleInformatique_hebergement = $request->request->get('salleInformatique_hebergement');
                                                                                                                    $espaceCollectif_hebergement = $request->request->get('espaceCollectif_hebergement');
                                                                                                                    $wifi_hebergement = $request->request->get('wifi_hebergement');
                                                                                                                    $echangesSurveillantsSemaine_hebergement = $request->request->get('echangesSurveillantsSemaine_hebergement');
                                                                                                                    $echangeSurveillantsWeekEnd_hebergement = $request->request->get('echangeSurveillantsWeekEnd_hebergement');
                                                                                                                    if(
                                                                                                                        !empty($chambre_hebergement) &&
                                                                                                                        !empty($prestationMenageChambre_hebergement) &&
                                                                                                                        !empty($prestationMenageHebergement_hebergement) &&
                                                                                                                        !empty($interventionDemandeeRealisee_hebergement) &&
                                                                                                                        !empty($salleInformatique_hebergement) &&
                                                                                                                        !empty($espaceCollectif_hebergement) &&
                                                                                                                        !empty($wifi_hebergement) &&
                                                                                                                        !empty($echangesSurveillantsSemaine_hebergement) &&
                                                                                                                        !empty($echangeSurveillantsWeekEnd_hebergement)
                                                                                                                    ) {
                                                                                                                        $continue = true;
                                                                                                                    }
                                                                                                                }
                                                                                                                if ($continue) {
                                                                                                                    $continue = true;
                                                                                                                    if($regime == "Interne" || $regime == "Demi-pensionnaire"){
                                                                                                                        $continue = false;
                                                                                                                        $salle_restauration = $request->request->get('salle_restauration');
                                                                                                                        $salleWeekEnd_restauration = $request->request->get('salleWeekEnd_restauration');
                                                                                                                        $suiviRealiseRegimeAlimentaire_restauration = $request->request->get('suiviRealiseRegimeAlimentaire_restauration');
                                                                                                                        $qualite_restauration = $request->request->get('qualite_restauration');
                                                                                                                        $quantiteServie_restauration = $request->request->get('quantiteServie_restauration');
                                                                                                                        $horaireRepas_restauration = $request->request->get('horaireRepas_restauration');
                                                                                                                        $varieteMenu_restauration = $request->request->get('varieteMenu_restauration');
                                                                                                                        if(
                                                                                                                            !empty($salle_restauration) &&
                                                                                                                            !empty($salleWeekEnd_restauration) &&
                                                                                                                            !empty($suiviRealiseRegimeAlimentaire_restauration) &&
                                                                                                                            !empty($qualite_restauration) &&
                                                                                                                            !empty($quantiteServie_restauration) &&
                                                                                                                            !empty($horaireRepas_restauration) &&
                                                                                                                            !empty($varieteMenu_restauration)
                                                                                                                        ) {
                                                                                                                            $continue = true;
                                                                                                                        }
                                                                                                                    }
                                                                                                                    if ($continue) {
                                                                                                                        $prestation_transport = $request->request->get('prestation_transport');
                                                                                                                        if(
                                                                                                                            !empty($prestation_transport)
                                                                                                                        ) {
                                                                                                                            $beneficie_securite = $request->request->get('beneficie_securite');
                                                                                                                            if(
                                                                                                                                !empty($beneficie_securite)
                                                                                                                            ) {
                                                                                                                                $continue = true;
                                                                                                                                if($beneficie_securite == "Oui"){
                                                                                                                                    $continue = false;
                                                                                                                                    $formation_securite = $request->request->get('formation_securite');
                                                                                                                                    if(!empty($formation_securite)){
                                                                                                                                        $continue = true;
                                                                                                                                    }
                                                                                                                                }
                                                                                                                                if($continue){
                                                                                                                                    $identifieMembre_conseilVieSociale = $request->request->get('identifieMembre_conseilVieSociale');
                                                                                                                                    $representantConsultation_conseilVieSociale = $request->request->get('representantConsultation_conseilVieSociale');
                                                                                                                                    $representantRapporter_conseilVieSociale = $request->request->get('representantRapporter_conseilVieSociale');
                                                                                                                                    if(
                                                                                                                                        !empty($identifieMembre_conseilVieSociale) &&
                                                                                                                                        !empty($representantConsultation_conseilVieSociale) &&
                                                                                                                                        !empty($representantRapporter_conseilVieSociale)
                                                                                                                                    ) {
                                                                                                                                        $information_preparationSortieCentre = $request->request->get('information_preparationSortieCentre');
                                                                                                                                        if(
                                                                                                                                            !empty($information_preparationSortieCentre)
                                                                                                                                        ) {
                                                                                                                                            $maniereGeneral_satisfactionGenerale = $request->request->get('maniereGeneral_satisfactionGenerale');
                                                                                                                                            $recommenderai_satisfactionGenerale = $request->request->get('recommenderai_satisfactionGenerale');
                                                                                                                                            if(
                                                                                                                                                !empty($maniereGeneral_satisfactionGenerale) &&
                                                                                                                                                !empty($recommenderai_satisfactionGenerale)
                                                                                                                                            ) {
                                                                                                                                                return true;
                                                                                                                                            }
                                                                                                                                        }
                                                                                                                                    }
                                                                                                                                }
                                                                                                                            }
                                                                                                                        }
                                                                                                                    }
                                                                                                                }
                                                                                                            }
                                                                                                        }
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return false;
    }
}