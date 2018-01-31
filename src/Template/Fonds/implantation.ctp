<?php 
/********************************************************************************************
** Fichier : recherche.ctp
** Vue gérant l'écran de recherche
** Contrôleur : Fonds
** La particularité de cet écran réside dans la construction en JS (jquery) de la liste des
** critères dynamiquement selon les choix de l'utilisateur. 
********************************************************************************************/
?>
<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">

	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	
</nav>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
	google.charts.load('current', {'packages':['corechart']});
	google.charts.setOnLoadCallback(drawVisualization);

	function drawVisualization() {    
		/* Le tableau de données est le suivant :
		[	'Magasin'	'Volumétrie'	'Epi 1'		'Epi 2'		'...' 		'Epi 10'	]
		[	'Chaîne'	Chiffre			Chiffre		Chiffre		Chiffre 	Chiffre		]
		*/
		var data = new google.visualization.DataTable();
		data.addColumn('string', 'Magasin');
		data.addColumn('number', 'Volumétrie');
		<?php
			for ($i = 0; $i<= MAX_EPI-1 ; $i++ ) { ?>
				data.addColumn('number', 'Epi <?= $i + 1 ?>');
			<?php
			}
		?>			

		<?php

		$nbMagasin=10;
		$j=0;
		$i=0;
		foreach ($adresses as $adresse) {
			$j++;
		}		
		
		// Tableau de données : une ligne par magasin
		$repartitionVolumeEpi = array();
		for ($i = 0 ; $i <= $nbMagasin - 1 ; $i++) {
			$repartitionVolumeEpi[$i] = array();
		}
		
		foreach($adresses as $adresse) {
			$magasin = $adresse['magasin'] ;
			$volume = $adresse['volume'] ;
			$epiDeb = $adresse['epi_deb'] ;
			$epiFin = $adresse['epi_fin'] ;
			
			// Si l'utilisateur n'a pas donné d'épi de fin alors tout est dans l'épi deb
			if (!empty($epiDeb)) {
				if (empty($epiFin)) {
					$epiFin = $epiDeb;
				}
			} else {
				// Si l'épi de début n'a pas été renseigné, on ventile sur tous les épis MAX_EPI
				$epiDeb = 1;
				$epiFin = MAX_EPI;
			}
			
			$nbEpi = $epiFin - $epiDeb + 1 ;
			
			// Il faut maintenant ventiler le volume par épi.
			$volumeParEpi = number_format($volume / $nbEpi, 0) ;
			for ($k = $epiDeb-1 ; $k<= $epiFin-1 ; $k++) {
				$repartitionVolumeEpi[$magasin-1][$k] = $volumeParEpi ;
			}
		
		}
		//print (count($repartitionVolumeEpi));
		// On recalcule les totaux :
		for ($i = 0 ; $i <= $nbMagasin - 1 ; $i++){
			$total = 0;
			for ($j = 0 ; $j <= MAX_EPI - 1  ; $j++){
				if (isset($repartitionVolumeEpi[$i][$j])){
					$total = $total + $repartitionVolumeEpi[$i][$j];
				}
			}
			$repartitionVolumeEpi[$i]["total"] = $total;
		}
		
		?>
			
		// Remplissage du tableau de données
		data.addRows([
		<?php
		$i=0;
		$row = "";
		for ($i = 0 ; $i <=  $nbMagasin - 1 ; $i++) {
			$row = "";
			if ($i != 0) {
				$row = ',' ;
			}
			$num = $i + 1;
			$row = $row . '[\'' . $num . '\',' ;
			$row = $row . $repartitionVolumeEpi[$i]["total"];
				
			for ($j = 0 ; $j <= MAX_EPI - 1  ; $j++) {
				if (isset($repartitionVolumeEpi[$i][$j])){
					$row = $row . ',' . $repartitionVolumeEpi[$i][$j];
				}
				else {
					$row = $row . ',0' ; 
				}
			}
			$row = $row . ']' ;
			print($row) ;
							
		}
			
		?>
		]);		
		
    var options = {
		title : 'Implantation : taux d\'occupation des magasins',
		vAxis: {title: 'Mètres linéraires', gridlines:{count: 10}},
		hAxis: {title: 'Magasin'},
		seriesType: 'bars',
		height: 1000,
		isStacked: true,
		series: {0: {type: 'line'}, 1: {title: 'Magasin '}}
    };

    var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
    chart.draw(data, options);
  }
    </script>
	
