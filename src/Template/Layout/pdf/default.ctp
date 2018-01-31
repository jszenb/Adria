<?php

/********************************************************************************************/
// Application ADRIA
// default.ctp
// Template de génération par défaut par appel à des vues HTML
//
// Campus Condorcet (2016)
/********************************************************************************************/

// inclusion de la librairie TCPDF
// -------------------------------
require_once ROOT . DS . 'vendor' . DS . 'tcpdf' . DS . 'tcpdf.php'; 
set_time_limit(180);

// Création d'un document TCPDF avec les variables par défaut
$pdf = new TCPDF('P', 'mm', PDF_PAGE_FORMAT, TRUE, 'UTF-8', FALSE);
$pdf->SetCreator(PDF_CREATOR);
	
// Spécification du header et footer. Toutes les constantes sont dans le fichier de config de tcpdf dans le rep. vendor
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
$pdf->setPrintHeader(TRUE);
$pdf->setPrintFooter(TRUE);
 $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
 
// On définit les marges : je mets 25mm partout, ce qui souvent standard en A4.
$pdf->SetMargins(20, 20, 20);
$pdf->setFooterFont(['helvetica', '', 9]);
$pdf->SetFooterMargin(10);
 
// On indique que le dépassement d'une page entraine automatiquement la création d'un saut de page et d'une nouvelle page
$pdf->SetAutoPageBreak(TRUE);
 
// La fonte et la couleur à utiliser dans la page qui va être créée
$pdf->SetFont('helvetica', '', 10);
$pdf->setColor('text', 0, 0, 0);

// Début de la création de l'état 
// ------------------------------
$pdf->AddPage();
// Appel à une vue dont on récupère le contenu sur la page
// créée. L'AutoBreak fait la pagination
// ---------------------------------------------------------
$pdf->writeHTML($this->fetch('content'), TRUE, FALSE, TRUE, FALSE, '');

// Fermeture de l'état récupéré par la vue
// ---------------------------------------
$pdf->lastPage();

// On indique à TCPDF que le fichier doit être enregistré sur le serveur 
// ---------------------------------------------------------------------
$pdf->Output(WWW_ROOT . 'files' . DS . 'pdf' . DS . $filename . '.pdf', 'F');
?>
