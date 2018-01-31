<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">

	<?php echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); ?>
	
</nav>
<div class="fonds index large-9 medium-8 columns content">
    <h3><?= __('Rechercher un fonds') ?></h3>
	<div class="recherche">
		<?=	$this->Form->create(null, ['action' => 'recherche']); ?>
		<table class="recherche">
		<tr class="recherche">
			<td class="recherche"><?= $this->Form->select('critere', [1 => 'Nom du fonds', 2 => 'Entité documentaire', 3 => 'Etablissement', 4 => 'Type de fonds']) ?></td>
			<td class="recherche" width=15%><?= $this->Form->select('operande', [1 => '=', 2 => 'commence par'], ['length' => 10]) ?></td>	
			<td class="recherche"><?= $this->Form->input('valeur', ['type' => 'text', 'label' => '']) ?></td>
			<td class="recherche"><?= $this->Form->button('Rechercher', ['type' => 'submit']) ?></td>
		</tr>	
		</table>
			<?= $this->Form->end(); ?>
	</div>
    
	<?php 
	/* Préparation au comptage des volumétries et à l'affichage des résultats de volumétrie */
	
	// Volumétrie totale de tous les fonds, quel que soit le critère de recherche
	$totalMl = 0;
	$totalGo = 0;
	
	// Volumétrie totale des fonds de l'utilisateur et ramenés par la recherche
	$totalMlUser = 0;
	$totalGoUser = 0;
	
	// Volumétrie totale des fonds ramenés par la recherche et n'appartenant pas à l'utilisateur
	$totalMlNonUser = 0;
	$totalGoNonUser = 0;
	
	
	// Récupération des volumétries totales à partir de ce que le contrôleur a renvoyer. 
	// Les autres valeurs vont être calculées pendant l'affichage
	foreach ($volumetrie as $uneVolumetrie){
		$totalMl = $uneVolumetrie['sommeMl'];
		$totalGo = $uneVolumetrie['sommeGo'];
		// Il n'y a qu'une ligne dans le résultat : c'est un count
		break;
	}?>
	<!-- ******************************* Fonds de l'utilisateur connecté ******************************* -->	
    <h4>Vos fonds</h4>
	<?php 				
	// On affiche d'abord les noms de l'utilisateur en cours i.e. ceux de son entité documentaire
	// A-t-on ce genre de fonds ?
	$fondsPresent = false;
	foreach ($fonds as $fond){
		if ($fond->entite_doc->id == $idEntiteDocEnSession) {
			$fondsPresent = true;
			break;
		}						
	} 
	
	if ($fondsPresent) { ?>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>		
                <th width=40%><?= $this->Paginator->sort('nom', 'Nom') ?></th>
				<th width=10%><?= $this->Paginator->sort('Etablissements.nom', 'Etablissement') ?></th>
                <th width=10%><?= $this->Paginator->sort('EntiteDocs.nom', 'Entité documentaire') ?></th>
				<th width=15%><?= $this->Paginator->sort('type_fond_id', 'Type de fonds') ?></th>
                <th width=5%><?= $this->Paginator->sort('nb_ml', 'Vol. ml') ?></th>
                <th width=7%><?= $this->Paginator->sort('nb_go', 'Vol. Go') ?></th>				
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php 
			foreach ($fonds as $fond): 
				if ($fond->entite_doc->id == $idEntiteDocEnSession) {
					if ( ($typeUserEnSession == PROFIL_CC) || ( ($typeUserEnSession == PROFIL_CA) && (!$fond->ind_suppr) ) ): ?>
					<tr>
						<td><?= h($fond->nom) ?>
						<?php if ($fond->ind_suppr) {echo('<b>(supprimé)</b>');} ?>
						</td>
						<td><?= h($fond->entite_doc->etablissement->nom) ?></td>
						<td><?= $fond->has('entite_doc') ? $this->Html->link($fond->entite_doc->code, ['controller' => 'EntiteDocs', 'action' => 'view', $fond->entite_doc->id]) : '' ?></td>
						<td><?= $fond->has('type_fond') ? h($fond->type_fond->type) : '' ?></td>				
						<td width=7%><?= $fond->ind_nb_ml_inconnu ? h('inconnue') : $this->Number->format($fond->nb_ml) ?></td>
						<td width=7%><?= $fond->ind_nb_go_inconnu ? h('inconnue') : $this->Number->format($fond->nb_go) ?></td>							
						<?php 
							// Calcul de la volumétrie totale de l'utilisateur :
							$totalMlUser = $totalMlUser +  $this->Number->format($fond->nb_ml);
							$totalGoUser = $totalGoUser +  $this->Number->format($fond->nb_go);
						?>
						<td class="actions">
							<?= $this->Html->link(__('Consulter'), ['action' => 'view', $fond->id]) ?>
							<?php if ( ($typeUserEnSession == PROFIL_CC) || ( ($typeUserEnSession == PROFIL_CA) && ($idEntiteDocEnSession == $fond->entite_doc->id) ) ){ ?>
								<?= $this->Html->link(__('Modifier'), ['action' => 'edit', $fond->id]) ?>
							<?php } ?>
							<?php if ($typeUserEnSession == PROFIL_CC) { ?>
								<?= $this->Form->postLink(__('Supprimer'), ['action' => 'delete', $fond->id], ['confirm' => __('Voulez-vous vraiment supprimer le fonds {0} ?', $fond->nom)]) ?>
							<?php } ?>
						</td>
					</tr>
					<?php 
					endif;
				}
			endforeach; ?>
			<!-- Volumétrie totale -->
			<tr>
				<td/>
				<td/>
				<td/>
				<td class="right">Total :</td>
				<td><?php echo($totalMlUser . " / " .$totalMl) ?></td>
				<td><?php echo($totalGoUser . " / " .$totalGo) ?></td>
				<td/>
			</tr>
        </tbody>
    </table>
	<br>
	<?php } 
	else {
		echo("Pas de fonds correspondant à ce critère.<br><br>");
	}?>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('précédent')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('suivant') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>	

	<!-- ******************************* Fonds des autres utilisateurs ******************************* -->	
	<h4>Fonds des autres entités documentaires</h4>
	<?php 				
	// On affiche d'abord les noms de l'utilisateur en cours i.e. ceux de son entité documentaire
	// A-t-on ce genre de fonds ?
	$fondsPresent = false;
	foreach ($fonds as $fond){
		if ($fond->entite_doc->id != $idEntiteDocEnSession) {
			$fondsPresent = true;
			break;
		}						
	} 
	
	if ($fondsPresent) { ?>	
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>		
                <th width=40%><?= $this->Paginator->sort('nom', 'Nom') ?></th>
				<th width=10%><?= $this->Paginator->sort('Etablissements.nom', 'Etablissement') ?></th>
                <th width=10%><?= $this->Paginator->sort('EntiteDocs.nom', 'Entité documentaire') ?></th>
				<th width=15%><?= $this->Paginator->sort('type_fond_id', 'Type de fonds') ?></th>
                <th width=5%><?= $this->Paginator->sort('nb_ml', 'Vol. ml') ?></th>
                <th width=7%><?= $this->Paginator->sort('nb_go', 'Vol. Go') ?></th>				
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
		
        <tbody>
			
            <?php 
				// On affiche d'abord les noms de l'utilisateur en cours i.e. ceux de son entité documentaire
				foreach ($fonds as $fond): 
				if ($fond->entite_doc->id != $idEntiteDocEnSession) {
					if ( ($typeUserEnSession == PROFIL_CC) || ( ($typeUserEnSession == PROFIL_CA) && (!$fond->ind_suppr) ) ): ?>
					<tr>
						<td><?= h($fond->nom) ?>
						<?php if ($fond->ind_suppr) {echo('<b>(supprimé)</b>');} ?>
						</td>
						<td><?= h($fond->entite_doc->etablissement->nom) ?></td>
						<td><?= $fond->has('entite_doc') ? $this->Html->link($fond->entite_doc->code, ['controller' => 'EntiteDocs', 'action' => 'view', $fond->entite_doc->id]) : '' ?></td>
						<td><?= $fond->has('type_fond') ? h($fond->type_fond->type) : '' ?></td>				
						<td width=7%><?= $fond->ind_nb_ml_inconnu ? h('inconnue') : $this->Number->format($fond->nb_ml) ?></td>
						<td width=7%><?= $fond->ind_nb_go_inconnu ? h('inconnue') : $this->Number->format($fond->nb_go) ?></td>		
						<?php 
							// Calcul de la volumétrie totale de l'utilisateur :
							$totalMlNonUser = $totalMlNonUser +  $this->Number->format($fond->nb_ml);
							$totalGoNonUserUser = $totalGoNonUser +  $this->Number->format($fond->nb_go);
						?>
						<td class="actions">
							<?= $this->Html->link(__('Consulter'), ['action' => 'view', $fond->id]) ?>
							<?php if ( ($typeUserEnSession == PROFIL_CC) || ( ($typeUserEnSession == PROFIL_CA) && ($idEntiteDocEnSession == $fond->entite_doc->id) ) ){ ?>
								<?= $this->Html->link(__('Modifier'), ['action' => 'edit', $fond->id]) ?>
							<?php } ?>
							<?php if ($typeUserEnSession == PROFIL_CC) { ?>
								<?= $this->Form->postLink(__('Supprimer'), ['action' => 'delete', $fond->id], ['confirm' => __('Voulez-vous vraiment supprimer le fonds {0} ?', $fond->nom)]) ?>
							<?php } ?>
						</td>
					</tr>
					<?php 
					endif;
				}
			endforeach; ?>
			<!-- Volumétrie totale -->
			<tr>
				<td/>
				<td/>
				<td/>
				<td class="right">Total :</td>
				<td><?php echo($totalMlNonUser . " / " .$totalMl) ?></td>
				<td><?php echo($totalGoNonUser . " / " .$totalGo) ?></td>
				<td/>
			</tr>			
        </tbody>
    </table>
	<?php } 
	else {
		echo("Pas de fonds correspondant à ce critère.<br><br>");
	}?>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('précédent')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('suivant') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>
</div>