<?php 
	// Valeurs élémentaires :
	$maxVolumetrieEpi = 100; // volumétrie maximum de l'épi en metres linéaires
	$nbEpiMagasin = 10 ; // nombre max d'épis dans un magasin
	$nbMagasin = 10 ; // nombre de magasins
	$cmPx = 38 ; // pour la conversion centimètre / pixels. Un cm vaut environ 38 px.
 
	// Valeurs déduites :
    $maxVolumetrieMagasin = $maxVolumetrieEpi * $nbEpiMagasin ; // Volumétrie m.l. maximum en magasin 
	$largeurRectangleMagasin = 2 * $cmPx ;  // On représentera un magasin par un rectangle de largeur 2cm et de hauteur 10cm
	$hauteurRectangleMagasin = 10 * $cmPx  ;
	$largeurCanvas = $nbMagasin * $largeurRectangleMagasin  + 42.5 * $nbMagasin ; // Le canvas de dessin peut encadrer les magasins et on ajoute une marge
	$hauteurCanvas = $hauteurRectangleMagasin + 2 * $cmPx ; // idem en hauteur avec une marge de deux centimètres
?>

	<script type="text/javascript">   
	   function draw() {
		var canvas = document.getElementById('magasin1');
		var hauteurCanvas = <?= $hauteurCanvas ?> ;
		var nbMagasin = <?= $nbMagasin ?> ;
		var cmPx = <?= $cmPx ?>  ;
		var hauteurRectangleMagasin = <?= $hauteurRectangleMagasin ?> ;
		var departTraceHauteur = hauteurCanvas - cmPx ;
		var largeurMagasin = <?= $largeurRectangleMagasin ?> ;
		var hauteurMagasin = <?= $hauteurRectangleMagasin ?> ;
		var magasins =[];
		for (i = 1 ; i <= nbMagasin ; i++) {
			magasins[i] = 0 ;
		}


		<?php
			$i = 0 ;
			foreach ($sommeMagasins as $sommeMagasin) {
				$i = $i + 1 ;
				if ( $sommeMagasin->magasin >= 1 ) {
					?>
					magasins[<?= $sommeMagasin->magasin ?>] = <?= $sommeMagasin->sum ?> ;
					
					<?php
				}
			}
		
		?>
		
		if (canvas.getContext) {
			var ctx = canvas.getContext('2d');
			
			for (i = 1 ; i <= nbMagasin ; i++) {
				ctx.strokeStyle = "black";
				ctx.strokeRect( cmPx * i + largeurMagasin * (i - 1), departTraceHauteur, largeurMagasin, -hauteurMagasin);
				
				// On convertit le métrage linéaire pour le faire tenir dans la jauge :
				hauteurTracee = hauteurRectangleMagasin * magasins[i] / <?= $maxVolumetrieEpi ?> ;
				
				tauxOccupationMagasin = (hauteurTracee * 100 / hauteurRectangleMagasin).toFixed(1) ;
				couleur = "green";

				if (hauteurTracee < (hauteurRectangleMagasin * 75 / 100) ) {
					couleur = "green";
				} else if (hauteurTracee >= (hauteurRectangleMagasin * 75 / 100) && hauteurTracee < (hauteurRectangleMagasin * 90 / 100)) {
					couleur = "orange";
				}
				else {
					couleur = "red";
				}
				
				ctx.fillStyle = couleur ;
				ctx.fillRect( cmPx * i + largeurMagasin * (i - 1), departTraceHauteur, largeurMagasin, -hauteurTracee );
				
				ctx.fillStyle = "black";
				ctx.font = '36px courier';
				ctx.fillText(i, largeurMagasin * (0.5 + (i-1) * 1.5) , hauteurCanvas - 5 ); 
				
				ctx.fillStyle = "black";
				ctx.font = '25px courier';
				ctx.fillText(tauxOccupationMagasin + "%", largeurMagasin * (0.5 + (i-1) * 1.5) , cmPx / 1.5 ); 			
			}

		  }
		}
	</script>
	<style type="text/css">
		  canvas { border: 1px solid black; }
	</style>	
	
<div class="fonds index large-9 medium-8 columns content">
<!-- <div class="right"><?= $this->Html->link(__('Imprimer'), ['action' => 'generatepdf', '?' => ['mode' => 'statistiques' ]], ['title'=>'PDF généré','onclick'=>'javascript:window.open(this.href,\'_blank\',\'toolbar=0,scrollbars=0,location=0,status=0,menubar=0,resizable=0,width=400,height=100\');return false;']) ?></div> -->
<h3><?= __('Implantation') ?></h3>
	<canvas id="magasin1" width="<?= $largeurCanvas ?>" height="<?= $hauteurCanvas ?>"></canvas><br>Représentation symbolique des magasins
	
	<div id="chart_div"></div>
	
</div>


<?php echo $this->Html->script('jquery-2.1.4.min.js'); ?>
 <script type="text/javascript">
$(document).ready(function(){
	draw();
});
</script>



