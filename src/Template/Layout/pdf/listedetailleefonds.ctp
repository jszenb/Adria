<?php
/********************************************************************************************/
// Application ADRIA
// Listedetailleefonds.ctp
// Template de génération des liste détaillées de fonds
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

	public $monEtablissement = "";

	// Setter pour l'établissement
	public function set_etablissement($value) {
		$this->monEtablissement = $value;
	}
	
	// Setter pour le profil pour lequel on produit l'état
	public function set_profil($value) {
		$this->monProfil = $value;
	}		
	
    // Ecriture du pied de page
    public function Footer() {

        // Page number
        $this->Cell(135, 6, $this->monEtablissement, 'TB', false, 'L', 0, '', 0, false, 'T', 'M');
		
		if ($this->monProfil != PROFIL_CA) {
			$this->Cell(35, 6, 'Extraction ADRIA au ' . date('d/m/Y'), 'TB', false, 'R', 0, '', 0, false, 'T', 'M');
		}
		else {
            $numPage = 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages();
			$this->Cell(35, 6, $numPage, 'TB', false, 'R', 0, '', 0, false, 'T', 'M');
		}
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

if ($profil == PROFIL_CC) {
	$pdf->setPrintHeader(FALSE);
}
else {
	$pdf->setPrintHeader(TRUE);
}
$pdf->setPrintFooter(TRUE);
$pdf->SetFooterFont(['times','','10']);
 
// On spécifie la fonte par défaut
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
 
// On définit les marges : je mets 20mm partout sauf au footer
$pdf->SetMargins(20, 20, 20);
$pdf->SetFooterMargin(18);

// On indique que le dépassement d'une page entraine automatiquement la création d'un saut de page et d'une nouvelle page
$pdf->SetAutoPageBreak(TRUE, 19);

//*******************************************************************************************
// Ecriture de l'état : 
// boucle sur les fonds trouvés, avec rupture de page sur l'établissement puis rupture
// dans le tableau de fonds sur les entités documentaires et les types de fonds
//*******************************************************************************************

// Première page avec le titre du document
$pdf->AddPage();
$premierePage = true;

$pdf->Ln(5);
$pdf->SetFont('times', 'B', 11);
$pdf->SetTextColor(0);
$pdf->Write(6,"Archives scientifiques déclarées pour le GED : liste détaillée des fonds", '', false, 'L');
$pdf->Ln(10);	

// ------------------------------------------------------------------------------------------
// Constitution de l'entête du tableau de résultats : avec le titre des colonnes
// ------------------------------------------------------------------------------------------
$header = array('', 'Nom du fonds', 'Statut juridique', "Mètres\nlinéaires","Nb de\nGo");

$pdf->SetFillColor(255, 255, 255);
$pdf->SetTextColor(0);
$pdf->SetLineWidth(0.3);
$pdf->SetFont('times', 'B', 10);

// Couleur de la bordure du tableau :
$pdf->SetDrawColor(0, 0, 0);

// $w contient la largeur en mm de chaque colonne
$w = array(7, 100, 33, 15, 15);

$num_headers = count($header);

$totalw = array_sum($w);
$totalTotaux = $w[0]+$w[1];

for($i = 0; $i < $num_headers; ++$i) {
	//MultiCell($w,$h,$txt,$border = 0,$align = ‘J’,$fill = false,	$ln = 1,$x = “,	$y = “,	$reseth = true,	$stretch = 0,$ishtml = false,$autopadding = true,$maxh = 0,$valign = ’T’,$fitcell = false ) 
	if ($i <= 1) {
		$pdf->MultiCell($w[$i], 10, $header[$i], 1, 'L', 1,	0, '', '', true, 0, false, true, 30, 'T', false );
	}
	else if ($i == 2) {
        // Statut juridique doit être centré
        $pdf->MultiCell($w[$i], 10, $header[$i], 1, 'C', 1,	0, '', '', true, 0, false, true, 30, 'T', false );
    }
    else {
		$pdf->MultiCell($w[$i], 10, $header[$i], 1, 'R', 1,	0, '', '', true, 0, false, true, 30, 'T', false );
	}
}
$pdf->Ln();
// -----------------------------------------------------------------------------------------
// Ecriture du tableau de données 
// ------------------------------------------------------------------------------------------
$fill = 0; // permet de gérer l'alternance des couleurs dans les lignes de fonds
$ligne = 0; // permet de compter les lignes à gauche du tableau
$repeteEntite = false ; // permet de réécrire une entité si nécessaire
$typeFonds = ""; 
$etablissement = "";
$entiteDoc = "";

$pdf->set_profil($profil) ;

foreach($fonds as $row) {
	
	// MultiCell($w,$h,$txt,$border = 0,$align = ‘J’,$fill = false,	$ln = 1,$x = “,	$y = “,	$reseth = true,	$stretch = 0,$ishtml = false,$autopadding = true,$maxh = 0,$valign = ’T’,$fitcell = false ) 


	// Rupture de page sur l'établissement
	// -----------------------------------
	if ($etablissement != $row->entite_doc->etablissement->nom) {
		
		$etablissement = $row->entite_doc->etablissement->nom;

        
		// On commence une nouvelle page (sauf si c'est la première)
		if (!$premierePage) {
            // On doit écrire les totaux calculés avant de changé d'établissement
            $pdf->Cell($totalTotaux, 6, '', 'T', 0, '', $fill);
            $pdf->Cell($w[2], 6, 'Total : ', 'T', 0, 'R', $fill);            
            $pdf->Cell($w[3], 6, is_numeric($totalMl) ? number_format($totalMl, 1, ',', ' ') : 'inconnu' , 'T', 0, 'R');
            $pdf->Cell($w[4], 6, is_numeric($totalGo) ? number_format($totalGo, 0, ',', ' ') : 'inconnu', 'T', 0, 'R');            
            $pdf->Ln();
        // On met une bordure sur la dernière ligne du tableau avant le saut de page
			$pdf->Cell($totalw, 0, '', '');
			$pdf->AddPage();
		}
		// On recommence à compter les fonds à partir de 0
        $ligne = 0 ; 
        $totalMl = 0 ;
        $totalGo = 0 ;		
		$premierePage = false;
		
		// On passe la casse en gras italique blanc sur fond grisé
		$pdf->SetFont('times', 'B', 10);
		$pdf->SetTextColor(255);
		$pdf->SetFillColor(51,153,255);

		// Indiquer l'établissement dans le pied de page :
		$pdf->set_etablissement($etablissement);
		
		// Ecriture de la ligne
		$pdf->MultiCell($totalw, 6, strtoupper($etablissement), 1, 'C', 1,	0, '', '', true, 0, false, true, 6, 'T', false );
		$pdf->Ln();
	}

	// Rupture interne au tableau sur le type de fonds
	// Si on vient de changer d'établissement ou d'entité
	// on réindique la valeur même si elle n'a pas changé
	// --------------------------------------------------
	$repeteEntite = false ;
	
	if ( $typeFonds != $row->type_fond->type ) {
		// On passe la casse en gras normal noir 
        
		$typeFonds = $row->type_fond->type;
		
		// On n'a pas changé d'entité entre temps
		if ($entiteDoc == $row->entite_doc->nom) {
			$repeteEntite = true;
		}
		
		$pdf->SetFont('times', 'B', 10);
		$pdf->SetTextColor(255);
		$pdf->SetFillColor(204,0,0);
		
		// Ecriture de la ligne
		$pdf->MultiCell($totalw, 6, $typeFonds, 1, 'L', 1,	0, '', '', true, 0, false, true, 30, 'T', false );
		$pdf->Ln();
	}
	
	// Rupture interne au tableau sur l'entité
	// ---------------------------------------
	if ( ( $entiteDoc != $row->entite_doc->nom) || $repeteEntite) {
		// On passe la casse en italique normal noir 
		$entiteDoc = $row->entite_doc->nom;

		$pdf->SetFont('times', 'B', 10);
		$pdf->SetTextColor(0);
		$pdf->SetFillColor(210,210,210);
		
		// Ecriture de la ligne
		$pdf->MultiCell($totalw, 6, $entiteDoc, 1, 'L', 1,	0, '', '', true, 0, false, true, 30, 'T', false );
		$pdf->Ln();
	}
	
	// Ecriture des données de fonds
	// -----------------------------
	$pdf->SetTextColor(0);
	$pdf->SetFont('times', '', '10');
	$pdf->SetFillColor(224, 235, 255);
	
	$ligne++;
	$pdf->Cell($w[0], 6, $ligne, 'LR', 0, 'L', $fill);
	$pdf->Cell($w[1], 6, $row->nom, 'LR', 0, 'L', $fill, false, 1);
	$pdf->Cell($w[2], 6, $row->type_stat_jurid->type, 'LR', 0, 'C', $fill);
	//$pdf->Cell($w[3], 6, number_format($row->nb_ml), 'LR', 0, 'R', $fill);
	//$pdf->Cell($w[4], 6, number_format($row->nb_go), 'LR', 0, 'R', $fill);
	$pdf->Cell($w[3], 6, is_numeric($row->nb_ml) ? number_format($row->nb_ml, 1, ',', ' ') : 'inconnu' , 'LR', 0, 'R', $fill);
	$pdf->Cell($w[4], 6, is_numeric($row->nb_go) ? number_format($row->nb_go, 0, ',', ' ') : 'inconnu', 'LR', 0, 'R', $fill);
	
    if ( is_numeric($row->nb_ml) ) {
        $totalMl += $row->nb_ml ;
    }
    
    if ( is_numeric($row->nb_go) ) {
        $totalGo += $row->nb_go ;
    }
    
	$pdf->Ln();
    
	
	// Pour alterner les couleurs dans les lignes du tableau :
	// décommenter la ligne.
	// -------------------------------------------------------
	//$fill=!$fill;
	
}

// Ecriture de la volumétrie pour le dernier établissmeent (puisqu'on ne boucle pas)
$pdf->Cell($totalTotaux, 6, '', 'T', 0, '', $fill);
$pdf->Cell($w[2], 6, 'Total : ', 'T', 0, 'R', $fill);            
$pdf->Cell($w[3], 6, is_numeric($totalMl) ? number_format($totalMl, 1, ',', ' ') : 'inconnu' , 'T', 0, 'R');
$pdf->Cell($w[4], 6, is_numeric($totalGo) ? number_format($totalGo, 0, ',', ' ') : 'inconnu', 'T', 0, 'R');            
$pdf->Ln();


// Close and output PDF document
// -----------------------------


$pdf->Output(WWW_ROOT . 'files' . DS . 'pdf' . DS . $filename . '.pdf', 'F');

//============================================================+
// END OF FILE
//============================================================+2