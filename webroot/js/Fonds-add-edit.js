/*********************************************************************/
//* Script Fonds-add-edit.js                                         */
/* Ce script est appelé par la vue Fonds (page edit et add) pour     */
/* gérer les relations entre les cases à cocher de volumétrie ou de  */
/* dates extrêmes, et les champs de saisie correspondantn etc.       */
/*********************************************************************/

// Initialisation de la page :
$(document).ready(function(){

	if ($('#ind_nb_ml_inconnu').is(':checked')) {
		$('#nb_ml').prop( "readonly", true);
		$('#nb_ml').css("background", "#f0f0f0");
	}
	
	if ($('#ind_nb_go_inconnu').is(':checked')) {
		$('#nb_go').prop( "readonly", true);
		$('#nb_go').css("background", "#f0f0f0");
	}	

	if ($('#ind_annee').is(':checked')) {
		$('#annee_deb').prop( "readonly", true);
		$('#annee_fin').prop( "readonly", true);
		$('#annee_deb').css("background", "#f0f0f0");
		$('#annee_fin').css("background", "#f0f0f0");		
	}	
	
	// Initialisation de la zone de la prestation de traitement externalisé
	if ($('#type-prise-en-charge-id').val() == $priseEnChargeNon ) {
		$('#type-realisation-traitement-id').val($realisationTraitementAucun);
		bloqueDetailPrestation();
	}	

	// Initialisation de la gestion de l'implantation en magasin
        if ($('#adresses-0-volume').val() == '' ) {     
                $('#adresses-0-magasin').prop('disabled', true); 
                $('#adresses-0-magasin').prop('required', false); 
                $('#adresses-0-epi-deb').prop('disabled', true);   
                $('#adresses-0-epi-fin').prop('disabled', true);    
                $('#adresses-0-travee-deb').prop('disabled', true);  
                $('#adresses-0-travee-fin').prop('disabled', true);   
                $('#adresses-0-tablette-deb').prop('disabled', true);  
                $('#adresses-0-tablette-fin').prop('disabled', true);   
        }

	if ($('#adresses-1-volume').val() == '' ) {	
		$('#adresses-1-magasin').prop('disabled', true);	
		$('#adresses-1-magasin').prop('required', false);			
		$('#adresses-1-epi-deb').prop('disabled', true);	
		$('#adresses-1-epi-fin').prop('disabled', true);	
		$('#adresses-1-travee-deb').prop('disabled', true);	
		$('#adresses-1-travee-fin').prop('disabled', true);	
		$('#adresses-1-tablette-deb').prop('disabled', true);	
		$('#adresses-1-tablette-fin').prop('disabled', true);	
	}
	if ($('#adresses-2-volume').val() == '' ) {	
		$('#adresses-2-magasin').prop('disabled', true);	
		$('#adresses-2-magasin').prop('required', false);			
		$('#adresses-2-epi-deb').prop('disabled', true);	
		$('#adresses-2-epi-fin').prop('disabled', true);	
		$('#adresses-2-travee-deb').prop('disabled', true);	
		$('#adresses-2-travee-fin').prop('disabled', true);	
		$('#adresses-2-tablette-deb').prop('disabled', true);	
		$('#adresses-2-tablette-fin').prop('disabled', true);	
	}	
		
})

// Gestion des évènements sur case à cocher de volumétrie ml et Go :
function onChangeCheckNbMl() {
	if ($('#ind_nb_ml_inconnu').is(':checked')) {
		// La case vient d'être cochée : on met la valeur à zéro nb_ml et on 
		// désactive
		$('#nb_ml').prop( "readonly", true);
		$('#nb_ml').css("background", "#f0f0f0");
		$('#nb_ml').val(0);
	}
	else {
		// La case vient d'être décochée. On permet de saisir dans nb_ml :
		$('#nb_ml').prop( "readonly", false);
		$('#nb_ml').css("background", "white");
	}
}

function onChangeCheckNbGo() {
	if ($('#ind_nb_go_inconnu').is(':checked')) {
		// La case vient d'être cochée : on met la valeur à zéro nb_go et on 
		// désactive
		$('#nb_go').prop( "readonly", true);
		$('#nb_go').css("background", "#f0f0f0");
		$('#nb_go').val(0);
	}
	else {
		// La case vient d'être décochée. On permet de saisir dans nb_go :
		$('#nb_go').prop( "readonly", false);
		$('#nb_go').css("background", "white");
	}
}

function onChangeCheckDate() {
	if ($('#ind_annee').is(':checked')) {
		// La case vient d'être cochée : on met le dates à vide
		$('#annee_deb').val('');
		$('#annee_fin').val('');		
		$('#annee_deb').prop( "readonly", true);
		$('#annee_fin').prop( "readonly", true);
		$('#annee_deb').css("background", "#f0f0f0");
		$('#annee_fin').css("background", "#f0f0f0");

	}
	else {
		// La case vient d'être décochée. On permet de saisir dans nb_go :
		$('#annee_deb').prop( "readonly", false);
		$('#annee_fin').prop( "readonly", false);
		$('#annee_deb').css("background", "white");
		$('#annee_fin').css("background", "white");
	}
}

