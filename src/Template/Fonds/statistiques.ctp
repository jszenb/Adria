<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	
</nav>
	
	<?php 
	// Si on travaille sur les dates extrêmes, on charge un loader
	if ( ( $typeGraphique == 'timeline') || ( $typeGraphique == 'treemaps' ) || ( $typeGraphique == 'sankey' ) )  { ?>
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<?php } ?>	
	
    <script type="text/javascript">
    <?php if ( ( $typeGraphique != 'timeline') && ( $typeGraphique != 'treemaps' ) && ( $typeGraphique != 'sankey' ) ) { ?>
		// Load the Visualization API and the piechart package.
		google.load("visualization", '1.1', {packages:['corechart', 'bar', 'table']});
		
		// Set a callback to run when the Google Visualization API is loaded.
		google.setOnLoadCallback(drawChart);	
	<?php }
	?>

	<?php
		$width = 1200;
		$height = 700;
		switch ($typeGraphique) {
			case "camembert": ?>
				// Fonction de dessin du graphique 
				function drawChart() {
					// Create the data table.
					var data = new google.visualization.DataTable();
					data.addColumn('string', '<?php echo(addslashes($abscisse)); ?>');
					data.addColumn('number', '<?php echo(addslashes($ordonnee)); ?>');

					<?php
					$j=0;
					$i=0;
					foreach ($query as $resultat) {
						$j++;
					}
					if ( ($abscisse == 'Statut juridique') || 
					     ($abscisse == 'Aires culturelles') || 
						 ($abscisse == 'Disciplines') || 
						 ($abscisse == 'Type de fonds') || 
						 ($abscisse == 'Type de documents' ) || 
						 ($abscisse == "Mode d'entrée" ) || 
						 ($abscisse == 'Type de supports' ) ) {
						$i=0;
						$tab_couleurs = "[";
						foreach ($query as $resultat) {
							if ($i==($j-1)) {
								$tab_couleurs .= "'" . $resultat['couleur'] . "'" ;
							}
							else 
							{
								$tab_couleurs .= "'" . $resultat['couleur'] . "'," ;
							}
							$i++;
						}
						$tab_couleurs .= "]";
					}
					?>
					
					// Remplissage du tableau de données
					data.addRows([
					<?php
					$i=0;
					foreach ($query as $resultat):
						if ($i==($j-1)) {
							echo ('[\''.addslashes($resultat['libelle']).'\','.number_format($resultat['count'],0,'.','').']');
						}
						else {
							 echo ('[\''.addslashes($resultat['libelle']).'\','.number_format($resultat['count'],0,'.','').'],');               
						}
						$i++;
					endforeach;
					
					?>
					]);

					// Options du graphique
					var options = {<?php 
									if ( ($abscisse == 'Statut juridique') || 
										($abscisse == 'Aires culturelles') || 
										($abscisse == 'Disciplines') || 
										($abscisse == 'Type de fonds') || 
										($abscisse == 'Type de documents' ) || 
										($abscisse == "Mode d'entrée" ) || 
										($abscisse == 'Type de supports' ) ) { ?>
									'colors': <?php echo ($tab_couleurs) ?>,
									<?php } ?>
									'title':'<?php addslashes($titre) ?>',
									
									'is3D': false,
								   'width':<?php echo($width) ?>,
								   'height':<?php echo($height) ?>};
					// Instanciation du graphique dans la div adéquate
					var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
					chart.draw(data, options);				
					// Sauvegarde de l'image PNG pour impression :
					google.visualization.events.addListener(chart, 'ready', function () {
						chart_div.innerHTML = '<img src="' + chart.getImageURI() + '">';
						console.log(chart_div.innerHTML);
					});
			
					// Création du code HTML de div pour lien vers l'image
					document.getElementById('png').outerHTML = '<a target="_blank" href="' + chart.getImageURI() + '">Version imprimable du graphique</a>';
					
					// Génération du tableau de donnée dans la div adéquate
					var table = new google.visualization.Table(document.getElementById('table_div'));
					table.draw(data, {showRowNumber: false, width: '50%'});
					
				}
				<?php break; 
			case "colonne": ?>
				// Fonction de dessin du graphique 
				function drawChart() {
					//var data = google.visualization.arrayToDataTable([
					var data = new google.visualization.DataTable();
					data.addColumn('string', '<?php echo($abscisse); ?>');
					data.addColumn('number', '<?php echo($ordonnee); ?>');
						<?php
						$j=0;
						$i=0;
						foreach ($query as $resultat) {
							$j++;
						}
						?>
						
						// Remplissage du tableau de données
						data.addRows([
						<?php
						foreach ($query as $resultat):

						//echo (number_format($resultat['count'],0,'.',''));
							if ($i==($j-1)) {
								echo ('[\''.addslashes($resultat['libelle']).'\','.number_format($resultat['count'],0,'.','').']');
							}
							else {
								 echo ('[\''.addslashes($resultat['libelle']).'\','.number_format($resultat['count'],0,'.','').'],');               
							}
							$i++;
						endforeach;
						?>
					]);
					var options = {
						title: '<?php echo($titre) ?>',
						width:<?php echo($width) ?>,
						chartArea:{top:10},
						height:<?php echo($height) ?>,
						legend: {position: 'bottom'},
						hAxis: { textPosition: 'out', slantedText: 'true', slantedTextAngle: '40' }};
								   
					var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
					chart.draw(data, options);
					
					// Sauvegarde de l'image PNG pour impression :
					google.visualization.events.addListener(chart, 'ready', function () {
						chart_div.innerHTML = '<img src="' + chart.getImageURI() + '">';
						console.log(chart_div.innerHTML);
					});
			
					// Création du code HTML de div pour lien vers l'image
					document.getElementById('png').outerHTML = '<a target="_blank" href="' + chart.getImageURI() + '">Version imprimable du graphique</a>';
					
					// Génération du tableau de donnée dans la div adéquate
					var table = new google.visualization.Table(document.getElementById('table_div'));
					table.draw(data, {showRowNumber: false, width: '50%'});
				}
				<?php 
				break; 
			case "multipleCas9": // même cas que "multiple" mais j'ai dû faire un calcul de pourcentage avant
			case "multiple": ?>
				// Fonction de dessin du graphique 
				function drawChart() {
					//var data = google.visualization.arrayToDataTable([
					var data = new google.visualization.DataTable();
					data.addColumn('string', '<?php echo($abscisse); ?>');
					data.addColumn('number', '<?php echo($ordonnee); ?>');
					data.addColumn('number', '<?php echo($ordonnee2); ?>');
					<?php 
						if ( isset($ordonnee3) && ( !empty($ordonnee3) ) ) { ?>
							data.addColumn('number', '<?php echo($ordonnee3); ?>');
						<?php 
						}
						$j=0;
						$i=0;
						foreach ($query as $resultat) {
							$j++;
						}
						?>
						
						// Remplissage du tableau de données
						data.addRows([
						<?php
						if ( isset($ordonnee3) && ( !empty($ordonnee3) ) ) {
							$somme = 0 ;
							$somme2 = 0 ;
							foreach ($query as $resultat):
								$resultat['somme'] == '' ? $somme = 0 : $somme = number_format($resultat['somme'],0,'.','') ;
								$resultat['somme2'] == '' ? $somme2 = 0 : $somme2 = number_format($resultat['somme2'],0,'.','') ;
								if ($i==($j-1)) {
									echo ('[\''.addslashes($resultat['libelle']).'\','.$resultat['count'].','.$somme.','.$somme2.']');
								}
								else {
									echo ('[\''.addslashes($resultat['libelle']).'\','.$resultat['count'].','.$somme.','.$somme2.'],');               
								}
								$i++;
							endforeach;														
						}
						else if ($typeGraphique == "multipleCas9") {
							foreach($totaux as $total) {
								// La requête $totaux ne contient qu'un compte et une somme, il n'y a donc qu'une seule ligne
								$totalFonds = $total['totalFonds'];
								$totalML = $total['totalML'];
							}
							// Conversion des résultat en pourcentage avant réalisation du graphique
							foreach ($query as $resultat):
								$res = $resultat['count'];
								$som = $resultat['somme'];
								$res = $res / $totalFonds * 100;
								$som = $som / $totalML * 100;
								if ($i==($j-1)) {
									echo ('[\''.$resultat['libelle'].'\','.$res.','.$som.']');
								}
								else {
									echo ('[\''.$resultat['libelle'].'\','.$res.','.$som.'],');               
								}
								$i++;
							endforeach;						
						}
						else {
							$somme = 0 ;
							foreach ($query as $resultat):
							    $resultat['somme'] == '' ? $somme = 0 : $somme = number_format($resultat['somme'],0,'.','') ;
								if ($i==($j-1)) {
									echo ('[\''.$resultat['libelle'].'\','.$resultat['count'].','.$somme.']');
								}
								else {
									echo ('[\''.$resultat['libelle'].'\','.$resultat['count'].','.$somme.'],');               
								}
								$i++;
							endforeach;		
						}							

						?>
					]);
					var options = {

						width: <?php echo($width); ?>,
						height: <?php echo($height); ?>,
						chart: {
							title: '<?php echo($titre); ?>' 
						},
						bars: 'horizontal' ,
						chartArea:{top:0},
						series: {
							0: { axis: '<?php echo($ordonnee); ?>' }, 
							1: { axis: '<?php echo($ordonnee2); ?>' } 
							<?php if ( isset($ordonnee3) && ( !empty($ordonnee3) ) ) { ?>
							, 2: { axis: '<?php echo($ordonnee3); ?>' }
							<?php } ?>,
						},
						axes: {
							x: {
								'<?php echo($ordonnee); ?>': {label: '<?php echo($ordonnee); ?>', side: 'top'}, 
								'<?php echo($ordonnee2); ?>': {side: 'right', label: '<?php echo($ordonnee2); ?>'} 
								<?php if ( isset($ordonnee3) && ( !empty($ordonnee3) ) ) { ?>
								, '<?php echo($ordonnee3); ?>': {side: 'right', label: '<?php echo($ordonnee3); ?>'}
								<?php } ?>,
							},
							y: {
								0: { side: 'bottom'} 
							}
						}				
					};

				
					
					var chart = new google.charts.Bar(document.getElementById('chart_div'));
					chart.draw(data, options);	
					
					// Génération du tableau de donnée dans la div adéquate
					var table = new google.visualization.Table(document.getElementById('table_div'));
					table.draw(data, {showRowNumber: false, width: '50%'});
					
				}
				<?php 
				break; 	
			case "timeline": ?>
				<?php
				$j=0;
				$i=0;
				foreach ($query as $resultat) {
					$j++;
				}
					
				if ($j > 0) {
					
				?>			
					google.charts.load('current', {'packages':['timeline']});
					google.charts.setOnLoadCallback(drawChart);
					function drawChart() {
						var container = document.getElementById('chart_div');
						var chart = new google.visualization.Timeline(container);
						var dataTable = new google.visualization.DataTable();

						dataTable.addColumn({ type: 'string', id: 'Fonds' });
						dataTable.addColumn({ type: 'string', id: 'dummy bar label' });
						dataTable.addColumn({ type: 'string', role: 'tooltip', 'p': {'html': true} });				
						dataTable.addColumn({ type: 'date', id: 'Début' });
						dataTable.addColumn({ type: 'date', id: 'Fin' });
						

								
						// Remplissage du tableau de données
						dataTable.addRows([
						<?php
						foreach ($query as $resultat):
							// On construit le libellé à afficher lorsqu'on survole une ligne du graphique
							$debut = $resultat['debut'];
							$fin = $resultat['fin'];
							
							// Gestion des fonds n'ayant qu'une année de renseignée : 
							// on aligne la date manquante sur la seule date connue. 
							if (empty($fin) and !empty($debut)) {
								$fin = $debut;
							}
							if (empty($debut) and !empty($fin)) {
								$debut = $fin;
							}

							$diff = $fin - $debut;
							
							$label = '<b>Fonds '.addslashes($resultat['nom']).'</b><hr>'.$debut.' - '.$fin.'<br>  '. $diff. ' année(s)<br><br>';
							if ($i==($j-1)) {
								echo ('[\''.addslashes($resultat['nom']).'\',\''.addslashes($resultat['nom']).'\',\''.$label.'\', new Date('.$debut.', 0), new Date('.$fin.', 11)]');
							//	echo ('[\''.addslashes($resultat['nom']).'\',null,\''.$label.'\', new Date('.$resultat['debut'].', 0), new Date('.$resultat['fin'].', 11)]');
							//	echo ('[\''.addslashes($resultat['nom']).'\', new Date('.$resultat['debut'].', 0), new Date('.$resultat['fin'].', 11)]');
							}
							else {
								echo ('[\''.addslashes($resultat['nom']).'\',\''.addslashes($resultat['nom']).'\',\''.$label.'\', new Date('.$debut.', 0), new Date('.$fin.', 11)],');             
							//	echo ('[\''.addslashes($resultat['nom']).'\', new Date('.$resultat['debut'].', 0), new Date('.$resultat['fin'].', 12)],');             
							}
							$i++;
						endforeach;
						?>
						]);				
						
						var options = {
							height: 1000,
							focusTarget: 'category',
							tooltip: {isHtml: true},
							timeline: { showRowLabels: false }
						};
						
						chart.draw(dataTable, options);
						document.getElementById('png').outerHTML = '<b>Couverture chronologique des fonds (d\'après les dates extrêmes)</b> <br>Attention : les fonds sans dates extrêmes n\'apparaissent pas.';
						
					}
				<?php
				}
				else { ?>
					google.charts.load('current', {'packages':['timeline']});
					google.charts.setOnLoadCallback(drawChart);				
					function drawChart() {
						document.getElementById('png').outerHTML = '<b>Pas de données pour l\'entité documentaire sélectionnée</b>';
					}
				<?php 
				}
				break; 
			case "stacked": 
				?>
				//google.charts.load("current", {packages:["corechart"]});
				function drawChart() {
					var data = google.visualization.arrayToDataTable(<?php echo ($query) ; ?>);

					var view = new google.visualization.DataView(data);
					
					var options = {
						colors: <?php echo ($tab_couleurs); ?>,
						title: '<?php echo ($titre); ?>',
						width: <?php echo ($width); // j'agrandis car le graphique est assez petit en réalité. Idem pour la hauteur ?>,
						height: <?php echo ($height*2); ?>,
						legend: { position: 'right', maxLines: 5 },
						bar: { groupWidth: '80%' },
						hAxis: {minValue: 0},
						isStacked: 'percent',
						chartArea:{top:10}						
					};	
					var chart = new google.visualization.BarChart(document.getElementById("chart_div"));
					chart.draw(view, options);		
					
					// Sauvegarde de l'image PNG pour impression :
					google.visualization.events.addListener(chart, 'ready', function () {
						chart_div.innerHTML = '<img src="' + chart.getImageURI() + '">';
						console.log(chart_div.innerHTML);
						});
			
					// Création du code HTML de div pour lien vers l'image
					document.getElementById('png').outerHTML = '<a target="_blank" href="' + chart.getImageURI() + '">Version imprimable du graphique</a>';					
					
					// Génération du tableau de donnée dans la div adéquate
					var table = new google.visualization.Table(document.getElementById('table_div'));
					table.draw(data, {showRowNumber: false, width: '100%'});
				}
				<?php
				break; 	
			case "sankey": 
				?>
				google.charts.load("current", {'packages':["sankey"]});
				google.charts.setOnLoadCallback(drawChart);
				function drawChart() {
					
					var data = new google.visualization.DataTable();
    
					data.addColumn('string', 'Discipline');
					data.addColumn('string', 'Aire culturelle');
					data.addColumn('number', 'Nombre de fonds');		
					data.addRows(<?php echo ($query) ; ?>);	
					//var data = google.visualization.arrayToDataTable(<?php echo ($query) ; ?>);
					var colors = (<?php echo ($tab_couleurs) ; ?>);

					//var view = new google.visualization.DataView(data);
					var options = {
						width: <?php echo ($width)*1.2; ?>,
						height: <?php echo ($height)*2; ?>,
						sankey: {
							node: {
								colors: colors,
								interactivity: true
							},
							link: {
								colorMode: 'gradient',
								colors: colors
							}
						}
					};
					/*var options = {
						colors: <?php echo ($tab_couleurs); ?>,
						title: '<?php echo ($titre); ?>',
						width: <?php echo ($width); // j'agrandis car le graphique est assez petit en réalité. Idem pour la hauteur ?>,
						height: <?php echo ($height*2); ?>,
						legend: { position: 'right', maxLines: 5 },
						bar: { groupWidth: '80%' },
						hAxis: {minValue: 0},
						isStacked: 'percent',
						chartArea:{top:10}						
					};*/	
					var chart = new google.visualization.Sankey(document.getElementById("chart_div"));
					chart.draw(data, options);		
								
				}
				<?php
				break; 				
			default :
				break;
		} ?>
    </script>

