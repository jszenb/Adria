<?php
/********************************************************************************************/
// Application ADRIA
// fichefonds.ctp
// Template de génération de la fiche d'un fonds
//
// Campus Condorcet (2016)
/********************************************************************************************/

//*******************************************************************************************
// Préparation de l'état : 
//    * surcharge de la classe par défaut pour gestion du pied de page,
//    * timeout,
//    * Déclaration de l'objet $pdf et paramétrage général pour la page
//*******************************************************************************************
// inclusion de la librairie TCPDF
require_once ROOT . DS . 'vendor' . DS . 'tcpdf' . DS . 'tcpdf.php'; 

// Extension de la librairie Tcpdf afin de gérer dynamiquement le pied de page
// et y modifier le nom de l'établissement
class MYPDF extends TCPDF {

	public $monFonds = "";
	public $monProfil = "";

	// Setter pour le nom du fonds (pour le pied de page)
	public function set_nom_fonds($value) {
		$this->monFonds = $value;
	}
	
	// Setter pour le profil pour lequel on produit l'état
	public function set_profil($value) {
		$this->monProfil = $value;
	}	
	
    // Ecriture du pied de page
    public function Footer() {
       
        $this->Cell(135, 6, 'Fiche de fonds : ' . $this->monFonds . ' - Extraction ADRIA au ' . date('d/m/Y') , 'TB', false, 'L', 0, '', 1, false, 'T', 'M');
		$this->Cell(35, 6, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 'TB', false, 'R', 0, '', 0, false, 'T', 'M');
    }
    
    // Ecriture d'une ligne de données 
    public function ecritLigne($libelle, $donnee, $lg_libelle = 60, $lg_donnee = 110) {
        $this->SetTextColor(0);
        $this->SetFont('times', 'B', '10');
        if ($libelle != '') {
            $libelle .= ' :';
        }
        $this->Cell($lg_libelle, 6, $libelle, 0, 0, '0', false, false, 1);
        $this->SetFont('times', '', '10');
        $this->Cell( $lg_donnee, 6, $donnee , 0, 0, '0', false, false, 1);
        $this->Ln();
    }
        
    // Ecriture de l'intitulé d'un bloc de données    
	// ------------------------------
    public function ecritBloc($libelle, $lg_libelle = 170) {
        $this->SetTextColor(255, 0, 0);
        $this->SetFont('times', 'B', '11');
        $this->SetFillColor(224, 235, 255);
        $this->Cell($lg_libelle, 6, $libelle , 'B', 0, '', false, false, 1);
        $this->Ln(); 
    }
}

// Timeout pour la production de l'état : 180 secondes.
set_time_limit(180);

// Création d'un document MYPDF dans l'objet $pdf 
// (les constantes sont dans le fichier de config de tcpdf dans le rep. vendor)
$pdf = new MYPDF('P', 'mm', PDF_PAGE_FORMAT, TRUE, 'UTF-8', FALSE);

// Spécification de certains paramètres de TCPDF (içi on spécifie l'auteur par défaut)
$pdf->SetCreator(PDF_CREATOR);
	
// Spécification du header et footer. Toutes les constantes sont dans le fichier de config de tcpdf dans le rep. vendor
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

if ($typeUserEnSession == PROFIL_CC) {
	$pdf->setPrintHeader(FALSE);
}
else {
	$pdf->setPrintHeader(TRUE);
}
$pdf->setPrintFooter(TRUE);
$pdf->SetFooterFont(['times','','10']);
 
// On spécifie la fonte par défaut
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
 
// On définit les marges : je mets 25mm partout sauf au footer
$pdf->SetMargins(20, 20, 20);
$pdf->SetFooterMargin(22);

// On indique que le dépassement d'une page entraine automatiquement la création d'un saut de page et d'une nouvelle page
$pdf->SetAutoPageBreak(TRUE, 24);

//*******************************************************************************************
// Ecriture de l'état : 
// boucle sur les fonds trouvés, avec rupture de page sur l'établissement puis rupture
// dans le tableau de fonds sur les entités documentaires et les types de fonds
//*******************************************************************************************

