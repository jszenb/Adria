<?php require_once WWW_ROOT . DS . 'php/declareVariables.php'; ?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
	<?php 
	if ($typeUserEnSession == PROFIL_CA) {
		$entiteDocLieuConservation = $lieuConservation->entite_docs[0]->id ;
	}
	echo $this->element('listeMenuActions', ['typeUserEnSession' => $typeUserEnSession]); 
	
	// L'utilisateur CC peut effacer un établissement, et aussi bien l'utilisateur CC que l'utilisateur CA peuvent le modifier
	if ($typeUserEnSession == PROFIL_CC) {
	?>
	<ul class="side-nav">
		<li><?= $this->Form->postLink(__('Supprimer le lieu de conservation'), ['action' => 'delete', $lieuConservation->id], ['confirm' => __('Voulez-vous vraiment supprimer le lieu de conservation {0} ?', $lieuConservation->nom)]) ?> </li>		
	</ul>
	<?php } ?>
</nav>

<!--
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Lieu Conservation'), ['action' => 'edit', $lieuConservation->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Lieu Conservation'), ['action' => 'delete', $lieuConservation->id], ['confirm' => __('Are you sure you want to delete # {0}?', $lieuConservation->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Lieu Conservations'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Lieu Conservation'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Entite Docs'), ['controller' => 'EntiteDocs', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Entite Doc'), ['controller' => 'EntiteDocs', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Fonds'), ['controller' => 'Fonds', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Fond'), ['controller' => 'Fonds', 'action' => 'add']) ?> </li>
    </ul>
</nav>
-->

<div class="lieuConservations form large-9 medium-8 columns content">
    <?= $this->Form->create($lieuConservation) ?>
    <fieldset>
        <legend><?= __('Modifier le lieu de conservation') ?></legend>
        <?php
            echo $this->Form->input('nom');
            echo $this->Form->input('adresse_1');
            echo $this->Form->input('adresse_2');
            echo $this->Form->input('adresse_3');
            echo $this->Form->input('adresse_cp', ['label' => 'Code postal']);
            echo $this->Form->input('adresse_ville', ['label' => 'Ville']);
            echo $this->Form->input('adresse_pays', ['label' => 'Pays']);
			if ($typeUserEnSession == PROFIL_CC) {
				// Gestion de l'entité documentaire
				// Pour le CA il n'y a qu'une valeur possible : son entité documentaire : on n'affiche rien. Mais pour le CC, on lui permet de modifier les informations
				//echo $this->Form->input('entite_docs._ids', ['options' => $entiteDocs, 'label' => 'Entité documentaire', 'required' => true]);
				echo $this->Form->input('entite_docs._ids', ['options' => $entiteDocs, 'label' => 'Entité documentaire', 'required' => true]);
            }			
			echo $this->Form->input('fonds._ids', ['options' => $fonds]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Enregistrer')) ?>
    <?= $this->Form->end() ?>
</div>
