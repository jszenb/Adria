<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<script type="text/javascript">
	// Load the Visualization API and the piechart package.
	google.load("visualization", '1.1', {packages:['corechart', 'bar']});	
</script>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); 
	
	// L'utilisateur CC peut effacer un établissement 
	if ($typeUserEnSession == PROFIL_CC) { ?>
	<ul class="side-nav">
		<li><?= $this->Html->link(__('Modifier l\'entité documentaire'), ['action' => 'edit', $entiteDoc->id]) ?></li>
		<li><?= $this->Form->postLink(__('Supprimer l\'entité documentaire'), ['action' => 'delete', $entiteDoc->id], ['confirm' => __('Voulez-vous vraiment supprimer l\'entité documentaire {0} ?', $entiteDoc->code)]) ?> </li>	
	</ul>
	<?php } ?>
</nav>
<div class="entiteDocs view large-9 medium-8 columns content">
    <h3>Entité documentaire : <?= h($entiteDoc->nom) ?> </h3>

	<div class="right"><?= $this->Html->link(__('Télécharger'), ['action' => 'generatepdf', '?' => ['id' => $entiteDoc->id ]], ['title'=>'PDF généré','onclick'=>'javascript:window.open(this.href,\'_blank\',\'toolbar=0,scrollbars=0,location=0,status=0,menubar=0,resizable=0,width=400,height=150\');return false;']) ?></div>	
	<h4>&nbsp;</h4>
	<table class="vertical-table">
        <!-- <tr>
            <th><?= __('Nom') ?></th>
            <td><?= h($entiteDoc->nom) ?></td>
        </tr> -->
        <tr>
            <th><?= __('Code') ?></th>
            <td><?= h($entiteDoc->code) ?></td>
        </tr>
        <tr>
            <th><?= __('Etablissement') ?></th>
            <td><?= $entiteDoc->has('etablissement') ? $this->Html->link($entiteDoc->etablissement->code, ['controller' => 'Etablissements', 'action' => 'view', $entiteDoc->etablissement->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Adresse 1') ?></th>
            <td><?= h($entiteDoc->adresse_1) ?></td>
        </tr>
		<?php if (!empty($entiteDoc->adresse_2)) { ?>
        <tr>
            <th><?= __('Adresse 2') ?></th>
            <td><?= h($entiteDoc->adresse_2) ?></td>
        </tr>
		<?php } ?>
		<?php if (!empty($entiteDoc->adresse_3)) { ?>		
        <tr>
            <th><?= __('Adresse 3') ?></th>
            <td><?= h($entiteDoc->adresse_3) ?></td>
        </tr>
		<?php } ?>		
        <tr>
            <th><?= __('Code postal') ?></th>
            <td><?= h($entiteDoc->adresse_cp) ?></td>
        </tr>
        <tr>
            <th><?= __('Ville') ?></th>
            <td><?= h($entiteDoc->adresse_ville) ?></td>
        </tr>
        <!-- <tr>
            <th><?= __('Adresse Pays') ?></th>
            <td><?= h($entiteDoc->adresse_pays) ?></td>
        </tr> -->
        <tr>
            <th><?= __('Tél.') ?></th>
            <td><?= h($entiteDoc->num_tel) ?></td>
        </tr>
        <tr>
            <th><?= __('Courriel') ?></th>
            <td><?= h($entiteDoc->mail) ?></td>
        </tr>

        <!-- <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($entiteDoc->id) ?></td>
        </tr> -->
    </table>
	<h4><?= __('Référent(e) archives') ?></h4>
    <div class="related">

        <?php if (!empty($entiteDoc->users)) { ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Nom') ?></th>
                <th><?= __('Prénom') ?></th>
                <th><?= __('Tél.') ?></th>				
                <th><?= __('Courriel') ?></th>

                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($entiteDoc->users as $users): ?>
            <tr>
                <td><?= h($users->nom) ?></td>
                <td><?= h($users->prenom) ?></td>
				<td><?= h($users->num_tel) ?></td>
                <td><?= h($users->mail) ?></td>

                <td class="actions">
                    <?= $this->Html->link(__('Consulter'), ['controller' => 'Users', 'action' => 'view', $users->id]) ?>
					
					<?php 
					
						if ( ($typeUserEnSession == PROFIL_CC) || ( ($typeUserEnSession == PROFIL_CA) && ($idUserEnSession == $users['id']) ) ){ ?>
					
						<?= $this->Html->link(__('Modifier'), ['controller' => 'Users', 'action' => 'edit', $users->id]) ?>
					
					<?php } ?>

                </td>
            </tr>
            <?php endforeach; ?>
        </table>
		<?php 
		}
		else {?>
			<p>Aucun(e) référent(e) archives déclaré(e) pour cette entité documentaire.</p>
		<?php } ?>
    </div>	
	<br>	
	<h4><?= __('Fonds') ?></h4>
    <div class="related">
        <?php if (!empty($entiteDoc->fonds)) { ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Nom') ?></th>
                <th><?= __('Type de fonds') ?></th>				
                <th><?= __('Vol. ml') ?></th>
                <th><?= __('Vol. Go') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php 
			$totalMl = 0.0;
			$totalGo = 0.0;
			foreach ($entiteDoc->fonds as $fonds): 
			
				// Calcul des sommes de volumétrie qu'on affichera après la boucle.
				$totalMl = $totalMl + (float)$fonds->nb_ml;
				$totalGo = $totalGo + (float)$fonds->nb_go;
			?>
            <tr>
		<td>
                   <?php $dateAffichee = '' ;
                   empty($fonds->dt_der_modif) ? $dateAffichee = $fonds->dt_creation->nice('Europe/Paris', 'fr-FR') : $dateAffichee = $fonds->dt_der_modif->nice('Europe/Paris', 'fr-FR') ; 
                   ?>
                  <?= h($fonds->nom) ?><?= $fonds->ind_maj ? '&nbsp;&#x2714; (' .  $dateAffichee . ')' : '' ?>
                </td>
                <td>
                   <?= h($fonds->type_fond->type) ?>
                </td>
                <td><?= $fonds->ind_nb_ml_inconnu ? h('inconnue') : $this->Number->format($fonds->nb_ml) ?></td>
                <td><?= $fonds->ind_nb_go_inconnu ? h('inconnue') : $this->Number->format($fonds->nb_go) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('Consulter'), ['controller' => 'Fonds', 'action' => 'view', $fonds->id]) ?>
					<?php 
						// Celui qui consulte peut modifier uniquement s'il est Campus Condorcet ou s'il est Chargé d'archives pour l'entité documentaire dont dépend le fonds
						if ( ($typeUserEnSession == PROFIL_CC) || ( ($typeUserEnSession == PROFIL_CA) && ($idEntiteDocEnSession == $entiteDoc['id']) ) ){ ?>
						<?= $this->Html->link(__('Modifier'), ['controller' => 'Fonds', 'action' => 'edit', $fonds->id]) ?>
					<?php } ?>
                </td>
            </tr>
            <?php endforeach; ?>
			<tr>
				<td>&nbsp;</td>
				<td class="right">Total : </td>
				<td><?php echo($totalMl); ?></td>
				<td><?php echo($totalGo); ?></td>
			</tr>
        </table>
		<?php 
		}
		else {?>
			<p>Aucun fonds déclaré pour cette entité documentaire.</p>
		<?php } ?>
    </div>
	<h4><?= __('Lieu(x) de conservation') ?></h4>
    <div class="related">

        <?php if (!empty($entiteDoc->lieu_conservations)){ ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <!-- <th><?= __('Id') ?></th> -->
                <th><?= __('Nom') ?></th>
                <th><?= __('Adresse') ?></th>
                <!-- <th><?= __('Adresse 2') ?></th>
                <th><?= __('Adresse 3') ?></th> -->
                <th><?= __('Code postal') ?></th>
                <th><?= __('Ville') ?></th>
                <!-- <th><?= __('Adresse Pays') ?></th>
                <th><?= __('Entite Doc Id') ?></th> -->
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($entiteDoc->lieu_conservations as $lieuConservations): ?>
            <tr>
                <!-- <td><?= h($lieuConservations->id) ?></td> -->
                <td><?= h($lieuConservations->nom) ?></td>
                <td><?= h($lieuConservations->adresse_1) ?></td>
                <!-- <td><?= h($lieuConservations->adresse_2) ?></td>
                <td><?= h($lieuConservations->adresse_3) ?></td> -->
                <td><?= h($lieuConservations->adresse_cp) ?></td>
                <td><?= h($lieuConservations->adresse_ville) ?></td>
                <!-- <td><?= h($lieuConservations->adresse_pays) ?></td>
                <td><?= h($lieuConservations->entite_doc_id) ?></td> -->
                <td class="actions">
                    <?= $this->Html->link(__('Consulter'), ['controller' => 'LieuConservations', 'action' => 'view', $lieuConservations->id]) ?>
					<?php 
						// Celui qui consulte peut modifier uniquement s'il est Campus Condorcet ou s'il est Chargé d'archives pour l'entité documentaire dont dépend le fonds
						if ( ($typeUserEnSession == PROFIL_CC) || ( ($typeUserEnSession == PROFIL_CA) && ($idEntiteDocEnSession == $entiteDoc['id']) ) ){ ?>
							<?= $this->Html->link(__('Modifier'), ['controller' => 'LieuConservations', 'action' => 'edit', $lieuConservations->id]) ?>
					<?php } ?>						
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
		<?php 		
		}
		else {?>
			<p>Aucun lieu de conservation déclaré pour cette entité documentaire.</p>
		<?php } ?>		
    </div>
	<h4><?= __('Statistiques') ?></h4>
	<?php if ($nb_typeFonds == 0 && $nb_Aires == 0 && $nb_Thematiques == 0 && $nb_Dates == 0) {
		echo "<p>Les statistiques ne peuvent être produites que si des fonds sont renseignés." ;
	} ?>
	<table>
		<?php if ($nb_typeFonds != 0) { ?>	
		<tr>
			<td colspan=2>

				<script type="text/javascript">
					// Set a callback to run when the Google Visualization API is loaded.
					//google.load('visualization', '1.0', {'packages':['corechart']});
										
					google.setOnLoadCallback(drawChartTypeFonds);				
					function drawChartTypeFonds() {
								//var data = new google.visualization.DataTable();
								var data = google.visualization.arrayToDataTable(<?php echo ($tab_typeFonds) ; ?>) ;
								
								var options = {
									title: 'Répartition des fonds par type de fonds',
									legend: {position: 'right'}
									};
								var chart_type_fonds = new google.visualization.ColumnChart(document.getElementById('chart_type_fonds'));
								chart_type_fonds.draw(data, options);	
					}		
				</script>
				
				<center><div id="chart_type_fonds"></div></center>
			</td>
		</tr>
	
		<tr><td colspan=2>&nbsp;</td></tr>
		<?php } ?>		
		<tr>
			<td>
				<?php if ($nb_Thematiques != 0) { ?>
				<script type="text/javascript">
					// Set a callback to run when the Google Visualization API is loaded.

					google.setOnLoadCallback(drawChartThematiques);				
					function drawChartThematiques() {
								//var data = new google.visualization.DataTable();
								var data = google.visualization.arrayToDataTable(<?php echo ($tab_Thematiques) ; ?>) ;
								
								var options = {
									title: 'Répartition des fonds par disciplines (% d\'après la volumétrie en ml)',
									legend: {position: 'top', maxLines: 9},
									is3D: true,
									height: 600,
									width:500,
									colors: <?php echo ($couleurs_Thematiques) ; ?>
									
									};
								/*,pieSliceText: 'label'*/
								var chart_thematiques = new google.visualization.PieChart(document.getElementById('chart_thematiques'));
								chart_thematiques.draw(data, options);	
								
					}		
				</script>
				<center><div id="chart_thematiques"></div></center>		
				<?php } ?>
			</td>
			<td>
				<?php if ($nb_Aires != 0) { ?>
				<script type="text/javascript">
					// Set a callback to run when the Google Visualization API is loaded.
					google.setOnLoadCallback(drawChartAires);				
					function drawChartAires() {
								//var data = new google.visualization.DataTable();
								var data = google.visualization.arrayToDataTable(<?php echo ($tab_Aires) ; ?>) ;
								
								var options = {
									title: 'Répartition des fonds par aires culturelles (% d\'après la volumétrie en ml)',							
									legend: {position: 'top', maxLines: 9},
									is3D: true,
									height: 600,
									width:500,
									colors: <?php echo ($couleurs_Aires) ; ?>
									};
								var chart_aires = new google.visualization.PieChart(document.getElementById('chart_aires'));
								chart_aires.draw(data, options);	
					}		
				</script>
				<center><div id="chart_aires"></div></center>		
				<?php } ?>
			</td>
		</tr>
		<?php if ($nb_Dates != 0) { ?>		
		<tr>
			<td colspan=2>	
				<script type="text/javascript">			
					//google.charts.load('current', {'packages':['timeline']});
					google.load("visualization", "1.1", {'packages':['timeline']});
					google.setOnLoadCallback(drawChartDates);
					function drawChartDates() {
						var container = document.getElementById('chart_div_dates');
						var chart_dates = new google.visualization.Timeline(container);
						var dataDates = google.visualization.arrayToDataTable(<?php echo ($tab_Dates) ; ?>) ;
						var options = {
							height: 700,
							focusTarget: 'category',
							tooltip: {isHtml: true},
							timeline: { showRowLabels: false }
						};
						
						chart_dates.draw(dataDates, options);
						document.getElementById('png').outerHTML = '<b>Couverture chronologique des fonds de l\'entité documentaire</b> <br>Attention : les fonds sans dates extrêmes n\'apparaissent pas.';
					}
				</script>
				<align="right"><div id='png'></div></align>
				<center><div id="chart_div_dates"></div> </center>

			</td>
		</tr>
		<?php } ?>		
	</table>
	 <div class="related">
	 </div>
	<?php 
		// Celui qui consulte peut modifier uniquement s'il est Campus Condorcet ou s'il est Chargé d'archives pour l'entité documentaire dont dépend le fonds
		if ( ($typeUserEnSession == PROFIL_CC) || ( ($typeUserEnSession == PROFIL_CA) && ($idEntiteDocEnSession == $entiteDoc['id']) ) ){ ?>
		<div align="right">
		<?= $this->Html->link(__('Modifier'), ['action' => 'edit', $entiteDoc->id]) ?>
		<br><br>
		</div>	
		<?php } ?>
</div>

