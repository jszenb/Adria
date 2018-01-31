<?php
/********************************************************************************************/
// Application ADRIA
// listefonds.ctp
// Template de génération des liste de fonds depuis les écrans  de liste des fonds
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
	public $monProfil = "";

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

		if ($this->monProfil != PROFIL_CA) {
			$this->Cell(135, 6, 'Extraction ADRIA', 'TB', false, 'L', 0, '', 0, false, 'T', 'M');
		}
		else {
			$this->Cell(135, 6, $this->monEtablissement, 'TB', false, 'L', 0, '', 0, false, 'T', 'M');
		}
		$this->Cell(35, 6, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 'TB', false, 'R', 0, '', 0, false, 'T', 'M');
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

// Pour le profil CA, on adapte le titre avec des informations
// sur l'entité documentaire
// -----------------------------------------------------------

if ($typeUserEnSession == PROFIL_CA && $mode == "user") {
	foreach ($fonds as $fond) {
		$codeEntite = $fond->entite_doc->code;
		$nomEntite = $fond->entite_doc->nom;
		$title = $title.' '.$nomEntite.' ('.$codeEntite.')';
		break;
	}
}

$pdf->Ln(5);
$pdf->SetFont('times', 'B', 11);
$pdf->SetTextColor(0);
$pdf->Write(6, $title, '', false, 'L');
$pdf->Ln(10);	

// Sous-titre avec les cumuls de volumétrie
// ----------------------------------------
if ($mode=="user") { 
	$sous_libelle = 'Total : ' . $totalFonds . ' fonds représentant ' . str_replace(',','', $totalMlRecherche) . " ml (sur ". str_replace(',','',$totalMl). ") et "
					. str_replace(',','',$totalGoRecherche) . " Go (sur ". str_replace(',','',$totalGo) . ")" ;
}
else {
	$sous_libelle = 'Total : ' . $totalFonds . ' fonds représentant ' . str_replace(',','', $totalMlRecherche) . " ml et "
					. str_replace(',','',$totalGoRecherche) . " Go" ;		
}
$pdf->SetFont('times', '', 11);
$pdf->SetTextColor(0);
$pdf->Write(6, $sous_libelle, '', false, 'L');
$pdf->Ln(10);	

// ------------------------------------------------------------------------------------------
// Constitution de l'entête du tableau de résultats : avec le titre des colonnes
// ------------------------------------------------------------------------------------------

// Création de deux tableaux contenant les headers et les dimensions des colonnes ($w)
if ($mode != "user" ) { 
	$header = array('Nom', 'Etablissement', "Entité\ndocumentaire", "Type de\nfonds", "Mètres\nlinéaires", "Nb de\nGo");
	$w = array(75, 25, 25, 25, 15, 15);
}
else {
	$header = array('Nom', "Type de fonds", "Mètres\nlinéaires", "Nb de\nGo");
	$w = array(110, 30, 15, 15);
}

$num_headers = count($header);

$totalw = array_sum($w);

$pdf->SetFillColor(255, 255, 255);
$pdf->SetTextColor(0);
$pdf->SetLineWidth(0.3);
$pdf->SetFont('times', 'B', 10);

// Couleur de la bordure du tableau :
$pdf->SetDrawColor(0, 0, 0);


for($i = 0; $i < $num_headers; ++$i) {
	//MultiCell($w,$h,$txt,$border = 0,$align = ‘J’,$fill = false,	$ln = 1,$x = “,	$y = “,	$reseth = true,	$stretch = 0,$ishtml = false,$autopadding = true,$maxh = 0,$valign = ’T’,$fitcell = false ) 
	if ($i > ( $num_headers-3 ) ) {
		$pdf->MultiCell($w[$i], 10, $header[$i], 1, 'R', 1,	0, '', '', true, 0, false, true, 30, 'T', false );
	}
	else {
		$pdf->MultiCell($w[$i], 10, $header[$i], 1, 'L', 1,	0, '', '', true, 0, false, true, 30, 'T', false );
	}
}
$pdf->Ln();

// -----------------------------------------------------------------------------------------
// Ecriture du tableau de données 
// ------------------------------------------------------------------------------------------

foreach($fonds as $row) {
	
	$pdf->set_etablissement($row->entite_doc->etablissement->nom) ;
	$pdf->set_profil($typeUserEnSession) ;
	
	// Ecriture des données de fonds
	// -----------------------------
	$pdf->SetTextColor(0);
	$pdf->SetFont('times', '', '10');
	$pdf->SetFillColor(224, 235, 255);
	
	$pdf->Cell($w[0], 6, $row->nom, 'LR', 0, 'L', false, false, 1);
	
	$index = 0;
	
	if ($mode != "user" ) {
		$pdf->Cell($w[1], 6, $row->entite_doc->etablissement->code, 'LR', 0, 'L', false);
		$pdf->Cell($w[2], 6, $row->entite_doc->code, 'LR', 0, 'L', false);
		$index = 2;
	}
	
	$pdf->Cell($w[$index+1], 6, $row->type_fond->type, 'LR', 0, 'L',false, false, 1);
	$pdf->Cell($w[$index+2], 6, is_numeric($row->nb_ml) ? number_format($row->nb_ml, 2, ',', ' ') : 'inconnu' , 'LR', 0, 'R', false);
	$pdf->Cell($w[$index+3], 6, is_numeric($row->nb_go) ? number_format($row->nb_go, 2, ',', ' ') : 'inconnu', 'LR', 0, 'R', false);
	
	$pdf->Ln();
	
	// Pour alterner les couleurs dans les lignes du tableau :
	// décommenter la ligne.
	// -------------------------------------------------------
	//$fill=!$fill;
	
}

// Ligne additionnelle pour avoir une bordure sur la ligne finale du tableau
// -------------------------------------------------------------------------
$pdf->Cell($totalw, 0, '', 'T');

// Close and output PDF document
// -----------------------------


$pdf->Output(WWW_ROOT . 'files' . DS . 'pdf' . DS . $filename . '.pdf', 'F');

//============================================================+
// END OF FILE
//============================================================+2