<div class="fonds index large-9 medium-8 columns content">
<!-- <div class="right"><?= $this->Html->link(__('Imprimer'), ['action' => 'generatepdf', '?' => ['mode' => 'statistiques' ]], ['title'=>'PDF généré','onclick'=>'javascript:window.open(this.href,\'_blank\',\'toolbar=0,scrollbars=0,location=0,status=0,menubar=0,resizable=0,width=400,height=100\');return false;']) ?></div> -->
<h3><?= __('Statistiques') ?></h3>
    <?= $this->Form->create('',['id'=>'formStat']) ?>
    <fieldset>
        <legend><?= __('Choisissez le graphique à afficher') ?></legend>

        <?php
			$options = [
			'1' => '1 Volumétrie totale des fonds exprimée en mètres linéaires par entité documentaire ', 
			'2' => '2 Volumétrie totale des fonds exprimée en mètres linéaires par établissement',
			'3' => '3 Volumétrie totale des fonds exprimée en giga-octets par entité documentaire',
			'4' => '4 Volumétrie totale des fonds exprimée en giga-octets par établissement',
			'5' => '5 Nombre total de fonds par entité documentaire',
			'6' => '6 Nombre total de fonds par établissement',
			'7' => '7 Comparatif des volumétries par entité documentaire',			
			'8' => '8 Comparatif des volumétries par établissement',
			'9' => '9 Répartition des fonds par type de fonds (% établis d\'après le nombre de fonds)',			
			'22' => '9bis Répartition des fonds par type de fonds (% établis d\'après la volumétrie ml)',
			'10' => '10 Répartition des fonds par type de documents (% établis d\'après le nombre de fonds)',
			'11' => '11 Répartition des fonds par type de supports (% établis d\'après le nombre de fonds)',			
			'12' => '12 Répartition des fonds par état de traitement (% établis d\'après le nombre de fonds et la volumétrie ml)',
			'13' => '13 Répartition des fonds par statut juridique (% établis d\'après le nombre de fonds)',
			'14' => "14 Répartition des fonds par mode d’entrée (% établis d'après le nombre de fonds)",
			'15' => '15 Couverture chronologique des fonds (d\'après les dates extrêmes)',
			'16' => '16 Répartition par disciplines',
			'17' => '17 Répartition par aires culturelles',
			'18' => '18 Répartition des aires culturelles par discipline (d\'après la volumétrie ml)',
			'19' => '19 Répartition des disciplines par aire culturelle (d\'après la volumétrie ml)',
			'20' => '20 Répartition des aires culturelles par discipline (d\'après le nombre de fonds)',			
			'21' => '21 Répartition des disciplines par aire culturelle (d\'après le nombre de fonds)'			
			
			];
			?>
			<table class="recherche">
				<tr class="recherche">
					<td class="recherche" width="70%">
						<?php echo $this->Form->input('typestat', ['options' => $options, 'label' => '', 'class' => 'recherche', 'empty' => true, 'onChange' => "statChange();"]);	?>
					</td>
					<td class="recherche" width="30%">
						<?php echo $this->Form->input('entitedoc', ['options' => $entiteDocs, 'label' => '', 'empty' => 'Choisissez une entité documentaire dans la liste ci-dessous', 'class' => 'recherche', 'onChange' => "entiteChange();"]); ?>
					</td>
					<!--
					<td class="recherche" width="20%">
						<?php /* echo $this->Form->button('Afficher', ['type' => 'submit']); */ ?>
					</td>
					-->
				</tr>
			</table>

    </fieldset>
    <?= $this->Form->end() ?>
	<align="right"><div id="png"></div></align>
	<center><div id="chart_div"></div></center>
	<center><div id="table_div"></div></center>