// Première page avec le titre du document
// ---------------------------------------
$pdf->AddPage();
$premierePage = true;

// On fixe le nom du fonds pour le pied de page :
$pdf->set_nom_fonds($fond->nom) ;
$pdf->set_profil($profil) ;


// Pour le profil CA, on adapte le titre avec des informations
// sur l'entité documentaire
// -----------------------------------------------------------
$pdf->Ln(5);
$pdf->SetFont('times', 'B', 12);
$pdf->SetTextColor(0);
//$fond->ind_maj ? $extension =  ' ✔' : $extension = '';
$pdf->Write(6, $title . ' : ' .  $fond->nom . $extension, '', false, 'L');
$pdf->Ln(10);	

// -----------------------------------------------------------------------------------------
// Ecriture des données 
// ------------------------------------------------------------------------------------------
	
	// Bloc "identification du fonds"
	// ------------------------------
    $pdf->ecritBloc('Identification du fonds');

    $pdf->ecritLigne('Nom', $fond->nom);
	
    if (!$fond->ind_annee) { 
        $dates = $fond->annee_deb . ' - ' . $fond->annee_fin ;
    } 
    else { 
        $dates = 'A renseigner' ;
    }
    $pdf->ecritLigne('Dates extrêmes', $dates);    
    
    $pdf->ecritLigne('Cote', $fond->cote);
    $pdf->ecritLigne('Entité documentaire', $fond->entite_doc->nom . ' ('. $fond->entite_doc->code . ')');
    $pdf->ecritLigne('Lieu(x) de conservation', '');
   
   if (!empty($fond->lieu_conservations)):
        foreach ($fond->lieu_conservations as $lieuConservations): 
            $pdf->ecritLigne('',$lieuConservations->nom . ", " . $lieuConservations->adresse_1 . ", " . $lieuConservations->adresse_cp . " " . $lieuConservations->adresse_ville);
        endforeach;
    endif;
    
	$pdf->Ln();
    
    // Bloc "contexte"
	// ------------------------------
    $pdf->ecritBloc('Contexte');    
    
    $pdf->ecritLigne('Producteur', $fond->producteur);
    $pdf->ecritLigne('Historique', '');
    if (!empty($fond->historique)){
        $pdf->SetFont('times', '', '10');
        $pdf->SetTextColor(0);
        $pdf->Write(6, $fond->historique, '', false, 'L');
        $pdf->Ln(); 
    }        
    $pdf->ecritLigne('Statut juridique', $fond->type_stat_jurid->type);
    $pdf->ecritLigne("Mode d'entrée", $fond->type_entree->type);
    $pdf->ecritLigne("Document(s) afférent(s) au mode d'entrée", '', 70);
    
    if (!empty($fond->type_doc_afferents)):
        foreach ($fond->type_doc_afferents as $typeDocAfferents): 
            $pdf->ecritLigne('', $typeDocAfferents->type);
        endforeach;
    endif;
    
	$pdf->Ln();
    
    // Bloc "contenu et volumétrie"
	// ------------------------------
    $pdf->ecritBloc('Contenu et volumétrie'); 
    
    $pdf->ecritLigne('Type de fonds', $fond->type_fond->type);
    $pdf->ecritLigne("URL d'inventaire de la collection", $fond->url_collection);    
    $fond->ind_bib ? $indbib = 'Oui' : $indbib = 'Non';
    $pdf->ecritLigne("Fonds couplé à une collection d'imprimés ?", $indbib);
    if (!empty($fond->precision_bib)) {
        $pdf->ecritLigne('Précisions sur cette collection','');
        $pdf->SetFont('times', '', '10');
        $pdf->SetTextColor(0);
        $pdf->Write(6, $fond->precision_bib, '', false, 'L');
        $pdf->Ln();         
    }
    
    $pdf->ecritLigne('Type(s) de documents du fonds', '');
	if (!empty($fond->type_docs)):
        foreach ($fond->type_docs as $type_docs): 
            $pdf->ecritLigne('', $type_docs->type);
        endforeach;
    endif;    
    
    $pdf->ecritLigne('Type(s) de supports', '');
	if (!empty($fond->type_supports)):
        foreach ($fond->type_supports as $type_supports): 
            $pdf->ecritLigne('', $type_supports->type);
        endforeach;
    endif;      
   
    !$fond->ind_nb_ml_inconnu ? $volml = $this->Number->format($fond->nb_ml) : $volml = 'inconnue';
    $pdf->ecritLigne('Volumétrie physique en mètres linéaires', $volml);
    
    !$fond->ind_nb_go_inconnu ? $volgo = $this->Number->format($fond->nb_go) : $volgo = 'inconnue';
    $pdf->ecritLigne('Volumétrie physique en en giga-octets', $volgo);
    
    $pdf->ecritLigne('Accroissement', $fond->type_accroissement->type);
    
    $pdf->ecritLigne('Discipline(s)', '');
	if (!empty($fond->thematiques)):
        foreach ($fond->thematiques as $thematiques): 
            $pdf->ecritLigne('', $thematiques->intitule);
        endforeach;
    endif;    
    
    $pdf->ecritLigne('Aire(s) culturelle(s)', '');
	if (!empty($fond->aire_culturelles)):
        foreach ($fond->aire_culturelles as $aire_culturelles): 
            $pdf->ecritLigne('', $aire_culturelles->intitule);
        endforeach;
    endif;        
    
    $pdf->Ln(); 
    
    // Bloc "contenu et volumétrie"
	// ------------------------------
    $pdf->ecritBloc('Traitement matériel et intellectuel'); 
    
    $pdf->ecritLigne('Conditionnement(s)', '');
    if (!empty($fond->type_conditionnements)):
        foreach ($fond->type_conditionnements as $type_conditionnements): 
            $pdf->ecritLigne('', $type_conditionnements->type);
        endforeach;
    endif;
    
    $pdf->ecritLigne('Traitement', $fond->type_traitement->type);
    $pdf->ecritLigne('Instrument de recherche', $fond->type_instr_rech->type);
    $pdf->ecritLigne("URL de l'instrument de recherche", $fond->url_instr_rech);
    $pdf->ecritLigne("Numérisation", $fond->type_numerisation->type);    
    
    $pdf->Ln(); 
    
    // Bloc "Observations"
	// ------------------------------
    if (!empty($fond->observations)) {
        $pdf->ecritBloc('Observations');   
        $pdf->SetFont('times', '', '10');
        $pdf->SetTextColor(0);
        $pdf->Write(6, $fond->observations, '', false, 'L');
        $pdf->Ln();     
    }
    $pdf->Ln();  
	
    // Bloc "Orientation"
    // -----------------
    $pdf->ecritBloc('Orientation du fonds');
    $pdf->ecritLigne('Lieu de stockage', LIB_STOCKAGE_CIBLE[$fond->stockage]);
    $pdf->ecritLigne('Le fonds est-il communicable ?', LIB_COMMUNICATION[$fond->communication]);
    $pdf->Ln();


    // Bloc "Marché de traitement"
    // ----------------------------------------------
    if ( ( $pdf->monProfil != PROFIL_CO ) || ($pdf->monProfil == PROFIL_CO && $fond->type_prise_en_charge->id != NON_PRISE_EN_CHARGE) ) {
      $pdf->ecritBloc('Marché de traitement');   
      $pdf->ecritLigne("Prise en charge du fonds", $fond->type_prise_en_charge->type);
      $pdf->ecritLigne("Prestation", $fond->type_realisation_traitement->type);
      if ($fond->type_prise_en_charge->id != NON_PRISE_EN_CHARGE)  {
		$fond->site_intervention == '' ? $fond->site_intervention = ' ' : '';
		$pdf->ecritLigne("Site d'intervention", $fond->site_intervention);		
                if (!empty($fond->dt_deb_prestation)) {
                   $maDtDeb = $fond->dt_deb_prestation->nice('Europe/Paris', 'fr-FR') . " - ";
                }
                else {
                   $maDtDeb = '';
                }
                if (!empty($fond->dt_fin_prestation)) {
                   $maDtFin = $fond->dt_fin_prestation->nice('Europe/Paris', 'fr-FR');
                }
                else {
                   $maDtFin = '';
                }
		$pdf->ecritLigne("Dates envisagées / effectives", $maDtDeb . $maDtFin);	
		$pdf->ecritLigne("Responsable d'opérations", $fond->responsable_operation);
      }
    }
    $pdf->Ln();     
	
    // Bloc complémentaire de l'administrateur
    // ---------------------------------------
    if ($pdf->monProfil == PROFIL_CC){
		
		// Implantation en magasin
		// -----------------------
		if (!empty($fond->adresses)) {
			$pdf->ecritBloc('Implantation en magasin');
			foreach ($fond->adresses as $adresse) {
				if (!empty($adresse['volume'])) {
					$numAdresse = $adresse['num_seq'] + 1 ;
					$monAdresse = "Adresse n°" . $numAdresse ;
					$pdf->ecritLigne($monAdresse, '');
					
 					$pdf->ecritLigne("    Volume en mètres linéaires" , $adresse['volume']);
					
					$pdf->ecritLigne("    Magasin" , $adresse['magasin']);
					
					if (!empty($adresse['epi_fin'])) {
						$epi = $adresse['epi_deb'] . ' à '. $adresse['epi_fin'] ;
					}
					else {
						$epi = $adresse['epi_deb'];
					}
					$pdf->ecritLigne("    Epi(s)" , $epi);
					
					if (!empty($adresse['travee_fin'])) {
						$travee = $adresse['travee_deb'] . ' à '. $adresse['travee_fin'] ;
					}
					else {
						$travee = $adresse['travee_deb'];
					}
					$pdf->ecritLigne("    Travée(s)" , $travee);				
					
					if (!empty($adresse['tablette_fin'])) {
						$tablette = $adresse['tablette_deb'] . ' à '. $adresse['tablette_fin'] ;
					}
					else {
						$tablette = $adresse['tablette_deb'];
					}
					$pdf->ecritLigne("    Tablette(s)" , $tablette);	
					$pdf->Ln();				
				}
			}
		}
		
		
		// Informations complémentaires pour l'administrateur
		// --------------------------------------------------
		$pdf->ecritBloc('Informations complémentaires pour l\'administrateur');
       	 
		$pdf->ecritLigne('Identifiant du fonds en base',$fond->id);
        
		if (!empty($fond->dt_creation)) {
			$pdf->ecritLigne('Date de création', $fond->dt_creation->nice('Europe/Paris', 'fr-FR'));
		}
		else {
			$pdf->ecritLigne('Date de création', '');
		}

		if (!empty($fond->dt_der_modif)) {
			$pdf->ecritLigne('Date de dernière modification', $fond->dt_der_modif->nice('Europe/Paris', 'fr-FR'));
		}
		else {
			$pdf->ecritLigne('Date de dernière modification', '');
		}  

		$fond->ind_suppr ? $libsuppr = 'Oui' : $libsuppr = 'Non' ; 
		$pdf->ecritLigne('Le fonds est-il considéré comme supprimé ?', $libsuppr);
		$pdf->ecritLigne('Raison de la suppression',$fond->raison_suppression->raison);

		if (!empty($fond->dt_suppr)) {
			$pdf->ecritLigne('Date de la suppression', $fond->dt_suppr->nice('Europe/Paris', 'fr-FR'));
		}
		else {
			$pdf->ecritLigne('Date de la suppression', '');
		}              

		$pdf->Ln();    
	}
        
    
// Close and output PDF document
// -----------------------------


$pdf->Output(WWW_ROOT . 'files' . DS . 'pdf' . DS . $filename . '.pdf', 'F');

//============================================================+
// END OF FILE
//============================================================+2
