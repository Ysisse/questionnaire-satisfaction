var page = 1;

var pageSuivante = false;

function togg(element){
    if(getComputedStyle(document.getElementById(element)).display != "none"){
        document.getElementById(element).style.display = "none";
    } else {
        document.getElementById(element).style.display = "";
    }
}

function isInt(val) {
    var intRegex = /^-?\d+$/;
    if (!intRegex.test(val))
        return false;

    var intVal = parseInt(val, 10);
    return parseFloat(val) == intVal && !isNaN(intVal);
}

function validationNumDepartement(event) {
    if(event.value.length != 2 ){
        pageSuivante = false;
        return false;
    }
    if(!isInt(event.value)){
        pageSuivante = false;
        return false;
    }
    return true;
}

function verificationDecouverteAutre(event) {
    if(event.value == "Autres établissements"){
        togg("decouvertEtablissement_autre");
    }
}


function validateForm() {
    if(
        verifPage1() && verifPage2() && verifPage3() && verifPage4() && verifPage5() && verifPage6() && verifPage7() && verifPage8() && verifPage9() &&
        verifPage10() && verifPage11() && verifPage12() && verifPage13() && verifPage14() && verifPage15() && verifPage16() && verifPage17() && verifPage18() && verifPage19() &&
        verifPage20() && verifPage21() && verifPage22() && verifPage23()
    ){
        return true;
    }
    return false;
}

