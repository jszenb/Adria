<?php
/********************************************************************************************/
// Application ADRIA
// VolumetrieParLieuxEtablissementsEntites.ctp
// Volumétrie par lieux de conservation, établissements et entités documentaires
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
		$this->SetFont('times', 'I', '8');
		$this->Cell(257, 6, 'N.B. : les lieux de conservation auxquels aucun fonds n\'est rattaché n\'apparaissent pas dans ce document. Les lieux de conservation dont les fonds ont une volumétrie en mètre linéaire nulle apparaissent.', 'B', false, 'L', 0, '', 0, false, 'T', 'M');
        $this->Ln();
		$this->SetFont('times', '', '10');
		$this->Cell(135, 6, $this->monEtablissement, 'TB', false, 'L', 0, '', 0, false, 'T', 'M');
		$this->Cell(122, 6, 'Extraction ADRIA au ' . date('d/m/Y'), 'TB', false, 'R', 0, '', 0, false, 'T', 'M');
		/*if ($this->monProfil != PROFIL_CA) {
			$this->Cell(122, 6, 'Extraction ADRIA au ' . date('d/m/Y'), 'TB', false, 'R', 0, '', 0, false, 'T', 'M');
		}
		else {
            $numPage = 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages();
			$this->Cell(122, 6, $numPage, 'TB', false, 'R', 0, '', 0, false, 'T', 'M');
		}*/
    }
}

// Timeout pour la production de l'état : 180 secondes.
set_time_limit(180);

// Création d'un document MYPDF dans l'objet $pdf 
// (les constantes sont dans le fichier de config de tcpdf dans le rep. vendor)
$pdf = new MYPDF('L', 'mm', PDF_PAGE_FORMAT, TRUE, 'UTF-8', FALSE);

// Spécification de certains paramètres de TCPDF (içi on spécifie l'auteur par défaut)
$pdf->SetCreator(PDF_CREATOR);
	
// Spécification du header et footer. Toutes les constantes sont dans le fichier de config de tcpdf dans le rep. vendor
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

/*if ($profil == PROFIL_CC) {
	$pdf->setPrintHeader(FALSE);
}
else {
	$pdf->setPrintHeader(TRUE);
}*/
$pdf->setPrintHeader(TRUE);
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
$pdf->Write(6,"Volumétrie par lieux de conservation, établissements et entités documentaires", '', false, 'L');
$pdf->Ln(10);	

// ------------------------------------------------------------------------------------------
// Constitution de l'entête du tableau de résultats : avec le titre des colonnes
// ------------------------------------------------------------------------------------------
$header = array('', 'Lieu de conservation', 'Etablissement', "Entité\ndocumentaire", "Mètres\nlinéaires");

$pdf->SetFillColor(255, 255, 255);
$pdf->SetTextColor(0);
$pdf->SetLineWidth(0.3);
$pdf->SetFont('times', 'B', 10);

// Couleur de la bordure du tableau :
$pdf->SetDrawColor(0, 0, 0);

// $w contient la largeur en mm de chaque colonne
$w = array(7, 170, 30, 30, 20);

$num_headers = count($header);

$totalw = array_sum($w);
$totalTotaux = $w[0]+$w[1];

for($i = 0; $i < $num_headers; ++$i) {
	//MultiCell($w,$h,$txt,$border = 0,$align = ‘J’,$fill = false,	$ln = 1,$x = “,	$y = “,	$reseth = true,	$stretch = 0,$ishtml = false,$autopadding = true,$maxh = 0,$valign = ’T’,$fitcell = false ) 
	if ($i <= 1) {
		$pdf->MultiCell($w[$i], 10, $header[$i], 1, 'L', 1,	0, '', '', true, 0, false, true, 30, 'T', false );
	}
	else if ( ($i == 2) or ($i == 3) ) {
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
$cp = "";

$pdf->set_profil($profil) ;
$pdf->SetTextColor(0);
$pdf->SetFont('times', '', '10');
$pdf->SetFillColor(224, 235, 255);

foreach($fonds as $row) {
	//MultiCell($w,$h,$txt,$border = 0,$align = ‘J’,$fill = false,	$ln = 1,$x = “,	$y = “,	$reseth = true,	$stretch = 0,$ishtml = false,$autopadding = true,$maxh = 0,$valign = ’T’,$fitcell = false ) 
	if ( $cp != substr($row['cp'], 0,2) && $cp != "A renseigner" ) {
		$cp = substr($row['cp'], 0,2);
		
		if (!is_numeric($cp)) {
			$cp = "A renseigner";
		}
		$pdf->SetFont('times', 'B', '10');
		$pdf->MultiCell($totalw, 6, $cp, 1, 'L', 1,	0, '', '', true, 0, false, true, 30, 'T', false );
		$pdf->Ln();
	}
	
	// Ecriture des données de volumétrie
	// -----------------------------
	$pdf->SetTextColor(0);
	$pdf->SetFont('times', '', '10');
	$pdf->SetFillColor(224, 235, 255);
	
	$ligne++;
	$pdf->Cell($w[0], 6, $ligne, 'LR', 0, 'L', $fill);
	$pdf->Cell($w[1], 6, $row['adresse1'] . ' ' .  $row['cp'] . ' ' . $row['ville'] .' (' .  $row['nomLieu'] . ')', 'LR', 0, 'L', $fill, false, 1);
	$pdf->Cell($w[2], 6, $row['etablissement'], 'LR', 0, 'C', $fill);
	$pdf->Cell($w[3], 6, $row['bib'], 'LR', 0, 'C', $fill);
	$pdf->Cell($w[4], 6, is_numeric($row['volume']) ? number_format($row['volume'], 1, ',', ' ') : 'inconnu' , 'LR', 0, 'R', $fill);
    
	$pdf->Ln();
    
}

$pdf->Ln();


// Close and output PDF document
// -----------------------------


$pdf->Output(WWW_ROOT . 'files' . DS . 'pdf' . DS . $filename . '.pdf', 'F');

//============================================================+
// END OF FILE
//============================================================+2