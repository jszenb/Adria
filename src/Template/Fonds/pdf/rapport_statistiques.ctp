<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = 'CakePHP: the rapid development php framework';
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
		Campus Condorcet - Cartographie dynamique des archives
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css('base.css') ?>
    <?= $this->Html->css('cake.css') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>


</head>


    <script type="text/javascript">
    // Load the Visualization API and the piechart package.
    google.load('visualization', '1.0', {'packages':['corechart']});
	google.load("visualization", "1.1", {'packages':['bar']});

    // Set a callback to run when the Google Visualization API is loaded.
    google.setOnLoadCallback(drawChart);

	<?php
		$width = 1200;
		$height = 500;
		switch ($typeGraphique) {
			case "camembert": ?>
				// Fonction de dessin du graphique 
				function drawChart() {
					// Create the data table.
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
						if ($i==($j-1)) {
							echo ('[\''.addslashes($resultat['libelle']).'\','.$this->Number->precision($resultat['count'],0).']');
						}
						else {
							 echo ('[\''.addslashes($resultat['libelle']).'\','.$this->Number->precision($resultat['count'],0).'],');               
						}
						$i++;
					endforeach;
					?>
					]);
					// Options du graphique
					var options = {'title':'<?php echo($titre) ?>',
								   'width':<?php echo($width) ?>,
								   'height':<?php echo($height) ?>};
						// Instantiate and draw our chart, passing in some options.
					var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
					chart.draw(data, options);
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
							if ($i==($j-1)) {
								echo ('[\''.addslashes($resultat['libelle']).'\','.$this->Number->precision($resultat['count'],0).']');
							}
							else {
								 echo ('[\''.addslashes($resultat['libelle']).'\','.$this->Number->precision($resultat['count'],0).'],');               
							}
							$i++;
						endforeach;
						?>
					]);
					var options = {
						title: '<?php echo($titre) ?>',
						width:<?php echo($width) ?>,
						height:<?php echo($height) ?>};
								   
					var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
					chart.draw(data, options);
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
							foreach ($query as $resultat):
								if ($i==($j-1)) {
									echo ('[\''.addslashes($resultat['libelle']).'\','.$resultat['count'].','.$resultat['somme'].','.$resultat['somme2'].']');
								}
								else {
									echo ('[\''.addslashes($resultat['libelle']).'\','.$resultat['count'].','.$resultat['somme'].','.$resultat['somme2'].'],');               
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
							foreach ($query as $resultat):
								if ($i==($j-1)) {
									echo ('[\''.$resultat['libelle'].'\','.$resultat['count'].','.$resultat['somme'].']');
								}
								else {
									echo ('[\''.$resultat['libelle'].'\','.$resultat['count'].','.$resultat['somme'].'],');               
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

						series: {
							0: { axis: '<?php echo($ordonnee); ?>' }, 
							1: { axis: '<?php echo($ordonnee2); ?>' } 
							<?php if ( isset($ordonnee3) && ( !empty($ordonnee3) ) ) { ?>
							, 2: { axis: '<?php echo($ordonnee3); ?>' }
							<?php } ?>,
						},
						axes: {
							y: {
								'<?php echo($ordonnee); ?>': {label: '<?php echo($ordonnee); ?>'}, 
								'<?php echo($ordonnee2); ?>': {side: 'right', label: '<?php echo($ordonnee2); ?>'} 
								<?php if ( isset($ordonnee3) && ( !empty($ordonnee3) ) ) { ?>
								, '<?php echo($ordonnee3); ?>': {side: 'right', label: '<?php echo($ordonnee3); ?>'}
								<?php } ?>,
							}
						}				
					};

					var chart = new google.charts.Bar(document.getElementById('chart_div'));
					chart.draw(data, options);
				}
				<?php 
				break; 				
			default :
				break;
		} ?>
    </script>
<body>
<div class="fonds index large-9 medium-8 columns content">
<h3><?= __('Statistiques') ?></h3>
	<center><div id="chart_div"></div> </center>
</div>
</body>
</html>