function onChangePriseEnCharge() {
	// Si il n'y a pas de prise en charge, il n'y a pas de traitement réalisé ou envisagé
	if ($('#type-prise-en-charge-id').val() == $priseEnChargeNon ) {
		$('#type-realisation-traitement-id').val($realisationTraitementAucun);		
		// On neutralise les autres champs détaillant la prestation : 
		bloqueDetailPrestation();

	} else {
		// on permet de saisir les champs détaillant la prestation : 
		activeDetailPrestation();	
	}
}

function onChangeRealisationTraitement() {
	// Si il n'y a pas de prise en charge, il n'y a pas de traitement réalisé ou envisagé
	if ($('#type-prise-en-charge-id').val() == $priseEnChargeNon ) {
		$('#type-realisation-traitement-id').val($realisationTraitementAucun);	
		// On neutralise les autres champs détaillant la prestation : 
		bloqueDetailPrestation();

	} else {
		// on permet de saisir les champs détaillant la prestation : 
		activeDetailPrestation();	
	}		
}

function activeDetailPrestation() {
	$('#site-intervention').prop('disabled', false);	
	$('#responsable-operation').prop('disabled', false);
	$('#dt-deb-prestation-day').prop('disabled', false);
	$('#dt-deb-prestation-month').prop('disabled', false);
	$('#dt-deb-prestation-year').prop('disabled', false);
	$('#dt-fin-prestation-day').prop('disabled', false);
	$('#dt-fin-prestation-month').prop('disabled', false);
	$('#dt-fin-prestation-year').prop('disabled', false);	
}

function bloqueDetailPrestation() {
	$('#site-intervention').val('');	
	$('#responsable-operation').val('');
	$('#dt-deb-prestation-day').val('');
	$('#dt-deb-prestation-month').val('');
	$('#dt-deb-prestation-year').val('');
	$('#dt-fin-prestation-day').val('');
	$('#dt-fin-prestation-month').val('');
	$('#dt-fin-prestation-year').val('');
	$('#site-intervention').prop('disabled', true);	
	$('#responsable-operation').prop('disabled', true);
	$('#dt-deb-prestation-day').prop('disabled', true);
	$('#dt-deb-prestation-month').prop('disabled', true);
	$('#dt-deb-prestation-year').prop('disabled', true);
	$('#dt-fin-prestation-day').prop('disabled', true);
	$('#dt-fin-prestation-month').prop('disabled', true);
	$('#dt-fin-prestation-year').prop('disabled', true);	
}


// Gestion des données d'implantation en magasin : 
function onChangeAdresse(numSeq) {
	// Si le volume de séquence 1 est renseigné alors il faut indiquer les autres informations à son sujet, notamment le magasin qui est obligatoire.
	if ($('#adresses-' + numSeq + '-volume').val() != '' ) {
		$('#adresses-' + numSeq + '-magasin').prop('disabled', false);	
		$('#adresses-' + numSeq + '-magasin').prop('required', true);			
		$('#adresses-' + numSeq + '-epi-deb').prop('disabled', false);	
		$('#adresses-' + numSeq + '-epi-fin').prop('disabled', false);	
		$('#adresses-' + numSeq + '-travee-deb').prop('disabled', false);	
		$('#adresses-' + numSeq + '-travee-fin').prop('disabled', false);	
		$('#adresses-' + numSeq + '-tablette-deb').prop('disabled', false);	
		$('#adresses-' + numSeq + '-tablette-fin').prop('disabled', false);	
	}
	else {
		// On réinitialise toutes les valeurs
		$('#adresses-' + numSeq + '-magasin').val("");
		$('#adresses-' + numSeq + '-magasin').prop('disabled', true);	
		$('#adresses-' + numSeq + '-magasin').prop('required', false);	
		$('#adresses-' + numSeq + '-epi-deb').val('');		
		$('#adresses-' + numSeq + '-epi-deb').prop('disabled', true);	
		$('#adresses-' + numSeq + '-epi-fin').prop('disabled', true);
		$('#adresses-' + numSeq + '-epi-fin').val('');		
		$('#adresses-' + numSeq + '-travee-deb').prop('disabled', true);	
		$('#adresses-' + numSeq + '-travee-deb').val('');
		$('#adresses-' + numSeq + '-travee-fin').prop('disabled', true);	
		$('#adresses-' + numSeq + '-travee-fin').val('');
		$('#adresses-' + numSeq + '-tablette-deb').prop('disabled', true);	
		$('#adresses-' + numSeq + '-tablette-deb').val('');
		$('#adresses-' + numSeq + '-tablette-fin').prop('disabled', true);
		$('#adresses-' + numSeq + '-tablette-fin').val('');
	}
		
}