function verifPage1() {
    if(document.forms["questionnaire_satisfaction"]["age"].value != ""){
        pageSuivante = true;
        return true;
    }
    pageSuivante = false;
    return false;
}
function verifPage2() {
    if(document.forms["questionnaire_satisfaction"]["formations_dispositifs"].value != ""){
        pageSuivante = true;
        return true;
    }
    pageSuivante = false;
    return false;
}
function verifPage3() {
    if(document.forms["questionnaire_satisfaction"]["departement_origine"].value !=""){
        pageSuivante = validationNumDepartement(document.forms["questionnaire_satisfaction"]["num_departement"]);
        return validationNumDepartement(document.forms["questionnaire_satisfaction"]["num_departement"]);
    }
    pageSuivante = false;
    return false;
}
function verifPage4() {
    if(document.forms["questionnaire_satisfaction"]["regime"].value != ""){
        pageSuivante = true;
        return true;
    }
    pageSuivante = false;
    return false;
}
function verifPage5() {
    if(
        document.forms["questionnaire_satisfaction"]["MDPH_decouvertEtablissement"].checked
        ||
        document.forms["questionnaire_satisfaction"]["internet_decouvertEtablissement"].checked
        ||
        document.forms["questionnaire_satisfaction"]["reseauPersonnel_decouvertEtablissement"].checked
        ||
        document.forms["questionnaire_satisfaction"]["conseillerCapEmploi_decouvertEtablissement"].checked
        ||
        document.forms["questionnaire_satisfaction"]["conseillerPoleEmploi_decouvertEtablissement"].checked
        ||
        (
            document.forms["questionnaire_satisfaction"]["autre_decouvertEtablissement"].checked
            &&
            document.forms["questionnaire_satisfaction"]["decouvertEtablissement_autre"].value != ""
        )
    ){
        pageSuivante = true;
        return true;
    }
    pageSuivante = false;
    return false;
}
function verifPage6() {
    if(document.forms["questionnaire_satisfaction"]["beneficie_preAccueil"].value != ""){
        if(document.forms["questionnaire_satisfaction"]["beneficie_preAccueil"].value == "Oui"){
            if(
                document.forms["questionnaire_satisfaction"]["information_preAccueil"].value != ""
                &&
                document.forms["questionnaire_satisfaction"]["echange_preAccueil"].value != ""
                &&
                document.forms["questionnaire_satisfaction"]["visite_preAccueil"].value != ""
            ){
                pageSuivante = true;
                return true;
            }
        } else {
            pageSuivante = true;
            return true;
        }
    }
    pageSuivante = false;
    return false;
}
function verifPage7() {
    if(
        document.forms["questionnaire_satisfaction"]["contratSejour_accueil"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["livretAccueil_accueil"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["livretFonctionnement_accueil"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["accueilPhysique_accueil"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["accueilTelephonique_accueil"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["horaireOuverture_accueil"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["accueilProfessionnel_accueil"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["informationRegleVieEtablissement_accueil"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["informationRegleActivitesProposees_accueil"].value != ""
    ) {
        pageSuivante = true;
        return true;
    }
    pageSuivante = false;
    return false;
}
function verifPage8() {
    if(
        document.forms["questionnaire_satisfaction"]["qualiteFormation_dispositifSuivi_formationSuivie"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["supportFormation_dispositifSuivi_formationSuivie"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["maitriseSujetFormateur_dispositifSuivi_formationSuivie"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["clarteExplicationFormateur_dispositifSuivi_formationSuivie"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["interventionCollectiveFormateur_dispositifSuivi_formationSuivie"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["interventionIndividuelleFormateur_dispositifSuivi_formationSuivie"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["climatTravail_echangeFormateur_dispositifSuivi_formationSuivie"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["dureFormationDispositif_dispositifSuivi_formationSuivie"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["conditionMaterielle_dispositifSuivi_formationSuivie"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["echangeStagiaire_dispositifSuivi_formationSuivie"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["choicFormationDispositif_dispositifSuivi_formationSuivie"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["accessibiliteGeneralLocauxFormation_dispositifSuivi_formationSuivie"].value != ""
    ) {
        pageSuivante = true;
        return true;
    }
    pageSuivante = false;
    return false;
}
function verifPage9() {
    if(
        document.forms["questionnaire_satisfaction"]["elaborationRedaction_projetPersonnalise"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["signeProjetActualisation_projetPersonnalise"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["projetActualiseRegulierement_projetPersonnalise"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["suiviProjet_projetPersonnalise"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["accompagnementReferent_projetPersonnalise"].value != ""
    ) {
        pageSuivante = true;
        return true;
    }
    pageSuivante = false;
    return false;
}
function verifPage10() {
    if(
        document.forms["questionnaire_satisfaction"]["connaissancePresence_accompagnement_assistanceSociale"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["beneficieAccompagnement_accompagnement_assistanceSociale"].value != ""
    ){
        if(document.forms["questionnaire_satisfaction"]["beneficieAccompagnement_accompagnement_assistanceSociale"].value == "Oui")
        {
            if(
                document.forms["questionnaire_satisfaction"]["accompagnementRealise_accompagnement_assistanceSociale"].value != ""
                &&
                document.forms["questionnaire_satisfaction"]["ecouteAssistanceSociale_accompagnement_assistanceSociale"].value != ""
                &&
                document.forms["questionnaire_satisfaction"]["clarteExplications_accompagnement_assistanceSociale"].value != ""
                &&
                document.forms["questionnaire_satisfaction"]["delaisRDV_accompagnement_assistanceSociale"].value != ""
            ){
                pageSuivante = true;
                return true;
            }
        } else {
            pageSuivante = true;
            return true;
        }
    }
    pageSuivante = false;
    return false;
}
function verifPage11() {
    if(
        document.forms["questionnaire_satisfaction"]["connaissancePresence_accompagnement_psychologueClinicienne"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["beneficieAccompagnement_accompagnement_psychologueClinicienne"].value != ""
    ) {
        if(document.forms["questionnaire_satisfaction"]["beneficieAccompagnement_accompagnement_psychologueClinicienne"].value == "Oui")
        {
            if(
                document.forms["questionnaire_satisfaction"]["accompagnementRealise_accompagnement_psychologueClinicienne"].value != ""
                &&
                document.forms["questionnaire_satisfaction"]["ecoutePsychologueClinicienne_accompagnement_psychologueClinicienne"].value != ""
                &&
                document.forms["questionnaire_satisfaction"]["clarteExplications_accompagnement_psychologueClinicienne"].value != ""
                &&
                document.forms["questionnaire_satisfaction"]["delaisRDV_accompagnement_psychologueClinicienne"].value != ""
            ){
                pageSuivante = true;
                return true;
            }
        } else {
            pageSuivante = true;
            return true;
        }
    }
    pageSuivante = false;
    return false;
}
function verifPage12() {
    if(
        document.forms["questionnaire_satisfaction"]["connaissancePresence_accompagnement_psychologueTravail"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["beneficieAccompagnement_accompagnement_psychologueTravail"].value != ""
    ) {
        if(document.forms["questionnaire_satisfaction"]["beneficieAccompagnement_accompagnement_psychologueTravail"].value == "Oui")
        {
            if(
                document.forms["questionnaire_satisfaction"]["accompagnementRealise_accompagnement_psychologueTravail"].value != ""
                &&
                document.forms["questionnaire_satisfaction"]["ecoutePsychologueTravail_accompagnement_psychologueTravail"].value != ""
                &&
                document.forms["questionnaire_satisfaction"]["clarteExplications_accompagnement_psychologueTravail"].value != ""
                &&
                document.forms["questionnaire_satisfaction"]["delaisRDV_accompagnement_psychologueTravail"].value != ""
            ){
                pageSuivante = true;
                return true;
            }
        } else {
            pageSuivante = true;
            return true;
        }
    }
    pageSuivante = false;
    return false;
}
function verifPage13() {
    if(
        document.forms["questionnaire_satisfaction"]["connaissancePresence_accompagnement_psychologueProfessionnel"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["beneficieAccompagnement_accompagnement_psychologueProfessionnel"].value != ""
    ) {
        if(document.forms["questionnaire_satisfaction"]["beneficieAccompagnement_accompagnement_psychologueProfessionnel"].value == "Oui")
        {
            if(
                document.forms["questionnaire_satisfaction"]["accompagnementRealise_accompagnement_psychologueProfessionnel"].value != ""
                &&
                document.forms["questionnaire_satisfaction"]["ecoutePsychologueProfessionnel_accompagnement_psychologueProfessionnel"].value != ""
                &&
                document.forms["questionnaire_satisfaction"]["clarteExplications_accompagnement_psychologueProfessionnel"].value != ""
                &&
                document.forms["questionnaire_satisfaction"]["delaisRDV_accompagnement_psychologueProfessionnel"].value != ""
                &&
                document.forms["questionnaire_satisfaction"]["informationDelivree_accompagnement_psychologueProfessionnel"].value != ""
                &&
                document.forms["questionnaire_satisfaction"]["interventionCollective_accompagnement_psychologueProfessionnel"].value != ""
            ){
                pageSuivante = true;
                return true;
            }
        } else {
            pageSuivante = true;
            return true;
        }
    }
    pageSuivante = false;
    return false;
}
function verifPage14() {
    if(
        document.forms["questionnaire_satisfaction"]["connaissancePresence_accompagnement_referentParcours"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["beneficieAccompagnement_accompagnement_referentParcours"].value != ""
    ) {
        if(document.forms["questionnaire_satisfaction"]["beneficieAccompagnement_accompagnement_referentParcours"].value == "Oui")
        {
            if(
                document.forms["questionnaire_satisfaction"]["accompagnementRealise_accompagnement_referentParcours"].value != ""
                &&
                document.forms["questionnaire_satisfaction"]["ecouteReferentParcours_accompagnement_referentParcours"].value != ""
                &&
                document.forms["questionnaire_satisfaction"]["clarteExplications_accompagnement_referentParcours"].value != ""
                &&
                document.forms["questionnaire_satisfaction"]["delaisRDV_accompagnement_referentParcours"].value != ""
            ){
                pageSuivante = true;
                return true;
            }
        } else {
            pageSuivante = true;
            return true;
        }
    }
    pageSuivante = false;
    return false;
}
function verifPage15() {
    if(
        document.forms["questionnaire_satisfaction"]["connaissancePresence_accompagnement_infirmiere"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["beneficieAccompagnement_accompagnement_infirmiere"].value != ""
    ) {
        if(document.forms["questionnaire_satisfaction"]["beneficieAccompagnement_accompagnement_infirmiere"].value == "Oui")
        {
            if(
                document.forms["questionnaire_satisfaction"]["accompagnementRealise_accompagnement_infirmiere"].value != ""
                &&
                document.forms["questionnaire_satisfaction"]["ecouteInfirmiere_accompagnement_infirmiere"].value != ""
                &&
                document.forms["questionnaire_satisfaction"]["clarteExplications_accompagnement_infirmiere"].value != ""
                &&
                document.forms["questionnaire_satisfaction"]["delaisRDV_accompagnement_infirmiere"].value != ""
            ){
                pageSuivante = true;
                return true;
            }
        } else {
            pageSuivante = true;
            return true;
        }
    }
    pageSuivante = false;
    return false;
}
function verifPage16() {
    if(
        document.forms["questionnaire_satisfaction"]["beneficieAccompagnement_accompagnement_medecin"].value != ""
    ) {
        if(document.forms["questionnaire_satisfaction"]["beneficieAccompagnement_accompagnement_medecin"].value == "Oui")
        {
            if(
                document.forms["questionnaire_satisfaction"]["ecouteRDV_accompagnement_medecin"].value != ""
                &&
                document.forms["questionnaire_satisfaction"]["clarteExplication_accompagnement_medecin"].value != ""
            ){
                pageSuivante = true;
                return true;
            }
        } else {
            pageSuivante = true;
            return true;
        }
    }
    pageSuivante = false;
    return false;
}
function verifPage17() {
    if(document.forms["questionnaire_satisfaction"]["regime"].value == "Externe" || document.forms["questionnaire_satisfaction"]["regime"].value == "Demi-pensionnaire"){
        pageSuivante = true;
        return true;
    } else {
        if(
            document.forms["questionnaire_satisfaction"]["chambre_hebergement"].value != ""
            &&
            document.forms["questionnaire_satisfaction"]["prestationMenageChambre_hebergement"].value != ""
            &&
            document.forms["questionnaire_satisfaction"]["prestationMenageHebergement_hebergement"].value != ""
            &&
            document.forms["questionnaire_satisfaction"]["interventionDemandeeRealisee_hebergement"].value != ""
            &&
            document.forms["questionnaire_satisfaction"]["salleInformatique_hebergement"].value != ""
            &&
            document.forms["questionnaire_satisfaction"]["espaceCollectif_hebergement"].value != ""
            &&
            document.forms["questionnaire_satisfaction"]["wifi_hebergement"].value != ""
            &&
            document.forms["questionnaire_satisfaction"]["echangesSurveillantsSemaine_hebergement"].value != ""
            &&
            document.forms["questionnaire_satisfaction"]["echangeSurveillantsWeekEnd_hebergement"].value != ""
        ) {
            pageSuivante = true;
            return true;
        }
        pageSuivante = false;
        return false;
    }
}
function verifPage18() {
    if(document.forms["questionnaire_satisfaction"]["regime"].value == "Externe"){
        pageSuivante = true;
        return true;
    } else {
        if(
            document.forms["questionnaire_satisfaction"]["salle_restauration"].value != ""
            &&
            document.forms["questionnaire_satisfaction"]["salleWeekEnd_restauration"].value != ""
            &&
            document.forms["questionnaire_satisfaction"]["suiviRealiseRegimeAlimentaire_restauration"].value != ""
            &&
            document.forms["questionnaire_satisfaction"]["qualite_restauration"].value != ""
            &&
            document.forms["questionnaire_satisfaction"]["quantiteServie_restauration"].value != ""
            &&
            document.forms["questionnaire_satisfaction"]["horaireRepas_restauration"].value != ""
            &&
            document.forms["questionnaire_satisfaction"]["varieteMenu_restauration"].value != ""
        ) {
            pageSuivante = true;
            return true;
        }
        pageSuivante = false;
        return false;
    }
}
function verifPage19() {
    if(
        document.forms["questionnaire_satisfaction"]["prestation_transport"].value != ""
    ) {
        pageSuivante = true;
        return true;
    }
    pageSuivante = false;
    return false;
}
function verifPage20() {
    if(document.forms["questionnaire_satisfaction"]["beneficie_securite"].value != ""){
        if(document.forms["questionnaire_satisfaction"]["beneficie_securite"].value == "Oui"){
            if(document.forms["questionnaire_satisfaction"]["formation_securite"].value != ""){
                pageSuivante = true;
                return true;
            }
        }
        pageSuivante = true;
        return true;
    }
    pageSuivante = false;
    return false;
}
function verifPage21() {
    if(document.forms["questionnaire_satisfaction"]["identifieMembre_conseilVieSociale"].value != "")
        if(document.forms["questionnaire_satisfaction"]["identifieMembre_conseilVieSociale"].value == "Oui")
        {
            if(document.forms["questionnaire_satisfaction"]["representantConsultation_conseilVieSociale"].value != ""
                &&
                document.forms["questionnaire_satisfaction"]["representantRapporter_conseilVieSociale"].value != "")
            {
                pageSuivante = true;
                return true;
            } else {
                pageSuivante = false;
                return false;
            }
        } else {
            pageSuivante = true;
            return true;
        }
    {
        pageSuivante = true;
        return true;
    }
    return false;
}
function verifPage22() {
    if(
        document.forms["questionnaire_satisfaction"]["information_preparationSortieCentre"].value != ""
    ) {
        pageSuivante = true;
        return true;
    }
    pageSuivante = false;
    return false;
}
function verifPage23() {
    if(
        document.forms["questionnaire_satisfaction"]["maniereGeneral_satisfactionGenerale"].value != ""
        &&
        document.forms["questionnaire_satisfaction"]["recommenderai_satisfactionGenerale"].value != ""
    ) {
        document.getElementById("question_confirmation_button").innerHTML = '<button type="submit" class="btn btn-secondary" id="question_confirmation_button">Confirmer</button>';
        pageSuivante = true;
        return true;
    }
    pageSuivante = false;
    return false;
}

function passerPageSuivante() {
    if(validationPage()) {
        togg("question_page_" + page);
        if (page == 1) {
            document.getElementById("question_passage_gauche").style.visibility = "";
        }
        if(page==16) {
            if (document.forms["questionnaire_satisfaction"]["regime"].value == "Externe") {
                page += 3;
            } else {
                if (document.forms["questionnaire_satisfaction"]["regime"].value == "Demi-pensionnaire") {
                    page += 2;
                } else {
                    page++;
                }
            }
        } else {
            page++;
        }
        togg("question_page_" + page);
        document.getElementById("question_progession").innerHTML = "Progression : page "+page+" sur 23";
        if(page == 23){
            document.getElementById("question_passage_droite").style.visibility = "hidden";
            togg("question_confirmation");
        }
    } else {
        alert("Veuillez répondre à toutes les questions avant de passer à la suite.")
    }
}

function passerPagePrecedante() {
    togg("question_page_" + page);
    if(page==19) {
        if (document.forms["questionnaire_satisfaction"]["regime"].value == "Externe") {
            page -= 3;
        } else {
            page--;
        }
    } else {
        if(page == 18){
            if (document.forms["questionnaire_satisfaction"]["regime"].value == "Demi-pensionnaire") {
                page -= 2;
            } else {
                page--;
            }
        } else {
            page--;
        }
    }
    changezCurseur();
    togg("question_page_"+page);
    if(page!=23){
        document.getElementById("question_passage_droite").style.visibility = "";
        document.getElementById("question_confirmation").style.display = "none";
    }
    if(page==23){
        document.getElementById("question_confirmation").style.display = "";
    }
    if(page==1){
        document.getElementById("question_passage_gauche").style.visibility = "hidden";
    }
    document.getElementById("question_progession").innerHTML = "Progression : page "+page+" sur 23";
}

function validationPage(){
    if(page==1){
        return verifPage1();
    }
    if(page==2){
        return verifPage2();
    }
    if(page==3){
        return verifPage3();
    }
    if(page==4){
        return verifPage4();
    }
    if(page==5){
        return verifPage5();
    }
    if(page==6){
        return verifPage6();
    }
    if(page==7){
        return verifPage7();
    }
    if(page==8){
        return verifPage8();
    }
    if(page==9){
        return verifPage9();
    }
    if(page==10){
        return verifPage10();
    }
    if(page==11){
        return verifPage11();
    }
    if(page==12){
        return verifPage12();
    }
    if(page==13){
        return verifPage13();
    }
    if(page==14){
        return verifPage14();
    }
    if(page==15){
        return verifPage15();
    }
    if(page==16){
        return verifPage16();
    }
    if(page==17){
        return verifPage17();
    }
    if(page==18){
        return verifPage18();
    }
    if(page==19){
        return verifPage19();
    }
    if(page==20){
        return verifPage20();
    }
    if(page==21){
        return verifPage21();
    }
    if(page==22){
        return verifPage22();
    }
    if(page==23){
        return verifPage23();
    }
}

function changezCurseur() {
    //ne fonctionne pas
    /*if(validationPage()){
        document.getElementById("question_passage_droite").style = "color: green;cursor: url(https://dab1nmslvvntp.cloudfront.net/wp-content/uploads/2015/07/1436013803checkbox-1024x1024.jpg), pointer;";
    } else {
        document.getElementById("question_passage_droite").style = "color: red;cursor: not-allowed;";
    }*/
}