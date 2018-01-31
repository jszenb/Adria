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

	public $monEntite = "";
	public $monProfil = "";

	// Setter pour le nom du fonds (pour le pied de page)
	public function set_nom_entite($value) {
		$this->monEntite = $value;
	}
	
	// Setter pour le profil pour lequel on produit l'état
	public function set_profil($value) {
		$this->monProfil = $value;
	}	
	
    // Ecriture du pied de page
    public function Footer() {
       
        $this->Cell(135, 6, 'Fiche entité documentaire : ' . $this->monEntite . ' - Extraction ADRIA au ' . date('d/m/Y'), 'TB', false, 'L', 0, '', 1, false, 'T', 'M');
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
    
    
    // Calcul à l'échelle 1/2 des dimensions d'une image.
    // On rend une valeur entière forcée par un cast
    // --------------------------------------------------
    public function calculTaille($size) {
        return (int)( ( ( $size / 2.02 ) * 1 ) / 2 ) ;
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

// On fixe le code de l'entité documentaire pour le pied de page :
$pdf->set_nom_entite($entiteDoc->code) ;
$pdf->set_profil($profil) ;


// Pour le profil CA, on adapte le titre avec des informations
// sur l'entité documentaire
// -----------------------------------------------------------
$pdf->Ln(5);
$pdf->SetFont('times', 'B', 12);
$pdf->SetTextColor(0);
$pdf->Write(6, $title . ' : ' . $entiteDoc->nom, '', false, 'L');
$pdf->Ln(10);	

// -----------------------------------------------------------------------------------------
// Ecriture des données 
// ------------------------------------------------------------------------------------------
	
	// Informations générales
	// ------------------------------
    $pdf->ecritLigne('Code', $entiteDoc->code);
	$pdf->ecritLigne('Etablissement', $entiteDoc->etablissement->nom);    
    $pdf->ecritLigne('Adresse 1', $entiteDoc->adresse_1);
    !empty($entiteDoc->adresse_2) ? $pdf->ecritLigne('Adresse 2', $entiteDoc->adresse_2) : '' ;
    !empty($entiteDoc->adresse_3) ? $pdf->ecritLigne('Adresse 2', $entiteDoc->adresse_3) : '' ;    
    $pdf->ecritLigne('Code postal', $entiteDoc->adresse_cp);
    $pdf->ecritLigne('Ville', $entiteDoc->adresse_ville);
    $pdf->ecritLigne('Tél.', $entiteDoc->num_tel);
    $pdf->ecritLigne('Courriel', $entiteDoc->mail);
   
   if (!empty($fond->lieu_conservations)):
        foreach ($fond->lieu_conservations as $lieuConservations): 
            $pdf->ecritLigne('',$lieuConservations->nom . ", " . $lieuConservations->adresse_1 . ", " . $lieuConservations->adresse_cp . " " . $lieuConservations->adresse_ville);
        endforeach;
    endif;
    
	$pdf->Ln();
    
    // Bloc "Référent(e) archives"
	// ------------------------------
    $pdf->ecritBloc('Référent(e) archives');    
       
	if (!empty($entiteDoc->users)){
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetTextColor(0);
        $pdf->Cell(5, 6, '', 'B', 0, '0', false, false, 1);
        $pdf->Cell(40, 6, 'Nom', 'B', 0, '0', false, false, 1);
        $pdf->Cell(40, 6, 'Prénom', 'B', 0, '0', false, false, 1);
        $pdf->Cell(25, 6, 'Tél.', 'B', 0, '0', false, false, 1);
        $pdf->Cell(60, 6, 'Courriel', 'B', 0, '0', false, false, 1);  
        $pdf->Ln();            
        foreach ($entiteDoc->users as $users):
            $pdf->SetFont('times', '', 10);
            $pdf->Cell(5, 6, '', 0, 0, '0', false, false, 1);
            $pdf->Cell(40, 6, $users->nom, 0, 0, '0', false, false, 1);
            $pdf->Cell(40, 6, $users->prenom, 0, 0, '0', false, false, 1);
            $pdf->Cell(25, 6, $users->num_tel, 0, 0, '0', false, false, 1);
            $pdf->Cell(60, 6, $users->mail, 0, 0, '0', false, false, 1);
            $pdf->Ln();
        endforeach;
    }
    else {
        $pdf->ecritLigne('', "Aucun(e) référent(e) archives déclaré(e) pour cette entité documentaire");
    }
    
	$pdf->Ln();
    
    // Bloc "Fonds"
	// ------------------------------
    $pdf->ecritBloc('Fonds'); 
    
	if (!empty($entiteDoc->fonds)){
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetTextColor(0);
        $pdf->Cell(5, 6, '', 'B', 0, '0', false, false, 1);
        $pdf->Cell(101, 6, 'Nom', 'B', 0, '0', false, false, 1);
        $pdf->Cell(40, 6, 'Type de fonds', 'B', 0, '0', false, false, 1);
        $pdf->Cell(12, 6, 'Vol. ml', 'B', 0, '0', false, false, 1);
        $pdf->Cell(12, 6, 'Vol. Go', 'B', 0, '0', false, false, 1);  
        $pdf->Ln();  

		$totalMl = 0.0;
		$totalGo = 0.0;
		foreach ($entiteDoc->fonds as $fonds): 
			
			// Calcul des sommes de volumétrie qu'on affichera après la boucle.
			$totalMl = $totalMl + (float)$fonds->nb_ml;
			$totalGo = $totalGo + (float)$fonds->nb_go;
            
            $pdf->SetFont('times', '', 10);
            $pdf->Cell(5, 6, '', 0, 0, '0', false, false, 1);
            $pdf->Cell(101, 6, $fonds->nom, 0, 0, '0', false, false, 1);
            $pdf->Cell(40, 6, $fonds->type_fond->type, 0, 0, '0', false, false, 1);
            $pdf->Cell(12, 6, $fonds->nb_ml, 0, 0, 'R', false, false, 1);
            $pdf->Cell(12, 6, $fonds->nb_go, 0, 0, 'R', false, false, 1);
            $pdf->Ln();               
        endforeach;
        $pdf->Cell(5, 6, '', 0, 0, '0', false, false, 1);
        $pdf->Cell(101, 6, '', 0, 0, '0', false, false, 1);
        $pdf->Cell(40, 6, 'Total :', 0, 0, 'R', false, false, 1);
        $pdf->Cell(12, 6, $totalMl, 0, 0, 'R', false, false, 1);
        $pdf->Cell(12, 6, $totalGo, 0, 0, 'R', false, false, 1);        
    }
    else {
        $pdf->ecritLigne('', "Aucun fonds déclaré pour cette entité documentaire.");
    }
    
	$pdf->Ln();
    
    // Bloc "Lieu(x) de conservation"
	// ------------------------------
    $pdf->ecritBloc('Lieu(x) de conservation'); 
    
	if (!empty($entiteDoc->lieu_conservations)){
        $pdf->SetFont('times', 'B', 10);
        $pdf->SetTextColor(0);
        $pdf->Cell(5, 6, '', 'B', 0, '0', false, false, 1);
        $pdf->Cell(61, 6, 'Nom', 'B', 0, '0', false, false, 1);
        $pdf->Cell(40, 6, 'Adresse', 'B', 0, '0', false, false, 1);
        $pdf->Cell(22, 6, 'Code postal', 'B', 0, '0', false, false, 1);
        $pdf->Cell(42, 6, 'Ville', 'B', 0, '0', false, false, 1);  
        $pdf->Ln();  
		foreach ($entiteDoc->lieu_conservations as $lieuConservations):             
            $pdf->SetFont('times', '', 10);
            $pdf->Cell(5, 6, '', 0, 0, '0', false, false, 1);
            $pdf->Cell(61, 6, $lieuConservations->nom, 0, 0, '0', false, false, 1);
            $pdf->Cell(40, 6, $lieuConservations->adresse_1, 0, 0, '0', false, false, 1);
            $pdf->Cell(22, 6, $lieuConservations->adresse_cp, 0, 0, '0', false, false, 1);
            $pdf->Cell(42, 6, $lieuConservations->adresse_ville, 0, 0, '0', false, false, 1);
            $pdf->Ln();               
        endforeach;      
    }
    else {
        $pdf->ecritLigne('', "Aucun lieu de conservation déclaré pour cette entité documentaire.");
    }
    
	$pdf->Ln();       
    // Bloc de graphiques
	// ------------------------------
    if ($nb_TypeFonds != 0) { 
        $size= getimagesize($temp_img_stat_typeFonds);
        $wmm =  $pdf->calculTaille( $size[0] ) ;
        $hmm =  $pdf->calculTaille( $size[1] ) ;
//$pdf->Write(6, 'w : ' . (int)$wmm . ' h : ' . (int)$hmm) ;
//$pdf->Ln();
 
        //$pdf->Image($temp_img_stat_typeFonds, '', '', 0, 0, 'PNG', $temp_img_stat_typeFonds, 'N', true);
        $pdf->Image($temp_img_stat_typeFonds, '', '', $wmm , $hmm, 'PNG', $temp_img_stat_typeFonds, 'N', true, 200, 'C');
        $pdf->Ln();      
    }
    
    if ($nb_Thematiques != 0) {   
        $size= getimagesize($temp_img_stat_thematiques);
        $wmm =  $pdf->calculTaille( $size[0] ) ;
        $hmm =  $pdf->calculTaille( $size[1] ) ;
    
        //$pdf->Image($temp_img_stat_thematiques, '', '', 0, 0, 'PNG', $temp_img_stat_thematiques, 'N', true);
        $pdf->Image($temp_img_stat_thematiques, '', '', $wmm , $hmm, 'PNG', $temp_img_stat_thematiques, 'N', true, 200, 'C');
        $pdf->Ln();      
    }

    if ($nb_Aires != 0) {    
        $size= getimagesize($temp_img_stat_aires);
        $wmm =  $pdf->calculTaille( $size[0] ) ;
        $hmm =  $pdf->calculTaille( $size[1] ) ;  
        //$pdf->Image($temp_img_stat_aires, '', '', 0, 0, 'PNG', $temp_img_stat_aires, 'N', true);
        $pdf->Image($temp_img_stat_aires, '', '', $wmm , $hmm, 'PNG', $temp_img_stat_aires, 'N', true, 200, 'C');
        $pdf->Ln();      
    }

    if ($nb_Dates != 0) {  
        //$pdf->AddPage();

        $pdf->Image($temp_img_stat_dates, '', '', 0, 0, 'PNG', $temp_img_stat_dates, 'N', true);
        $pdf->Ln();  
        /*$pdf->SetFont('times', 'B', 10);
        $pdf->SetTextColor(0);    
        $pdf->Write(6, "Couverture chronologique des fonds (d'après les dates extrêmes)", '', false, 'L');
        $pdf->Ln();
        $pdf->SetFont('times', '', 10);
        $pdf->SetTextColor(0);            
        $pdf->Write(6, "(Attention : les fonds sans dates extrêmes n'apparaissent pas.)", '', false, 'L');
        $pdf->Ln();    */    
    }
    
// Close and output PDF document
// -----------------------------


$pdf->Output(WWW_ROOT . 'files' . DS . 'pdf' . DS . $filename . '.pdf', 'F');

//============================================================+
// END OF FILE
//============================================================+2