</div>
<?php echo $this->Html->script('jquery-2.1.4.min.js'); ?>
<script type="text/javascript">
$(document).ready(function() {
	var entDoc = '<?php echo $entiteDocDates ; ?>' ;
	
	// Ajout d'une possibilité de génération de graphique on le met après la première ligne
	//$('#entitedoc').append($("<option></option>").val('all').html("Toutes entités"));
	$('#entitedoc :nth-child(1)').after("<option value='all'>Toutes entités</option>");
	$('#entitedoc').hide();
	
	
	if (entDoc != '') {
		$('#entitedoc option[value='+entDoc+']').prop('selected', true);
		$('#entitedoc').show();
	}
});
function statChange() {
	
	// On efface le graphique précédent :
	$('#png').empty();
	$('#chart_div').empty();
	$('#table_div').empty();
	
	// Pour les graphiques 9 à 17, on peut sélectionner une entité documentaire :
	switch($('#typestat').val()) {	
		case '9':
		case '10':
		case '11':
		case '12':
		case '13':
		case '14':
		case '15':
		case '16':		
		case '17':
		case '22':		
			$('#entitedoc option[value="all"]').prop('selected', true);
			$('#entitedoc').show();
			$('#entitedoc').css('border','2px solid');
			$('#entitedoc').css('border-color','red');		
			$('#formStat').submit();			
			break;
		default:
			$('#entitedoc option[value=""]').prop('selected', true);
			$('#entitedoc').hide();
			$('#formStat').submit();
	}
/*	if ($('#typestat').val() == '15') {
		$('#entitedoc').show();
		$('#entitedoc').css('border','2px solid');
		$('#entitedoc').css('border-color','red');
	}
	else {
		$('#entitedoc option[value=""]').prop('selected', true);
		$('#entitedoc').hide();
		$('#formStat').submit();
	}
*/
}
function entiteChange() {
	
	if ($('#entitedoc').val() != '') {
		$('#formStat').submit();
	}
}
</script> 